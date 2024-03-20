<?php

namespace App\Http\Controllers\Contratos;

use Contratista\Contratista;
use Contratista\ContratistaUte;
use Contrato\Contrato;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Itemizado\CategoriaItem;
use Itemizado\Item;
use Itemizado\Itemizado;
use Itemizado\TipoItem;
use Itemizado\UnidadMedida;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;
use Response;
use Storage;
use View;

class ItemizadoController extends ContratosControllerExtended
{

    protected $last_level;
    protected $porcentaje_redondeo;

    public function __construct()
    {
        $this->last_level = 3;
        $this->porcentaje_redondeo = config('custom.delta');

        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $item_id
     */
    public function getItem(Request $request, $item_id)
    {
        $response['item'] = Item::findFormatted($item_id);
        $response['last_level'] = $this->last_level;  // $response['item']->max_level
        $itemizado_id = $response['item']->itemizado_id;

        $response['view'] = $this->getViews($itemizado_id, 'edit', $item_id)->render();
        $response['opciones']['tipo_ultimo_nodo'] = TipoItem::whereNombre('ultimo_nodo')->first()->id;
        $response['opciones']['categoria_ajuste'] = CategoriaItem::whereNombre('Ajuste Alzado')->first()->id;

        return response()->json($response);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $item_id
     */
    public function deleteItem(Request $request, $item_id)
    {
        $itemItemizado = Item::findOrFail($item_id);
        $jsonResponse = array();

        if (sizeof($itemItemizado->child) > 0 || $itemItemizado->certificado) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = true;

            if (sizeof($itemItemizado->child) == 0) {
                $jsonResponse['message'] = [trans('itemizado.mensajes.error_eliminar_hijos')];
            }
            else {
                $jsonResponse['message'] = [trans('itemizado.mensajes.error_eliminar_certificado')];
            }

            return response()->json($jsonResponse);
        }

        $itemizado = $itemItemizado->itemizado;
        $itemItemizado->delete();

        // if($itemItemizado->padre_id != null)
        $ok = $this->regenerarCodigos($itemizado->id);

        $ok = $this->calcularTotal($itemizado->id);

        $contrato_id = $itemizado->contrato_id;

        $jsonResponse['status'] = true;

        $jsonResponse['container'] = '#itemizado_container';
        $jsonResponse['html'] = app('ContratosController')->getViews($contrato_id, 'itemizado')->getData()->view;

        $jsonResponse['message'] = [trans('itemizado.mensajes.item_eliminado')];
        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $accion
     * @param  int  $contrato_id
     */
    public function updateOrStore(Request $request, $contrato_id)
    {
        $user = Auth::user();

        if ($user->cant('itemizado-edit')) {
            Log::error(trans('index.error403'), [
              'User' => $user,
              'Intenta' => 'itemizado-edit'
            ]);

            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;

            return response()->json($jsonResponse);
        }

        if ($request->get('itemizado_accion') == 'clone')
            $jsonResponse = $this->clone_itemizado($request, $contrato_id);
        else
            $jsonResponse = $this->update_itemizado($request, $contrato_id);

        $jsonResponse = $jsonResponse->getData();

        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contrato_id
     */
    public function update_itemizado(Request $request, $contrato_id)
    {
        $input = $request->all();
        $itemizado = Itemizado::find($input['itemizado_id']);

        if ($input['itemizado_item_id']) {
            $item = Item::find($input['itemizado_item_id']);
            $puede_editar_valores = $item->puede_editar_valores;
        }
        else {
            $puede_editar_valores = true;
        }

        if (!$itemizado->borrador) {
            $rules = array(
              'itemizado_item_nombre' => $this->required255(),
            );

            $validator = Validator::make($input, $rules);
            $errores = array();

            if ($validator->fails() || sizeof($errores) > 0) {
                $jsonResponse['status'] = false;
                $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
                Session::flash('error', trans('mensajes.error.revisar'));
                $jsonResponse['message'] = [trans('mensajes.error.revisar')];
                return response()->json($jsonResponse);
            }

            $item->descripcion = $input['itemizado_item_nombre'];
            $item->item = $input['itemizado_item_item'];
            $item->save();

            $jsonResponse['status'] = true;
            $jsonResponse['borrador'] = 0;
            $jsonResponse['message'] = [trans('itemizado.mensajes.item_editado')];
            $jsonResponse['close_modal'] = '#itemizadoAddModal';
            $jsonResponse['container'] = '#itemizado_container';
            $jsonResponse['html'] = app('ContratosController')->getViews($contrato_id, 'itemizado')->getData()->view;
            return response()->json($jsonResponse);
        }

        if ($puede_editar_valores) {
            $transform = ['itemizado_item_importe_unitario', 'itemizado_item_cantidad', 'itemizado_item_importe_total'];
        }
        else {
            $transform = ['itemizado_item_cantidad'];
        }

        foreach ($transform as $keyInput => $valueInput) {
            $input[$valueInput] = str_replace(".", "", $input[$valueInput]);
            $input[$valueInput] = str_replace(",", ".", $input[$valueInput]);
        }

        $rules = array(
          'itemizado_item_nombre' => $this->required255(),
        );

        $validator = Validator::make($input, $rules);
        $errores = array();

        if ($puede_editar_valores) {
            $error = $this->validarTamanio($input, 'itemizado_item_cantidad');
            if (sizeof($error) > 0) {
                $errores = array_merge($errores, $error);
            }

            $error = $this->validarTamanio($input, 'itemizado_item_importe_unitario');
            if (sizeof($error) > 0) {
                $errores = array_merge($errores, $error);
            }


            $error = $this->validarTamanio($input, 'itemizado_item_importe_total');
            if (sizeof($error) > 0) {
                $errores = array_merge($errores, $error);
            }
        }

        if ($validator->fails() || sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
        }

        $errores = array();
        $tipo_agrupador_id = TipoItem::whereNombre('agrupador')->first()->id;
        $tipo_ultimo_nodo_id = TipoItem::whereNombre('ultimo_nodo')->first()->id;

        if ($puede_editar_valores) {
            if ($input['itemizado_item_categoria_id'] == 'ajuste_alzado') {
                $input['itemizado_item_categoria_id'] = CategoriaItem::whereNombre('Ajuste Alzado')->first()->id;
                $es_ajuste_alzado = true;
            }
            else {
                $input['itemizado_item_categoria_id'] = CategoriaItem::whereNombre('Unidad de Medida')->first()->id;
                $es_ajuste_alzado = false;
            }
        }
        else {
            $input['itemizado_item_categoria_id'] = $item->categoria_id;
            $es_ajuste_alzado = $item->is_ajuste_alzado;
        }

        if ($input['itemizado_accion'] == 'add') {
            if ($input['itemizado_padre_id'] == 0) {
                $order = Item::whereItemizadoId($input['itemizado_id'])->where('padre_id', '=', null)->max('order');

                if ($input['itemizado_tipo_id'] == $tipo_agrupador_id) {
                    $item = new Item();
                    $item->itemizado_id = $input['itemizado_id'];
                    $item->padre_id = null;
                    $item->tipo_id = $tipo_agrupador_id;
                    $item->codigo = '9999';
                    $item->descripcion = $input['itemizado_item_nombre'];
                    $item->item = $input['itemizado_item_item'];
                    $item->nivel = 1;
                    $item->order = $order + 1;
                    $item->save();
                }
                else {
                    $item = new Item();
                    $item->itemizado_id = $input['itemizado_id'];
                    $item->padre_id = null;
                    $item->tipo_id = $input['itemizado_tipo_id'];
                    $item->codigo = '9999';
                    $item->descripcion = $input['itemizado_item_nombre'];
                    $item->item = $input['itemizado_item_item'];
                    $item->nivel = 1;
                    $item->order = $order + 1;
                    $item->categoria_id = $input['itemizado_item_categoria_id'];
                    if ($es_ajuste_alzado) {
                        $item->cantidad = 1;
                        $item->monto_unitario = $input['itemizado_item_importe_total'];
                        $item->monto_total = $input['itemizado_item_importe_total'];
                    }
                    else {
                        $item->unidad_medida_id = $input['itemizado_item_unidad_medida'];
                        $item->cantidad = $input['itemizado_item_cantidad'];
                        $item->monto_unitario = $input['itemizado_item_importe_unitario'];
                        $item->monto_total = $input['itemizado_item_importe_unitario'] * $input['itemizado_item_cantidad'];
                    }
                    $item->responsable_id = $input['itemizado_item_responsable'];
                    $item->save();
                }
            }
            else {
                $order = Item::whereItemizadoId($input['itemizado_id'])->where('padre_id', '=',
                  $input['itemizado_item_id'])->max('order');

                if ($input['itemizado_tipo_id'] == $tipo_agrupador_id) {
                    $item = new Item();
                    $item->itemizado_id = $input['itemizado_id'];
                    $item->padre_id = $input['itemizado_item_id'];
                    $item->tipo_id = $input['itemizado_tipo_id'];
                    $item->codigo = '9999';
                    $item->descripcion = $input['itemizado_item_nombre'];
                    $item->item = $input['itemizado_item_item'];
                    $item->nivel = $input['itemizado_nivel'] + 1;
                    $item->order = $order + 1;
                    $item->save();
                }
                else {
                    $item = new Item();
                    $item->itemizado_id = $input['itemizado_id'];
                    $item->padre_id = $input['itemizado_item_id'];
                    $item->tipo_id = $input['itemizado_tipo_id'];
                    $item->codigo = '9999';
                    $item->descripcion = $input['itemizado_item_nombre'];
                    $item->item = $input['itemizado_item_item'];
                    $item->nivel = $input['itemizado_nivel'] + 1;
                    $item->order = $order + 1;
                    $item->categoria_id = $input['itemizado_item_categoria_id'];

                    if ($es_ajuste_alzado) {
                        $item->cantidad = 1;
                        $item->monto_unitario = $input['itemizado_item_importe_total'];
                        $item->monto_total = $input['itemizado_item_importe_total'];
                    }
                    else {
                        $item->unidad_medida_id = $input['itemizado_item_unidad_medida'];
                        $item->cantidad = $input['itemizado_item_cantidad'];
                        $item->monto_unitario = $input['itemizado_item_importe_unitario'];
                        $item->monto_total = $input['itemizado_item_importe_unitario'] * $input['itemizado_item_cantidad'];
                    }

                    $item->responsable_id = $input['itemizado_item_responsable'];
                    $item->save();
                }
            }

            if ($item->padre_id != null) {
                $position = '#accordion_sub_it_'.$item->id;
            }
            else {
                $position = '#'.$item->id;
            }

            $ok = $this->calcularYRegenerar($input['itemizado_id']);

            $jsonResponse['status'] = true;
            $jsonResponse['borrador'] = 1;

            $jsonResponse['message'] = [trans('itemizado.mensajes.item_agregado')];
            $jsonResponse['position'] = $position;

            if (isset($input['agregar_otro'])) {
                $jsonResponse['agregar_otro'] = true;
                $jsonResponse['fields']['inputs'] = [
                  'itemizado_item_nombre', 'itemizado_item_item', 'itemizado_item_importe_total',
                  'itemizado_item_cantidad', 'itemizado_item_cantidad', 'itemizado_item_importe_unitario'
                ];
                $jsonResponse['modal'] = '#itemizadoAddModal';
            }
            else {
                $jsonResponse['close_modal'] = '#itemizadoAddModal';
            }

            $jsonResponse['container'] = '#itemizado_container';
            $jsonResponse['html'] = app('ContratosController')->getViews($contrato_id, 'itemizado')->getData()->view;

            return response()->json($jsonResponse);
        }
        elseif ($input['itemizado_accion'] == 'edit') {
            $item = Item::where('itemizado_id', $input['itemizado_id'])->where('id',
              $input['itemizado_item_id'])->first();

            $item->descripcion = $input['itemizado_item_nombre'];
            $item->item = $input['itemizado_item_item'];

            if ($input['itemizado_tipo_id'] == $tipo_agrupador_id) {
                $item->save();
            }
            else {
                if ($puede_editar_valores) {
                    $item->responsable_id = $input['itemizado_item_responsable'];
                }

                if ($es_ajuste_alzado) {
                    if ($puede_editar_valores) {
                        $item->categoria_id = $input['itemizado_item_categoria_id'];
                        $item->monto_total = $input['itemizado_item_importe_total'];
                        $item->cantidad = 1;
                        $item->unidad_medida_id = null;
                        $item->monto_unitario = $input['itemizado_item_importe_total'];
                        $item->save();
                    }
                }
                else {
                    $item->cantidad = $input['itemizado_item_cantidad'];

                    if ($puede_editar_valores) {
                        $item->categoria_id = $input['itemizado_item_categoria_id'];
                        $item->unidad_medida_id = $input['itemizado_item_unidad_medida'];
                        $item->monto_unitario = $input['itemizado_item_importe_unitario'];
                        $item->monto_total = $input['itemizado_item_importe_unitario'] * $input['itemizado_item_cantidad'];
                    }
                    else {
                        $item->monto_total = $item->monto_unitario * $input['itemizado_item_cantidad'];
                    }

                    $item->save();
                }
            }
            if ($item->padre_id != null) {
                $position = '#accordion_sub_it_'.$item->id;
            }
            else {
                $position = '#'.$item->id;
            }

            if ($item->padre_id != null) {
                $position = '#accordion_sub_it_'.$item->id;
            }
            else {
                $position = '#'.$item->id;
            }

            $ok = $this->calcularYRegenerar($input['itemizado_id']);

            $jsonResponse['status'] = true;
            $jsonResponse['borrador'] = 1;

            if ($input['itemizado_accion'] == 'add') {
                $jsonResponse['message'] = [trans('itemizado.mensajes.item_agregado')];
                $jsonResponse['position'] = $position;
            }
            else {
                $jsonResponse['message'] = [trans('itemizado.mensajes.item_editado')];
                $jsonResponse['position'] = $position;
            }

            $jsonResponse['close_modal'] = '#itemizadoAddModal';
            $jsonResponse['container'] = '#itemizado_container';
            $jsonResponse['html'] = app('ContratosController')->getViews($contrato_id, 'itemizado')->getData()->view;
            return response()->json($jsonResponse);
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contrato_id
     */
    public function clone_itemizado(Request $request, $contrato_id)
    {
        $input = $request->all();

        $rules = [
          'monedas' => 'required|array',
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $validator->getMessageBag()->toArray();
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];

            return response()->json($jsonResponse);
        }

        $itemizado = Itemizado::find($input['itemizado_id']);
        $item = null;

        if ($input['itemizado_item_id'])
            $item = Item::find($input['itemizado_item_id']);

        if ($item) {
            $clone = $item->duplicate($input['monedas'], $itemizado);
        }
        else {
            $items = $itemizado->items()
              ->whereNull('padre_id')
              ->orderBy('order', 'ASC')
              ->get();

            foreach ($items as $item)
                $clone = $item->duplicate($input['monedas'], $itemizado);
        }

        $jsonResponse = [];
        $jsonResponse['status'] = true;
        $jsonResponse['borrador'] = 1;
        $jsonResponse['message'] = [trans('itemizado.mensajes.item_cloned')];
        $jsonResponse['position'] = '#'.$clone->id;
        $jsonResponse['close_modal'] = '#itemizadoAddModal';
        $jsonResponse['container'] = '#itemizado_container';
        $jsonResponse['html'] = app('ContratosController')->getViews($contrato_id, 'itemizado')->getData()->view;

        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $itemizado_id
     */
    private function calcularYRegenerar($itemizado_id)
    {
        $ok = $this->regenerarCodigos($itemizado_id);
        $ok = $this->calcularTotal($itemizado_id);
        return $ok;
    }

    /**
     * @param  int  $itemizado_id
     */
    private function calcularTotal($itemizado_id)
    {
        $total = Item::whereItemizadoId($itemizado_id)->whereNivel(1)->sum('subtotal');

        $itemizado = Itemizado::find($itemizado_id);
        $itemizado->total = $total;
        $itemizado->save();
        return 'OK';
    }

    /**
     * @param  int  $itemizado_id
     */
    private function regenerarCodigos($itemizado_id)
    {
        $items = Item::orderBy('order')
          ->whereItemizadoId($itemizado_id)
          ->whereNivel(1)
          ->get();

        if (sizeof($items) == 0)
            return true;

        $ultimo_nodo_id = TipoItem::whereNombre('ultimo_nodo')->first()->id;
        $subtotal1 = 0;
        $subtotal2 = 0;
        $subtotal3 = 0;
        $subtotal4 = 0;

        foreach ($items as $key => $level1) {
            $level1->codigo = str_pad($key + 1, 4, "0", STR_PAD_LEFT);
            $level1->save();

            $subtotal1 = 0;
            foreach ($level1->child as $key2 => $level2) {
                $level2->codigo = $level1->codigo.".".str_pad($key2 + 1, 2, "0", STR_PAD_LEFT);
                $level2->save();
                foreach ($level2->child as $key3 => $level3) {
                    $level3->codigo = $level2->codigo.".".str_pad($key3 + 1, 2, "0", STR_PAD_LEFT);
                    $level3->save();
                    foreach ($level3->child as $key4 => $level4) {
                        $level4->codigo = $level3->codigo.".".str_pad($key4 + 1, 2, "0", STR_PAD_LEFT);
                        $level4->save();

                        if ($level4->tipo_id == $ultimo_nodo_id) {
                            $subtotal4 = $subtotal4 + (float) $level4->monto_total;
                        }
                        $level4->subtotal = (float) $level4->monto_total;
                        $level4->save();
                    }

                    $subtotal3 = $subtotal3 + $subtotal4;
                    if ($level3->tipo_id == $ultimo_nodo_id) {
                        $subtotal3 = $subtotal3 + (float) $level3->monto_total;
                        $subtotal = (float) $level3->monto_total;
                    }
                    else {
                        $subtotal = $subtotal4;
                    }
                    $level3->subtotal = $subtotal;
                    $level3->save();
                    $subtotal4 = 0;
                }
                $subtotal2 = $subtotal2 + $subtotal3;
                if ($level2->tipo_id == $ultimo_nodo_id) {
                    $subtotal2 = $subtotal2 + (float) $level2->monto_total;
                    $subtotal = (float) $level2->monto_total;
                }
                else {
                    $subtotal = $subtotal3;
                }
                $level2->subtotal = $subtotal;
                $level2->save();
                $subtotal3 = 0;
            }
            $subtotal1 = $subtotal1 + $subtotal2;
            if ($level1->tipo_id == $ultimo_nodo_id) {
                $subtotal1 = $subtotal1 + (float) $level1->monto_total;
                $subtotal = (float) $level1->monto_total;
            }
            else {
                $subtotal = $subtotal2;
            }
            $level1->codigo = str_pad($key + 1, 4, "0", STR_PAD_LEFT);
            $level1->subtotal = $subtotal;
            $level1->save();
            $subtotal2 = 0;
        }
        $level1->subtotal = $subtotal1;
        $level1->save();

        return true;
    }

    public function regenerarOrden(Request $request)
    {
        $firstLevel = Input::get('first');
        $secondLevel = Input::get('second');
        $thirdLevel = Input::get('third');
        $fourthLevel = Input::get('fourth');

        $items_nivel_1 = collect();
        if ($firstLevel) {
            foreach ($firstLevel as $key => $value) {
                $item = Item::where('id', $value)->first();
                $items_nivel_1->push($item);
            }

            foreach ($items_nivel_1 as $key => $level1) {
                $level1->order = $key + 1;
                $level1->codigo = str_pad($key + 1, 4, "0", STR_PAD_LEFT);
                $level1->save();

                foreach ($level1->child as $key2 => $level2) {
                    $level2->codigo = $level1->codigo.".".str_pad($key2 + 1, 2, "0", STR_PAD_LEFT);
                    $level2->save();
                    foreach ($level2->child as $key3 => $level3) {
                        $level3->codigo = $level2->codigo.".".str_pad($key3 + 1, 2, "0", STR_PAD_LEFT);
                        $level3->save();
                        foreach ($level3->child as $key4 => $level4) {
                            $level4->codigo = $level3->codigo.".".str_pad($key4 + 1, 2, "0", STR_PAD_LEFT);
                            $level4->save();
                        }
                    }
                }
            }
        }

        $items_nivel_2 = collect();
        if ($secondLevel) {
            foreach ($secondLevel as $key => $value) {
                $item = Item::where('id', $value)->first();
                $items_nivel_2->push($item);
            }

            foreach ($items_nivel_2 as $key => $level2) {
                $level1 = Item::where('id', $level2->padre_id)->first();
                $level2->codigo = $level1->codigo.".".str_pad($key + 1, 2, "0", STR_PAD_LEFT);
                $level2->save();

                foreach ($level2->child as $key3 => $level3) {
                    $level3->codigo = $level2->codigo.".".str_pad($key3 + 1, 2, "0", STR_PAD_LEFT);
                    $level3->save();
                    foreach ($level3->child as $key4 => $level4) {
                        $level4->codigo = $level3->codigo.".".str_pad($key4 + 1, 2, "0", STR_PAD_LEFT);
                        $level4->save();
                    }
                }
            }
        }

        if ($thirdLevel) {
            $items_nivel_3 = collect();
            foreach ($thirdLevel as $key => $value) {
                $item = Item::where('id', $value)->first();
                $items_nivel_3->push($item);
            }

            foreach ($items_nivel_3 as $key => $level3) {
                $level2 = Item::where('id', $level3->padre_id)->first();
                $level3->codigo = $level2->codigo.".".str_pad($key + 1, 2, "0", STR_PAD_LEFT);
                $level3->save();

                foreach ($level3->child as $key4 => $level4) {
                    $level4->codigo = $level3->codigo.".".str_pad($key4 + 1, 2, "0", STR_PAD_LEFT);
                    $level4->save();
                }
            }
        }

        $items_nivel_4 = collect();
        if ($fourthLevel) {
            foreach ($fourthLevel as $key => $value) {
                $item = Item::where('id', $value)->first();
                $items_nivel_4->push($item);
            }

            foreach ($items_nivel_4 as $key => $level4) {
                $level3 = Item::where('id', $level4->padre_id)->first();
                $level4->codigo = $level3->codigo.".".str_pad($key + 1, 2, "0", STR_PAD_LEFT);
                $level4->save();
            }
        }

        $items_nivel_1 = collect();

        if ($firstLevel) {
            foreach ($firstLevel as $key => $value) {
                $item = Item::where('id', $value)->first();
                $items_nivel_1->push($item);
            }
        }

        $jsonResponse['status'] = true;
        $jsonResponse['message'] = [trans('itemizado.mensajes.itemizado_actualizado')];
        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contrato_id
     */
    public function finalizar(Request $request, $contrato_id)
    {
        $user = Auth::user();
        $contrato = Contrato::find($contrato_id);

        if ($user->cant('itemizado-edit')) {
            Log::error(trans('index.error403'), [
              'User' => $user,
              'Intenta' => 'itemizado-edit'
            ]);
            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;

            return response()->json($jsonResponse);
        }

        $errores = [];

        foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
            $itemizado = $valueContratoMoneda->itemizado;
            $itemizado_id = $itemizado->id;

            $monto_inicial = $valueContratoMoneda->monto_inicial;
            $total = (float) $itemizado->total;
            $percent = round($monto_inicial * $this->porcentaje_redondeo);

            if (!(abs($monto_inicial - $total) <= $percent)) {
                $errores['total'][] = trans('mensajes.error.contrato_total_menor_o_igual', [
                  'moneda' => $itemizado->contrato_moneda->moneda->nombre
                ]);
            }

            $items = $itemizado->items_nivel_1->where('subtotal', 0);

            if (!(($items == null) || count($items) == 0)) {
                foreach ($items as $keyItem => $valueItem) {
                    $errores[$valueItem->id][] = trans('validation_custom.itemizado.sin_hoja', [
                      'moneda' => $itemizado->contrato_moneda->moneda->nombre,
                      'item' => $valueItem->descripcion_codigo
                    ]);
                }
            }

            $items = $itemizado->items_hoja;

            foreach ($items as $keyItem => $valueItem) {
                if ($valueItem->certificado && $valueItem->is_unidad_medida) {
                    $item_certificado_cant = $valueItem->primer_item_original
                        ->items_certificado()
                        ->get()
                        ->filter(function ($item_certificado) {
                            return !$item_certificado->certificado_padre->redeterminado;
                        })
                        ->sum('cantidad');

                    if ($valueItem->cantidad < $item_certificado_cant) {
                        $errores[$valueItem->id][] = trans('validation_custom.itemizado.menor_certificado', [
                          'moneda' => $itemizado->contrato_moneda->moneda->nombre,
                          'item' => $valueItem->descripcion_codigo,
                          'cant' => $item_certificado_cant.''.$valueItem->unidad_medida_nombre
                        ]);
                    }
                }
            }
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['message'] = [];
            $jsonResponse['error_container'] = '#errores-itemizado';

            return response()->json($jsonResponse);
        }

        $contrato->load('causante');
        $causante = $contrato->causante;

        $dobleFirma = false;
        $firmaAr = null;
        $firmaPy = null;

        if ($causante) {
            $dobleFirma = $causante->doble_firma;

            if ($dobleFirma && $causante->jefe_contrato_ar == Auth::user()->id)
                $firmaAr = Auth::user()->id;

            if ($dobleFirma && $causante->jefe_contrato_py == Auth::user()->id)
                $firmaPy = Auth::user()->id;
        }

        foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
            $itemizado = $valueContratoMoneda->itemizado;
            $itemizado->borrador = 0;

            if ($dobleFirma) {
                // Firma del lado argentino
                if ($firmaAr)
                    $itemizado->firma_ar = $firmaAr;
                else
                    $firmaAr = $itemizado->firma_ar;

                // Firma del lado paraguayo
                if ($firmaPy)
                    $itemizado->firma_py = $firmaPy;
                else
                    $firmaPy = $itemizado->firma_py;

                $dobleFirma = $itemizado->doble_firma = !$firmaAr || !$firmaPy;
            }

            $itemizado->save();

            if ($dobleFirma)
                continue;

            if (!$itemizado->has_cronograma)
                $this->createCronograma($itemizado);
            else
                $this->updatecreateCronograma($itemizado);
        }

        if (!$dobleFirma)
            $this->createInstanciaHistorial($contrato, 'itemizado', 'aprobado');
        else if ($firmaAr || $firmaPy)
            $this->createInstanciaHistorial($contrato, 'itemizado', 'firma');
        else
            $this->createInstanciaHistorial($contrato, 'itemizado', 'a_firmar');

        $jsonResponse['status'] = true;
        $jsonResponse['borrador'] = 0;
        $jsonResponse['message'] = [trans('itemizado.mensajes.itemizado_actualizado')];

        $jsonResponse['url'] = route('contratos.ver.incompleto', [
          'id' => $contrato_id,
          'accion' => 'cronograma'
        ]);

        return response()->json($jsonResponse);
    }

    /**
     * @param $contrato_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function firmar($contrato_id) {
        $user = Auth::user();
        $contrato = Contrato::find($contrato_id);

        $contrato->load(['causante', 'contratos_monedas.itemizado']);
        $causante = $contrato->causante;

        // Verificar
        if (!$causante) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = [trans('mensajes.error.doble_firma_sin_causante', [
              'name' => trans('index.contrato')
            ])];
            $jsonResponse['message'] = [];
            $jsonResponse['error_container'] = '#errores-itemizado';

            return response()->json($jsonResponse);
        }

        // Verifica que si adminte doble firma
        if (!$causante->doble_firma) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_no_admite', [
              'name' => trans('index.contrato')
            ])]);
        }

        $firmaAr = null;
        $firmaPy = null;

        if ($causante->jefe_contrato_ar == $user->id)
            $firmaAr = $user->id;

        if ($causante->jefe_contrato_py == $user->id)
            $firmaPy = $user->id;

        foreach ($contrato->contratos_monedas as $contratoMoneda) {
            $itemizado = $contratoMoneda->itemizado;

            // Firma del lado argentino
            if ($firmaAr)
                $itemizado->firma_ar = $firmaAr;
            else
                $firmaAr = $itemizado->firma_ar;

            // Firma del lado paraguayo
            if ($firmaPy)
                $itemizado->firma_py = $firmaPy;
            else
                $firmaPy = $itemizado->firma_py;

            $dobleFirma = $itemizado->doble_firma = !$firmaAr || !$firmaPy;

            $itemizado->save();

            if ($dobleFirma)
                continue;

            if (!$itemizado->has_cronograma)
                $this->createCronograma($itemizado);
            else
                $this->updatecreateCronograma($itemizado);
        }

        if (!$dobleFirma)
            $this->createInstanciaHistorial($contrato, 'itemizado', 'aprobado');
        else if ($firmaAr || $firmaPy)
            $this->createInstanciaHistorial($contrato, 'itemizado', 'firma');
        else
            $this->createInstanciaHistorial($contrato, 'itemizado', 'a_firmar');

        $jsonResponse['status'] = true;
        $jsonResponse['borrador'] = 0;
        $jsonResponse['message'] = [trans('itemizado.mensajes.itemizado_actualizado')];

        $jsonResponse['url'] = route('contratos.ver.incompleto', [
          'id' => $contrato_id,
          'accion' => ($dobleFirma) ? 'itemizado' : 'cronograma'
        ]);

        return response()->json($jsonResponse);

    }

    /**
     * @param $contrato_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrador($contrato_id) {
        $user = Auth::user();
        $contrato = Contrato::find($contrato_id);

        $contrato->load('causante');
        $causante = $contrato->causante;

        // Verificar
        if (!$causante) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = [trans('mensajes.error.doble_firma_sin_causante', [
              'name' => trans('index.contrato')
            ])];
            $jsonResponse['message'] = [];
            $jsonResponse['error_container'] = '#errores-itemizado';

            return response()->json($jsonResponse);
        }

        // Verifica que si adminte doble firma
        if (!$causante->doble_firma) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_no_admite', [
              'name' => trans('index.contrato')
            ])]);
        }

        if ($causante->jefe_contrato_ar != $user->id && $causante->jefe_contrato_py != $user->id) {
            return $this->errorJsonResponse([trans('mensajes.error.borrador_denegado', [
              'name' => trans('index.contrato')
            ])]);
        }

        foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
            $itemizado = $valueContratoMoneda->itemizado;
            $itemizado->borrador = true;
            $itemizado->firma_ar = null;
            $itemizado->firma_py = null;
            $itemizado->doble_firma = false ;
            $itemizado->save();
        }

        $jsonResponse['status'] = true;
        $jsonResponse['borrador'] = 1;
        $jsonResponse['message'] = [trans('itemizado.mensajes.itemizado_actualizado')];

        $jsonResponse['url'] = route('contratos.ver.incompleto', [
          'id' => $contrato_id,
          'accion' => 'itemizado'
        ]);

        return response()->json($jsonResponse);

    }

    public function updatecreateCronograma($itemizado) {
        $cronograma = $itemizado->cronograma;

        foreach ($cronograma->items_unidad_medida as $valueItem)
            $this->updateParents($cronograma, $valueItem->id, $cronograma->meses);

        // Recalculo porcentajes de Items de Unidad de Medida
        $items_cronograma = $cronograma->items_cronograma->groupBy('item_id');

        foreach ($items_cronograma as $keyItem => $valueItemsCollection) {
            $item = Item::find($keyItem);

            if ($item->is_unidad_medida) {
                $cantidad = $item->cantidad;

                foreach ($valueItemsCollection as $valueItemCronograma) {
                    $valueItemCronograma->porcentaje = ($valueItemCronograma->cantidad * 100) / $cantidad;
                    $valueItemCronograma->save();
                }
            }
        }
    }

    //#region Vistas ajax

    /**
     * @param  int  $id
     * @param  string  $accion
     * @param  int  $item_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getViews($id, $accion, $item_id)
    {

        $itemizado = Itemizado::find($id);
        $relations = [
          'contrato_moneda.contrato.contratista',
        ];

        if ($accion == 'clone') {
            $relations[] = 'contrato_moneda.contrato.contratos_monedas.moneda';
        }

        $itemizado->load($relations);
        $item = Item::find($item_id);

        if ($item == null) {
            $item = new Item();
        }

        $unidadesMedida = UnidadMedida::getOpciones();
        $contratoMoneda = $itemizado->contrato_moneda;
        $contrato = $contratoMoneda->contrato;

        if ($contrato->contratista->is_ute) {
            $contratistas = ContratistaUte::where('ute_id', $contrato->contratista->id)->pluck('contratista_id');
            $responsables = Contratista::whereIn('id', $contratistas)->pluck('razon_social',
                'id')->prepend(trans('forms.select.contratista'), '');
        }
        else {
            $responsables = Contratista::where('id', $contrato->contratista->id)->pluck('razon_social', 'id');
        }

        $contrato_id = $contrato->id;

        $tipo_agrupador_id = TipoItem::whereNombre('agrupador')->first()->id;

        // Monedas disponibles
        $monedas = new Collection();
        $contratoIncompleto = $contrato->incompleto;

        if ($contratoIncompleto['status'] && $contratoIncompleto['itemizado'] && Auth::user()->can('itemizado-edit')) {
            // Obtiene las monedas del contrato
            $monedas = $contrato->contratos_monedas;

            $monedas = $monedas->filter(function ($value, $key) use ($contratoMoneda) {
                return $value->moneda_id != $contratoMoneda->moneda_id;
            });
        }

        $data = compact(
          'itemizado',
          'unidadesMedida',
          'contrato_id',
          'responsables',
          'accion',
          'item',
          'tipo_agrupador_id',
          'monedas');

        return view("contratos.contratos.show.itemizado.modals.itemizadoAdd", $data);
    }

    //#endregion

    public function exportar(Request $request)
    {
        $input = $request->all();
        $contrato = Contrato::where('id', $input['excel_input'])->first();

        Excel::create(trans('index.itemizados').'_'.$contrato->numero_contrato,
          function ($excel) use ($contrato, $input) {
              foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
                  $excel->sheet($valueContratoMoneda->moneda->nombre_simbolo,
                    function ($sheet) use ($valueContratoMoneda, $input, $contrato) {

                        if ($input['version'] == 'vigente' && $contrato->has_itemizado_vigente) {
                            $itemizado = Itemizado::where('id', $valueContratoMoneda->itemizado_vigente_id)->first();
                        }
                        else {
                            $itemizado = $valueContratoMoneda->itemizado;
                        }

                        $results = Item::where('itemizado_id', $itemizado->id)->orderBy('codigo', 'ASC')->get();

                        $arr_excel = $results->map(function ($item) {
                            $arr = array();

                            if ($item->is_ajuste_alzado) {
                                $es_ajuste_alzado = true;
                            }
                            else {
                                $es_ajuste_alzado = false;
                            }

                            $arr[trans('forms.codigo')] = $item->codigo;
                            $arr[trans('forms.item')] = $item->item ? $item->item : $item->codigo;
                            $arr[trans('forms.descripcion')] = $item->descripcion;

                            if (!$es_ajuste_alzado) {
                                $arr[trans('forms.cantidad')] = $item->cantidad;
                                $arr[trans('forms.unidad_medida_um')] = $item->unidad_medida_nombre;
                            }
                            else {
                                $arr[trans('forms.cantidad')] = (float) 1;
                                $arr[trans('forms.unidad_medida_um')] = trans('forms.ajuste_alzado');
                            }

                            if ($item->is_hoja) {
                                if (!$item->monto_unitario) {
                                    $arr[trans('forms.montos')] = number_format((float) 0, 2, ',', '.');
                                }
                                else {
                                    $arr[trans('forms.montos')] = (float) $item->monto_unitario;
                                }

                                $arr[trans('forms.total')] = (float) $item->monto_total;
                            }
                            else {
                                $arr[trans('forms.montos')] = (float) $item->monto_unitario;
                                $arr[trans('forms.total')] = (float) $item->monto_total;
                            }

                            return $arr;
                        });

                        $last = count($arr_excel) + 2;
                        $sheet->fromArray($arr_excel, null, 'A1', false, true);
                        $sheet->appendRow(array(' ', ' ', ' ', ' ', trans('forms.total'), $itemizado->total));

                        foreach (range('E', 'F') as $key => $char) {
                            $sheet->setColumnFormat([$char.'1:'.$char.'9999' => '0.00']);

                            $sheet->cells($char.'1:'.$char.'9999', function ($cells) {
                                $cells->setAlignment('right');
                            });
                        }

                        $rows = 1;
                        foreach ($arr_excel as $item) {
                            $rows++;
                            if (strlen($item['Código']) == '4') {
                                $sheet->row($rows, function ($row) {
                                    $row->setBackground('#b2b2b2');;
                                });
                            }
                            elseif (strlen($item['Código']) == '7') {
                                $sheet->row($rows, function ($row) {
                                    $row->setBackground('#dddddd');;
                                });
                            }
                            elseif (strlen($item['Código']) == '10') {
                                $sheet->row($rows, function ($row) {
                                    $row->setBackground('#eeeeee');;
                                });
                            }

                        }

                        $sheet->row(1, function ($row) {
                            $row->setBackground('#808080');
                            $row->setFontColor('#ffffff');
                            $row->setFontWeight('bold');
                        });
                        $sheet->row($last, function ($row) {
                            $row->setBackground('#808080');
                            $row->setFontColor('#ffffff');
                            $row->setFontWeight('bold');
                        });
                    });
              }
          })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
          'href' => '/excel/exports/'.trans('index.itemizados').'_'.$contrato->numero_contrato.'.xlsx',
        ));
    }

}
