<?php

namespace Tests\Feature;

use AnalisisPrecios\Categoria\CategoriaModelExtended;
use AnalisisPrecios\Categoria\TipoCategoria;
use App\User;
use Carbon\Carbon;
use Contratista\Contratista;
use Contrato\Contrato;
use Contrato\EstadoContrato;
use Contrato\OrganoAprobador;
use Contrato\Plazo;
use Indice\IndiceTabla1;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Yacyreta\Causante;
use Yacyreta\Moneda;

class ContractTest extends TestCase
{

    use WithFaker;
    use WithoutMiddleware;

    protected $userMail = 'admin@admin.com';

    protected $user;

    //#region Pruebas

    /**
     * Crea un contrato
     *
     * @return void
     */
    public function testCreate()
    {
        // Elimina la validación de middleware
        $this->withoutMiddleware();

        // Get user admin
        $user = $this->getUser();

        // Genera un contrato
        $input = $this->contrat();

        // Request
        $response =  $this->actingAs($user)
            ->json('POST', '/contratos/store', $input);

        // Evalua
        $response->assertStatus(200);

        // Output
        $output = json_decode($response->getContent());

        // Evalua que se recupere el id de contrato
        $this->assertIsInt($output->id);

        // Id de contrato
        return $output->id;
    }

    /**
     * Crea un contrato borrador
     *
     * @return void
     */
    public function testCreateAwarded()
    {
        // Elimina la validación de middleware
        $this->withoutMiddleware();

        // Get user admin
        $this->getUser();

        // Genera un
        $input = $this->contrat([
            'fecha_acta_inicio' => null,
            'estado_id' => EstadoContrato::where('nombre', 'adjudicada')->first()->id,
        ]);

        // Request
        $response =  $this->actingAs($this->getUser())
            ->json('POST', '/contratos/store', $input);

        // Evalua
        $response->assertStatus(200);

        // Output
        $output = json_decode($response->getContent());

        // Evalua que se recupere el id de contrato
        $this->assertIsInt($output->id);

        // Id de contrato
        return $output->id;
    }

    /**
     * Crea un contrato con dos monedas
     *
     * @return void
     */
    public function testCreateTwoCurrecy()
    {
        // Elimina la validación de middleware
        $this->withoutMiddleware();

        // Obtiene las monedas
        $currencies = Moneda::all();
        // Toma las 2 primeras
        $input = $this->contrat([], [
          [
            'id' => $currencies->get(0)->id,
            'amount' => 1000000,
            'rate' => 60,
          ],
          [
            'id' => $currencies->get(1)->id,
            'amount' => 1000000,
            'rate' => 80,
          ],
        ]);

        // Request
        $response =  $this->actingAs($this->getUser())
          ->json('POST', '/contratos/store', $input);

        // Evalua respuesta
        $response->assertStatus(200);

        // Output
        $output = json_decode($response->getContent());

        // Evalua que se recupere el id de contrato
        $this->assertIsInt($output->id);

        // Id de contrato
        return $output->id;
    }

    /**
     * Crea Itemizado y lo guarda
     * @depends testCreate
     */
    public function testItemization($id) {
        $contract = Contrato::find($id);
        $contract->load(['causante', 'contratos_monedas']);
        $contratCurrencies = $contract->contratos_monedas;

        $path = "/contratos/editar/updateOrStore/itemizado/{$id}";

        foreach ($contratCurrencies as $currency) {
            $input = [
              'itemizado_accion' => 'add',
              'itemizado_id' => $currency->itemizado_id,
              'itemizado_item_id' => null,
              'itemizado_padre_id' => null,
              'itemizado_nivel' => null,
              'itemizado_tipo_id' => 2,
              'itemizado_item_nombre' => 'Base',
              'itemizado_item_item' => null,
              'itemizado_item_categoria_id' => 'ajuste_alzado',
              'itemizado_item_importe_total' => number_format($currency->saldo, 2, ',', '.'),
              'itemizado_item_unidad_medida' => null,
              'itemizado_item_cantidad' => 1,
              'itemizado_item_importe_unitario' => 1,
              'itemizado_item_responsable' => 1,
            ];

            $response =  $this->actingAs($this->getUser())
                  ->json('POST', $path, $input);

            $response->assertStatus(200)
              ->assertJson([
                'status' => true,
              ]);
        }

        // Finalizar itemizado
        $response =  $this->actingAs($this->getUser())
          ->json('POST', "/contratos/editar/finalizar/itemizado/$id", [
            'borrador' => false
          ]);

        $response->assertStatus(200);

        return $contract;
    }

    /**
     * @depends testItemization
     */
    public function testPriceAnalysis($contract) {
        $contratCurrencies = $contract->contratos_monedas;

        $TypeManoDeObra = TipoCategoria::where('nombre', 'like', 'ManoObra')->first();

        // Buscar item de analisis
        foreach ($contratCurrencies as $currency) {
            $itemizado = $currency->itemizado;

            if (!$itemizado)
                continue;

            // Anlisis de precio
            $priceAnalysis = $itemizado->analisis_precios;

            if (!$priceAnalysis)
                continue;

            // Item de analisis
            $items = $priceAnalysis->analisis_items;

            if (!$items)
                continue;

            $indiceId = IndiceTabla1::where('moneda_id', $currency->moneda_id)
              ->where('nombre', 'like', '%mano de obra%')
              ->first()
              ->id;

            // Añadir componente
            foreach ($items as $item) {
                $categoriaId = CategoriaModelExtended::where('tipo_id', $TypeManoDeObra->id)
                    ->where('analisis_item_id', $item->id)
                    ->first()
                    ->id;

                $total = number_format($itemizado->total, 2, ',',  '.');

                $path = "/analisis_item_componente/$categoriaId/store";

                $input = [
                    'id' => null,
                    'nombre' => 'Base',
                    'indice_id' => $indiceId,
                    'cantidad' =>  '1,00',
                    'costo_unitario' => $total,
                    'costo_total_adaptado' => $total,
                    'costo_total' => $total,
                ];

                $response =  $this->actingAs($this->getUser())
                  ->json('POST', $path, $input);

                $response->assertStatus(200)
                  ->assertJson([
                    'status' => true,
                  ]);
            }

            $priceAnalysis = $contratCurrencies->first()->itemizado->analisis_precios;

            // Aprobar por Inspección de obras
            $path = "/analisis_precios/updateOrStore/{$priceAnalysis->id}/aprobar_obras";

            $input = [];

            $response =  $this->actingAs($this->getUser())->json('POST', $path, $input);

            $response->assertStatus(200)
              ->assertJson([
                'status' => true,
              ]);

            // Aprobar por Omisión de redeterminaciones de precios
            $path = "/analisis_precios/updateOrStore/{$priceAnalysis->id}/aprobar_precios";

            $input = [];

            $response =  $this->actingAs($this->getUser())->json('POST', $path, $input);

            $response->assertStatus(200)
              ->assertJson([
                'status' => true,
              ]);
        }

        return $contract;
    }

    /**
     * @depends testPriceAnalysis
     */
    public function testWorkplan($contract) {
        $contract->load([
            'causante',
            'contratos_monedas.itemizado.cronograma',
            'contratos_monedas.itemizado.items'
        ]);

        $contratCurrencies = $contract->contratos_monedas;

        // Buscar items
        foreach ($contratCurrencies as $currency) {
            $itemizado = $currency->itemizado;

            if (!$itemizado)
                continue;

            // Cronograma
            $cronograma = $itemizado->cronograma;

            if (!$cronograma)
                continue;

            // Item de cronograma
            $items = $itemizado->items;

            if (!$items)
                continue;

            $valueQty =  $cronograma->meses;
            $valueModulo = 100 % $valueQty;
            $value = (100 - $valueModulo) / $valueQty;

            // Añadir componente
            foreach ($items as $item) {

                $path = "/contratos/cronograma/$cronograma->id/$item->id";

                $input = [
                    'cronograma_id' => $cronograma->id,
                    'item_id' => $item->id,
                    'faltante' => '0,00',
                    'valor' => [],
                    'total' => '100,00',
                ];

                for ($i = 1; $i <= $valueQty; $i++) {
                    if ($i == 1)
                        $input['valor'][$i] = $value + $valueModulo;
                    else
                        $input['valor'][$i] = $value;
                }

                $response =  $this->actingAs($this->getUser())
                    ->json('POST', $path, $input);

                $response->assertStatus(200)
                    ->assertJson([
                        'status' => true,
                    ]);
            }
        }

        // Guardado definitivo de cronograma
        $path = "/contratos/editar/updateOrStore/cronograma/{$contract->id}";

        $input = [];

        $response =  $this->actingAs($this->getUser())
            ->json('POST', $path, $input);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);


        return $contract;
    }

    /**
     * @depends testWorkplan
     */
    public function testRedetermination($contract) {
        $contract->load(['causante', 'contratos_monedas.polinomica']);
        $contratCurrencies = $contract->contratos_monedas;

        $path = "/contratos/editar/updateOrStore/polinomica/{$contract->id}";

        $input = [
           'borrador' => false,
           'polinomicas' => [],
        ];

        foreach ($contratCurrencies as $currency) {
            $polinomica = $currency->polinomica;
            $input['polinomicas'][$polinomica->id] = [
                [
                    'nombre' => 'Base',
                    'tabla_indices_id' => IndiceTabla1::where('moneda_id', $currency->moneda_id)
                      ->where('nombre', 'like', '%mano de obra%')
                      ->first()
                      ->id,
                    'porcentaje' => 1,0000
                ]
            ];
        }

        // Finalizar itemizado
        $response =  $this->actingAs($this->getUser())
            ->json('POST', $path, $input);

        $response->assertStatus(200);

        return $contract;
    }

    /**
     * @depends testRedetermination
     */
    public function testAddendumExpansion($contract) {
        $contract->load([
            'causante',
            'contratos_monedas'
        ]);
        $contratCurrencies = $contract->contratos_monedas;

        $path = '/adenda/store';

        $today = Carbon::today();
        $year = $today->year;
        $yearShort = $today->format('y');

        $input = [
            'borrador' => false,
            'id' => null,
            'contrato_padre_id' => $contract->id,
            'tipo_id' => 2,
            'expediente_madre' => "EX-AA-{$year}-{$contract->numero_contrato}",
            'resoluc_adjudic' =>"AA-{$contract->numero_contrato}/{$yearShort}",
            'denominacion' => "Adenda de ampliación {$contract->numero_contrato}",
            'plazo_id' => $contract->plazo_id,
            'plazo' => $contract->plazo * 2,
            'monto_inicial' => [],
        ];

        foreach ($contratCurrencies as $currency)
            $input['monto_inicial'][$currency->id] = number_format($currency->monto_vigente * 2, 2, ',', '.');

        // Finalizar adenda
        $response =  $this->actingAs($this->getUser())
            ->json('POST', $path, $input);

        $response->assertStatus(200);

        // Output
        $output = json_decode($response->getContent());

        // Evalua que se recupere el id de adenda
        $this->assertIsInt($output->adenda_id);

        return $output->adenda_id;
    }

    #region debug
    public function testIncompleto() {
        $contrat = Contrato::find(49);

        dd($contrat->incompleto);
    }

    public function testAnalisisPrecio() {
        // Elimina la validación de middleware
        $this->withoutMiddleware();

        $path = "/analisis_precios/updateOrStore/23/firmar";

        $response =  $this->actingAs($this->user)
          ->json('GET', $path);

        $response->assertStatus(200)
          ->assertJson([
            'status' => true,
          ]);

    }

    #endregion

    #endregion

    #region Utilities

    /**
     * @param  array  $data
     * @param  array  $currencies
     * @return array
     */
    protected function contrat(array $data = [], $currencies = []) {

        // Valor por defecto
        $now = Carbon::now();
        $year = $now->year;
        $yearShort = $now->format('y');

        $default = [
          'id' => null,
          'borrador' => 0,
          'padre_id' => null,
          'tipo_id' => 1,
          'contrato_completo' => 0,
          'fecha_oferta' => $now->copy()->subMonths(3)->format('d/m/Y'),
          'fecha_acta_inicio' => $now->format('d/m/Y'),
          'anticipo' => '0,00 %',
          'fondo_reparo' => '0,00 %',
        ];

        $data = array_merge($default, $data);

        if (empty($data['numero_contrato']))
            $data['numero_contrato'] = $this->faker->randomNumber(6);

        if (empty($data['numero_contratacion']))
            $data['numero_contratacion'] = "Contrato {$data['numero_contrato']}";

        if (empty($data['expediente_madre']))
            $data['expediente_madre'] = "EX-{$year}-{$data['numero_contrato']}";

        if (empty($data['denominacion']))
            $data['denominacion'] = $this->faker->sentence(5);

        if (empty($data['resoluc_adjudic']))
            $data['resoluc_adjudic'] = "{$data['numero_contrato']}/{$yearShort}";

        if (empty($data['contratista_id']))
            $data['contratista_id'] = Contratista::all()->first()->id;

        if (empty($data['repre_tec_eby_id']))
            $data['repre_tec_eby_id'] = [ User::all()->random()->id ];

        if (empty($data['organo_aprobador_id']))
            $data['organo_aprobador_id'] = OrganoAprobador::all()->random()->id;

        if (empty($data['repre_leg_contratista']))
            $data['repre_leg_contratista'] = $this->faker->name();

        if (empty($data['repre_tec_contratista']))
            $data['repre_tec_contratista'] = $this->faker->name();

        if (empty($data['causante_id']))
            $data['causante_id'] = Causante::where("doble_firma", false)->get()->random()->id;

        if (empty($data['estado_id']))
            $data['estado_id'] = EstadoContrato::where('nombre', 'en_ejecucion')->first()->id;

        if (empty($data['plazo_id']))
            $data['plazo_id'] = Plazo::where('nombre', 'Meses')->first()->id;

        if (empty($data['plazo']))
            $data['plazo'] = 6;

        $data['moneda_id'] = [];
        $data['monto_inicial'] = [];
        $data['tasa_cambio'] = [];

        foreach ($currencies as $i => $currency) {
            $data['moneda_id'][$i] = $currency['id'];
            $data['monto_inicial'][$i] = is_numeric($currency['amount'])
              ? number_format($currency['amount'], 2, ',', '.')
              : $currency['amount'];
            $data['tasa_cambio'][$i] = is_numeric($currency['rate'])
              ? number_format($currency['rate'], 2, ',', '.')
              : $currency['rate'];
        }

        if (!count($data['moneda_id']))
            $data['moneda_id'] = [ Moneda::all()->first()->id ];

        if (!count($data['monto_inicial']))
            $data['monto_inicial'] = [ '1.000.000,00' ];

        if (!count($data['tasa_cambio']))
            $data['tasa_cambio'] = [ '60,00' ];

        return $data;
    }

    protected function getUser() {

        if (!$this->user)
            $this->user = User::where('email', $this->userMail)->first();

        return $this->user;
    }

    #endregion
}
