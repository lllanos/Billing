<?php

use Illuminate\Database\Seeder;

class PublicacionIndicePublicadoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Obtengo todas las publicaciones con sus respectivos valores e indices
        $publicaciones = \Indice\PublicacionIndice::with([
          'valores_publicados.indice_tabla1'
        ])->get();

        foreach ($publicaciones as $publicacion) {
            
            $monedas = []; // Array que almacena las publicaciones por moneda

            foreach ($publicacion->valores_publicados as $valor) {
                
                // Obtengo la moneda del valor actual
                $id_moneda = $valor->indice_tabla1->moneda_id;

                // Verifico si en el array monedas ya existe la moneda
                if (isset($monedas[$id_moneda])) {
                    // Obtengo la publicacion del array monedas
                    $currencyPublication = $monedas[$id_moneda];

                }
                elseif(is_null($publicacion->moneda_id)){// No existe la moneda en el array entonces verifico si la publicacion ya contaba con moneda asignada
                    
                    //No tiene moneda asignada, le asigno la actual y la almaceno en el array
                    $publicacion->moneda_id = $id_moneda;
                    $publicacion->save();
                    $monedas[$id_moneda] = $publicacion;
                    $currencyPublication = $publicacion;
                }
                else {
                    // Tiene moneda asignada entonces replico la publicacion con una nueva moneda
                    // Primero chequeo si ya existe una publicacion con la nueva moneda en el mismo mes y anio
                    // Si no existe realizo la replica de la publicacion con la nueva moneda
                    $currencyPublication =\Indice\PublicacionIndice::firstOrCreate(
                        ['anio' => $publicacion->anio,
                         'mes' => $publicacion->mes,
                         'moneda_id' => $id_moneda] ,
                        ['publicado' => $publicacion->publicado]
                    );

                    $monedas[$id_moneda] = $currencyPublication;
                }

                if ($valor->publicacion_id == $currencyPublication->id)
                    continue;

                $valor->publicacion_id = $currencyPublication->id;
                $valor->save();
            }    
        }
    }
}
