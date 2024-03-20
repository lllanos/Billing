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
use Yacyreta\Causante;
use Yacyreta\Moneda;

class DobleFirmaTest extends ContractTest
{
    protected $contractChiefAr;

    protected $contractChiefPy;

    protected $worksChiefAr;

    protected $worksChiefPy;

    #region Pruebas

    /**
     * Contrato con causante con doble firma
     * @return mixed
     */
    public function testCreate() {

        // Elimina la validación de middleware
        $this->withoutMiddleware();

        // Obtiene las monedas
        $currencies = Moneda::all();

        // Toma las 2 primeras
        $input = $this->contrat(
            [
                'causante_id' => Causante::where("doble_firma", true)->first()->id,
                'anticipo' => '10,00 %',
            ],
            [
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
            ]
        );

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
     * Firma contrato
     * @depends testCreate
     */
    public function testSign($id) {
        $contract = Contrato::find($id);
        $contract->load('causante');
        $this->contractChiefInit($contract->causante);

        $path = "/contratos/$id/firmar";

        $this->contratSign($path);

        return $id;
    }

    /**
     * Crea Itemizado y lo guarda
     * @depends testSign
     */
    public function testItemization($id) {
        return parent::testItemization($id);
    }

    /**
     * Firma el itemizado
     * @depends testItemization
     */
    public function testItemizationSign($contract) {
        $contract->load('causante');
        $this->contractChiefInit($contract->causante);

        // Firmar
        $path = "/contratos/editar/firmar/itemizado/{$contract->id}";

        $this->contratSign($path);

        return $contract;
    }

    /**
     * @depends testItemizationSign
     */
    public function testPriceAnalysis($contract) {
        return parent::testPriceAnalysis($contract);
    }

    /**
     * Firma Analisis de precio
     * @depends testPriceAnalysis
     */
    public function testPriceAnalysisSign($contract) {
        $contract->load([
            'causante',
            'contratos_monedas.itemizado.analisis_precios.analisis_items',
        ]);

        $this->contractChiefInit($contract->causante);

        $priceAnalysis = $contract->contratos_monedas->first()->itemizado->analisis_precios;

        // Firmar
        $path = "analisis_precios/updateOrStore/{$priceAnalysis->id}/firma";

        $this->contratSign($path, "POST");

        return $contract;
    }

    /**
     * @depends testPriceAnalysisSign
     */
    public function testWorkplan($contract) {
        return parent::testWorkplan($contract);
    }

    /**
     * Firma de cronograma
     * @depends testWorkplan
     */
    public function testWorkplanSign($contract) {
        $contract->load([
            'causante'
        ]);

        $this->worksChiefInit($contract->causante);

        // Firmar
        $path = "contratos/editar/firma/cronograma/{$contract->id}";

        $this->worksSign($path);

        return $contract;
    }

    /**
     * @depends testWorkplanSign
     */
    public function testRedetermination($contract) {
        return parent::testRedetermination($contract);
    }

    /**
     * Adenda de expansion
     * @depends testRedetermination
     */
    public function testAddendumExpansion($contract) {
        return parent::testAddendumExpansion($contract);
    }

    /**
     * Firma de adenda de expasion
     * @depends testAddendumExpansion
     */
    public function testAddendumExpansionSign($id) {
        $adenda = Contrato::find($id);

        $adenda->load([
            'causante'
        ]);

        $this->contractChiefInit($adenda->causante);

        // Firmar
        $path = "adenda/{$id}/firmar";

        $this->contratSign($path, "GET");

        return $adenda;

    }

    #endregion

    #region Utilities

    /**
     * Inicializa los jefes de contrato
     * @param $causative
     */
    protected function contractChiefInit(Causante $causative) {
        $this->contractChiefAr = User::find($causative->jefe_contrato_ar);
        $this->contractChiefPy = User::find($causative->jefe_contrato_py);
    }

    /**
     * Inicializa los jefes de obras
     * @param $causative
     */
    protected function worksChiefInit(Causante $causative) {
        $this->worksChiefAr = User::find($causative->jefe_obras_ar);
        $this->worksChiefPy = User::find($causative->jefe_obras_py);
    }

    /**
     * Firma de jefes de contrato
     * @param $causative
     */
    protected function contratSign($uri, $method = 'GET', $data = []) {
        // Lado argentino
        $response =  $this->actingAs($this->contractChiefAr)
            ->json($method, $uri, $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        // Lado paraguayo
        $response =  $this->actingAs($this->contractChiefPy)
            ->json($method, $uri, $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);
    }

    /**
     * Firma los jefes de obra
     * @param $causative
     */
    protected function worksSign($uri, $method = 'GET', $data = []) {
        // Lado argentino
        $response =  $this->actingAs($this->worksChiefAr)
            ->json($method, $uri, $data);

        $response->assertStatus(200);

        // Lado paraguayo
        $response =  $this->actingAs($this->worksChiefPy)
            ->json($method, $uri, $data);

        $response->assertStatus(200)->assertJson([
            'status' => true,
        ]);
    }

    #endregion
}
