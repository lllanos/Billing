<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Log;
use Response;
use Storage;
use View;

use Yacyreta\Mes;
use Indice\Clasificacion;
use Indice\Fuente;
use Indice\IndiceTabla1;
use Indice\PublicacionIndice;
use Indice\PeriodoIndiceTabla1;
use Indice\ValorIndice;
use Indice\ValorIndicePublicado;
use Yacyreta\Moneda;

class PublicacionesController extends Controller
{

    public function __construct()
    {
        View::share('ayuda', 'indices');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $search_input = '';
        $publicaciones = PublicacionIndice::orderBy('anio', 'desc')->orderBy('mes', 'desc')->get()
            ->filter(function ($publicacion) {
                return $publicacion->publicado;
            });


        if ($request->getMethod() != "GET") {
            $search_input = $input['search_input'];
            $input_lower = $this->minusculaSinAcentos($input['search_input']);

            if ($input_lower != '') {
                $publicaciones = $publicaciones->filter(function ($publicacion) use ($input_lower) {

                    return
                        substr_count($this->minusculaSinAcentos($publicacion->mes_anio), $input_lower) > 0 ||
                        substr_count($this->minusculaSinAcentos($publicacion->fecha_publicacion), $input_lower) > 0;
                });
            }
        }

        $publicaciones = $this->paginateCustom($publicaciones);

        $mes_anio = PublicacionIndice::last()->mes_anio_siguientes;
        $prefix = '';
        if ($mes_anio['mes_siguiente'] < 10)
            $prefix = '0';
        $mes_anio = $prefix . $mes_anio['mes_siguiente'] . '/' . $mes_anio['anio_siguiente'];

        $monedas = $publicaciones->filter(function ($value, $key) {
            return $value->moneda != null;
        })->map(function ($value, $key) {
            return $value->moneda;
        })->unique();

        return view('publicaciones.index', compact('publicaciones', 'mes_anio', 'search_input', 'monedas'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function filtrarPorMoneda(Request $request, $id)
    {
        $input = $request->all();
        $search_input = '';
        $publicaciones = PublicacionIndice::whereMonedaId($id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get()
            ->filter(function ($publicacion) {
                return $publicacion->publicado;
            });


        if ($request->getMethod() != "GET") {
            $search_input = $input['search_input'];
            $input_lower = $this->minusculaSinAcentos($input['search_input']);

            if ($input_lower != '') {
                $publicaciones = $publicaciones->filter(function ($publicacion) use ($input_lower) {

                    return
                        substr_count($this->minusculaSinAcentos($publicacion->mes_anio), $input_lower) > 0 ||
                        substr_count($this->minusculaSinAcentos($publicacion->fecha_publicacion), $input_lower) > 0;
                });
            }
        }

        $publicaciones = $this->paginateCustom($publicaciones);

        $mes_anio = PublicacionIndice::last()->mes_anio_siguientes;
        $prefix = '';
        if ($mes_anio['mes_siguiente'] < 10)
            $prefix = '0';
        $mes_anio = $prefix . $mes_anio['mes_siguiente'] . '/' . $mes_anio['anio_siguiente'];

        $monedas = Moneda::all();

        return view('publicaciones.index', compact('publicaciones', 'mes_anio', 'search_input', 'monedas'));
    }

    public function reporteIndices()
    {
        $monedas = Moneda::all();
        $moneda = Moneda::whereSimbolo('ARS')->first();
        if ($moneda == null) {
            $moneda = Moneda::first();
        }

        $moneda_id = $moneda->id;

        $anios_eloquent = PublicacionIndice::wherePublicado(1)
            ->distinct('anio')
            ->orderBy('anio', 'desc')->get();
        $anios = array();
        foreach ($anios_eloquent as $keyAnio => $valueAnio) {
            $anios[$valueAnio->anio] = $valueAnio->anio;
        }

        $selected_anio = max($anios);
        $data = $this->getHtmlTablareporteIndices($selected_anio, $moneda_id)->getData();
        $html_tabla = $data->view;


        return view(
            'publicaciones.reportes.index',
            compact('anios', 'html_tabla', 'selected_anio', 'monedas', 'moneda_id', 'moneda')
        );
    }

    public function getHtmlTablareporteIndices($anio, $moneda_id)
    {
        $publicacion_enero = PublicacionIndice::whereAnio($anio)
            ->whereMonedaId($moneda_id)
            ->orderBy('mes')
            ->first()
            ->id;

        $publicacion_ultimo_mes = PublicacionIndice::whereAnio($anio)
            ->whereMonedaId($moneda_id)
            ->orderBy('mes', 'desc')
            ->first()
            ->id;

        $indices = IndiceTabla1::whereMonedaId($moneda_id)
            ->get()
            ->filter(function ($indice) use ($publicacion_enero, $publicacion_ultimo_mes) {
                return $indice->periodo_actual != null &&
                    ($indice->periodo_actual->publicacion_inicio_id <= $publicacion_ultimo_mes) &&
                    ($indice->periodo_actual->publicacion_fin_id == null  ||
                        $indice->periodo_actual->publicacion_fin_id > $publicacion_enero);
            })
            ->sortBy(function ($valor, $key) {
                return $valor->nro;
            }, SORT_NATURAL, false);

        $ids_publicaciones_scalar = PublicacionIndice::wherePublicado(1)
            ->whereMonedaId($moneda_id)
            ->whereAnio($anio)
            ->orderBy('mes')
            ->pluck('mes', 'id')->toArray();

        $ids_publicaciones_estado = PublicacionIndice::whereAnio($anio)
            ->whereMonedaId($moneda_id)
            ->orderBy('mes')
            ->pluck('publicado', 'id')->toArray();

        $ids_publicaciones_scalar2 = array();
        foreach ($ids_publicaciones_scalar as $keyScalar => $valueScalar) {
            $ids_publicaciones_scalar2[$keyScalar] = $keyScalar;
        }

        foreach ($indices as $keyIndice => $valueIndice) {
            $valores_eloquent = ValorIndicePublicado::whereTablaIndicesId($valueIndice->id)
                ->whereIn('publicacion_id', $ids_publicaciones_scalar2)
                ->get();

            $valores = array();
            foreach ($valores_eloquent as $keyVal => $valueVal) {
                $valores[$valueVal->publicacion_id] = $valueVal->valor_show;
            }

            $valores_temp = array();
            foreach ($valores as $keyVal => $valueVal) {
                $key = array_search($keyVal, $ids_publicaciones_scalar2);
                $valores_temp[$key] = $valueVal;
            }

            $valueIndice->valores = $valores_temp;
        }

        $indices_categorizados = array();
        foreach ($indices as $keyValor => $valueValor) {
            if ($valueValor->se_publica) {
                $valueValor->clasificacion_id = $valueValor->clasificacion_id;
                $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
            }
        }


        $categorias_eloquent = Clasificacion::all()->sortBy('subcategoria')->sortBy('categoria')
            ->groupBy('categoria')->transform(function ($item, $k) {
                return $item->groupBy('subcategoria');
            });

        $valores_por_categoria = array();

        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = array();
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {

                if (isset($indices_categorizados[$valueSubCategoria[0]->id]))
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
            }
        }

        $ids_publicaciones = array();
        foreach ($ids_publicaciones_scalar as $key => $val) {
            $ids_publicaciones[$key] = $val;
        }

        $jsonResponse['moneda_id'] = $moneda_id;
        $jsonResponse['moneda'] = Moneda::find($moneda_id)->nombre_simbolo;
        $jsonResponse['view'] = View::make(
            'publicaciones.reportes.tabla',
            compact('ids_publicaciones', 'valores_por_categoria')
        )->render();

        return response()->json($jsonResponse);
    }

    public function fuentesIndices()
    {
        $periodo_1 = PublicacionIndice::first()->id;

        $periodos = PeriodoIndiceTabla1::orderBy('publicacion_fin_id')
            ->get()
            ->pluck('id', 'publicacion_fin_id');

        $periodos_inicio = PeriodoIndiceTabla1::orderBy('publicacion_fin_id')
            ->get()
            ->pluck('id', 'publicacion_inicio_id');

        foreach ($periodos_inicio as $keyPerInicio => $valuePerInicio) {
            $periodos[$keyPerInicio] = $periodos_inicio[$keyPerInicio];
        }

        unset($periodos[null]);
        $periodos_array = $periodos->all();
        ksort($periodos_array);

        $primera = array_reverse($periodos_array)[sizeof($periodos_array) - 1];
        foreach ($periodos_array as $key => $value) {
            if ($value == $primera && $key > 1)
                unset($periodos_array[$key]);
        }

        $periodos = collect($periodos_array);

        $i = 0;
        foreach ($periodos as $keyPer => $valuePer) {
            if (!isset($anios)) {
                $anios[$i]['show'] = PublicacionIndice::find($periodo_1)->mes_anio . ' - ' . PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i]['de'] = $periodo_1;
                $anios[$i]['a'] = $keyPer;

                $anios[$i + 1]['show']  = PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i + 1]['de']  = $keyPer;
            } else {
                $anios[$i]['show']  = $anios[$i]['show']  . ' - ' . PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i]['a']  = $keyPer;

                $anios[$i + 1]['show']  = PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i + 1]['de']  = $keyPer;
            }

            $i++;
        }
        if (!isset($keyPer))
            $keyPer = $periodo_1;

        $anios[$i]['show']  = PublicacionIndice::find($keyPer)->mes_anio . ' - ' . trans('index.actualidad');
        $anios[$i]['de']  = $keyPer;
        $anios[$i]['a']  = 'actualidad';

        foreach ($anios as $keyAnio => $valueAnio) {
            if ($valueAnio['de'] == $valueAnio['a'])
                unset($anios[$keyAnio]);
        }

        $selected_anio = $anios[sizeof($anios)];

        $monedas = Moneda::all();
        $moneda = Moneda::whereSimbolo('ARS')->first();
        if ($moneda == null) {
            $moneda = Moneda::first();
        }

        $moneda_id = $moneda->id;

        $data = $this->getHtmlTablafuentesIndices($selected_anio['de'], $selected_anio['a'], $moneda_id)->getData();
        $html_tabla = $data->view;

        $selected = sizeof($anios);
        return view('publicaciones.fuentes.index', compact('anios', 'html_tabla', 'selected', 'monedas', 'moneda_id', 'moneda'));
    }

    /**
     * @param  int $anio
     */
    public function getHtmlTablafuentesIndices($de, $a, $moneda_id)
    {
        $indices = IndiceTabla1::whereMonedaId($moneda_id)->get();

        if ($a == 'actualidad') {
            $indices = PeriodoIndiceTabla1::whereIn("tabla_indices_id", $indices)
                ->where('publicacion_inicio_id', '>=', $de)
                ->orWhere('publicacion_fin_id', '=', null)
                ->get()
                ->sortBy(function ($valor, $key) {
                    return $valor->nro;
                }, SORT_NATURAL, false);
        } else {
            $indices = PeriodoIndiceTabla1::whereIn("tabla_indices_id", $indices)
                ->where(function ($query) use ($de, $a) {
                    $query
                        ->where('publicacion_inicio_id', '<=', $de)
                        ->where('publicacion_fin_id', '=', null);
                })
                ->orWhere(function ($query) use ($de, $a) {
                    $query
                        ->where('publicacion_inicio_id', '<=', $de)
                        ->where('publicacion_fin_id', '<=', $a);
                })
                ->get()
                ->sortBy(function ($valor, $key) {
                    return $valor->nro;
                }, SORT_NATURAL, false);
        }

        $indices_categorizados = array();
        foreach ($indices as $keyValor => $valueValor) {
            $valueValor->clasificacion_id = $valueValor->clasificacion_id;
            $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
        }
        $categorias_eloquent = Clasificacion::all()->sortBy('subcategoria')->sortBy('categoria')
            ->groupBy('categoria')->transform(function ($item, $k) {
                return $item->groupBy('subcategoria');
            });

        $valores_por_categoria = array();
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = array();
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {

                if (isset($indices_categorizados[$valueSubCategoria[0]->id]))
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
            }
        }

        // $jsonResponse = View::make('publicaciones.fuentes.tabla', compact('valores_por_categoria'))->render();

        $jsonResponse['moneda_id'] = $moneda_id;
        $jsonResponse['moneda'] = Moneda::find($moneda_id)->nombre_simbolo;
        $jsonResponse['view'] = View::make('publicaciones.fuentes.tabla', compact('valores_por_categoria'))->render();

        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function exportar(Request $request)
    {
        $input = $request->all();
        $filtro = $input['excel_input'];
        $publicaciones = PublicacionIndice::orderBy('anio', 'desc')->orderBy('mes', 'desc')->get()
            ->filter(function ($publicacion) {
                return $publicacion->publicado;
            });

        $publicaciones = $publicaciones->map(function ($publicacion, $key) {
            if ($publicacion->publicado) {
                $arr[trans('index.publicado')] = trans('index.si');
                $arr['unset'] = trans('index.publicado');
            } else {
                $arr[trans('index.publicado')] = trans('index.no');
                $arr['unset'] = trans('index.no_publicado');
            }

            $arr[trans('forms.mes_indice')] = $publicacion->mes_anio;
            return $arr;
        });

        return $this->toExcel(
            trans('forms.publicaciones'),
            $this->filtrarExportacion($publicaciones, $filtro)
        );
    }
    /**
     * @param  string $de
     * @param  string $a
     */
    public function exportarFuentes($de, $a)
    {
        if ($a == 'actualidad') {
            $indices = PeriodoIndiceTabla1::where('publicacion_inicio_id', '>=', $de)
                ->orWhere('publicacion_fin_id', '=', null)->get()
                ->sortBy(function ($valor, $key) {
                    return $valor->nro;
                }, SORT_NATURAL, false);
        } else {
            $indices = PeriodoIndiceTabla1::Where(function ($query) use ($de, $a) {
                $query->where('publicacion_inicio_id', '<=', $de)
                    ->where('publicacion_fin_id', '=', null);
            })->orWhere(function ($query) use ($de, $a) {
                $query->where('publicacion_inicio_id', '<=', $de)
                    ->where('publicacion_fin_id', '<=', $a);
            })->get()
                ->sortBy(function ($valor, $key) {
                    return $valor->nro;
                }, SORT_NATURAL, false);
        }


        $indices_categorizados = array();
        foreach ($indices as $keyValor => $valueValor) {
            if (!$valueValor->no_se_publica) {
                $valueValor->clasificacion_id = $valueValor->clasificacion_id;
                $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
            }
        }

        $categorias_eloquent = Clasificacion::all()->sortBy('subcategoria')->sortBy('categoria')
            ->groupBy('categoria')->transform(function ($item, $k) {
                return $item->groupBy('subcategoria');
            });

        $valores_por_categoria = array();
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = array();
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {

                if (isset($indices_categorizados[$valueSubCategoria[0]->id]))
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
            }
        }

        $width = \PHPExcel_Cell::stringFromColumnIndex(5);

        $cortes['cat'] = array();
        $cortes['sub'] = array();
        // El primero
        $cortes['cat'][] = 1;
        $actual = 1;
        $inicio = true;
        $nros = array();
        $elprimero = true;
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = array();

            // uno propio + otro previo
            $next = 2;
            if (!$inicio)
                $actual = $actual + 2;

            if ($inicio)
                $inicio = !$inicio;

            $sub = 0;
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {
                if ($valueSubCategoria[0]->sigla_subcategoria == 'N/A') {
                    $next = $next + 2;
                    $actual = $actual + 2;
                } else {
                    $next = $next + 4;

                    $cortes['sub'][] = $actual + 2;
                    $actual = $actual + 4;
                }

                if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];

                    if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                        $inicio_nros = $actual + 1;
                        if ($elprimero) {
                            $elprimero = false;
                            $nros_count = $inicio_nros;
                        } else {
                            $nros_count = $inicio_nros;
                        }
                    } else {
                        $inicio_nros = $cortes['sub'][sizeof($cortes['sub']) - 1];
                        $nros_count = $inicio_nros + 3;
                    }

                    $next = $next + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);
                    $actual = $actual + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);

                    foreach ($indices_categorizados[$valueSubCategoria[0]->id] as $key => $val) {
                        if ($val->se_publica) {
                            $nros[$nros_count] = $val->nro;
                            $nros_count++;
                        }
                    }
                }
            }
            $cortes['cat'][] = $cortes['cat'][sizeof($cortes['cat']) - 1] + $next;
        }
        unset($cortes['cat'][sizeof($cortes['cat']) - 1]);

        $titulo = trans('index.reporte_indices_fuentes');

        Excel::create($titulo, function ($excel) use ($valores_por_categoria, $titulo, $width, $cortes, $nros) {
            $excel->sheet($titulo, function ($sheet) use ($valores_por_categoria, $width, $cortes, $nros) {
                $isExcel = true;
                $sheet->loadView('publicaciones.fuentes.tabla', compact('valores_por_categoria', 'isExcel'));

                foreach ($cortes['cat'] as $key => $value) {
                    $sheet->mergeCells('A' . $value . ':' . $width . $value);
                    $sheet->cells('A' . $value . ':' . $width . $value, function ($cells) {
                        $cells->setBackground('#999999');
                    });
                }

                foreach ($cortes['sub'] as $key => $value) {
                    $sheet->mergeCells('A' . $value . ':' . $width . $value);
                    $sheet->cells('A' . $value . ':' . $width . $value, function ($cells) {
                        $cells->setBackground('#f2f2f2');
                    });
                }
                $sheet->setOrientation('landscape');

                // Para que tome string y no transforme a float (cambiando punto por coma)
                foreach ($nros as $key => $value) {
                    $sheet->setValueOfCell('' . $value, 'A', $key);
                }
            });
        })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
            'href' => '/excel/exports/' . $titulo . '.xlsx',
        ));
    }

    public function exportarIndices($anio, $moneda_id)
    {
        $moneda = Moneda::find($moneda_id);

        $publicacion_enero = PublicacionIndice::whereAnio($anio)
            ->orderBy('mes')->first()->id;

        $publicacion_ultimo_mes = PublicacionIndice::whereAnio($anio)
            ->orderBy('mes', 'desc')->first()->id;

        $indices = IndiceTabla1::get()->filter(function ($indice) use ($publicacion_enero, $publicacion_ultimo_mes) {
            return $indice->periodo_actual != null &&
                ($indice->periodo_actual->publicacion_inicio_id <= $publicacion_ultimo_mes) &&
                ($indice->periodo_actual->publicacion_fin_id == null  ||
                    $indice->periodo_actual->publicacion_fin_id > $publicacion_enero);
        })->sortBy(function ($valor, $key) {
            return $valor->nro;
        }, SORT_NATURAL, false);

        $ids_publicaciones_scalar = PublicacionIndice::wherePublicado(1)->whereMonedaId($moneda_id)->whereAnio($anio)
            ->orderBy('mes')
            ->pluck('mes', 'id')->toArray();

        $ids_publicaciones_estado = PublicacionIndice::whereAnio($anio)->whereMonedaId($moneda_id)
            ->orderBy('mes')
            ->pluck('publicado', 'id')->toArray();

        $ids_publicaciones_scalar2 = array();
        foreach ($ids_publicaciones_scalar as $keyScalar => $valueScalar) {
            $ids_publicaciones_scalar2[$keyScalar] = $keyScalar;
        }

        foreach ($indices as $keyIndice => $valueIndice) {
            $valores_eloquent = ValorIndicePublicado::whereTablaIndicesId($valueIndice->id)
                ->whereIn('publicacion_id', $ids_publicaciones_scalar2)
                ->get();

            $valores = array();
            foreach ($valores_eloquent as $keyVal => $valueVal) {
                $valores[$valueVal->publicacion_id] = $valueVal->valor_show;
            }

            $valores_temp = array();
            foreach ($valores as $keyVal => $valueVal) {
                $key = array_search($keyVal, $ids_publicaciones_scalar2);
                $valores_temp[$key] = $valueVal;
            }

            $valueIndice->valores = $valores_temp;
        }

        $indices_categorizados = array();
        foreach ($indices as $keyValor => $valueValor) {
            if ($valueValor->se_publica) {
                $valueValor->clasificacion_id = $valueValor->clasificacion_id;
                $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
            }
        }

        $categorias_eloquent = Clasificacion::all()->sortBy('subcategoria')->sortBy('categoria')
            ->groupBy('categoria')->transform(function ($item, $k) {
                return $item->groupBy('subcategoria');
            });

        $valores_por_categoria = array();

        $cortes['cat'] = array();
        $cortes['sub'] = array();
        // El primero
        $cortes['cat'][] = 1;
        $actual = 1;
        $inicio = true;
        $nros = array();
        $elprimero = true;
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = array();

            // uno propio + otro previo
            $next = 2;
            if (!$inicio)
                $actual = $actual + 2;

            if ($inicio)
                $inicio = !$inicio;

            $sub = 0;
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {
                if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                    if ($valueSubCategoria[0]->sigla_subcategoria == 'N/A') {
                        $next = $next + 2;
                        $actual = $actual + 2;
                    } else {
                        $next = $next + 4;

                        $cortes['sub'][] = $actual + 2;
                        $actual = $actual + 4;
                    }
                }


                if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
                    if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                        $inicio_nros = $actual + 1;
                        if ($elprimero) {
                            $elprimero = false;
                            $nros_count = $inicio_nros;
                        } else {
                            $nros_count = $inicio_nros;
                        }
                    } else {
                        $inicio_nros = $cortes['sub'][sizeof($cortes['sub']) - 1];
                        $nros_count = $inicio_nros + 3;
                    }

                    $next = $next + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);
                    $actual = $actual + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);

                    foreach ($indices_categorizados[$valueSubCategoria[0]->id] as $key => $val) {
                        $nros[$nros_count] = $val->nro;
                        $nros_count++;
                    }
                }
            }
            $cortes['cat'][] = $cortes['cat'][sizeof($cortes['cat']) - 1] + $next;
        }
        unset($cortes['cat'][sizeof($cortes['cat']) - 1]);

        $ids_publicaciones = array();
        foreach ($ids_publicaciones_scalar as $key => $val) {
            $ids_publicaciones[$key] = $val;
        }

        $cant_meses = sizeof($ids_publicaciones);
        // Columnas de meses + # + nombre - 1 porque la cuenta arranca en 1
        $width = \PHPExcel_Cell::stringFromColumnIndex($cant_meses + 2 - 1);

        $titulo = trans('index.reporte_indices_valores') . ' ' . $moneda->nombre_simbolo;
        Excel::create($titulo . '_' . $anio, function ($excel) use ($titulo, $valores_por_categoria, $ids_publicaciones, $width, $cant_meses, $cortes, $nros) {
            $excel->sheet($titulo, function ($sheet) use ($valores_por_categoria, $ids_publicaciones, $width, $cant_meses, $cortes, $nros) {
                $isExcel = true;
                $sheet->loadView('publicaciones.reportes.tabla', compact('ids_publicaciones', 'valores_por_categoria', 'isExcel'));

                for ($i = 0; $i <= ($cant_meses + 1); $i++) {
                    $cell_valor = \PHPExcel_Cell::stringFromColumnIndex($i);
                    if ($i == 0) {
                        // A
                        $sheet->setWidth($cell_valor, 8);
                        $sheet->getStyle($cell_valor)
                            ->getAlignment()
                            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    } else if ($i == 1) {
                        // B: nombre
                        $sheet->setWidth($cell_valor, 50);
                    } else {
                        $sheet->setWidth($cell_valor, 10);
                        $sheet->getStyle($cell_valor)
                            ->getAlignment()
                            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    }
                }

                foreach ($cortes['cat'] as $key => $value) {
                    $sheet->mergeCells('A' . $value . ':' . $width . $value);
                    $sheet->cells('A' . $value . ':' . $width . $value, function ($cells) {
                        $cells->setBackground('#999999');
                    });
                }

                foreach ($cortes['sub'] as $key => $value) {
                    $sheet->mergeCells('A' . $value . ':' . $width . $value);
                    $sheet->cells('A' . $value . ':' . $width . $value, function ($cells) {
                        $cells->setBackground('#f2f2f2');
                    });
                }

                // Para que tome string y no transforme a float (cambiando punto por coma)
                foreach ($nros as $key => $value) {
                    $sheet->setValueOfCell('' . $value, 'A', $key);
                }

                $sheet->setOrientation('landscape');
            });
        })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
            'href' => '/excel/exports/' . $titulo . '_' . $anio . '.xlsx',
        ));
    }
}
