<?php

use Illuminate\Database\Seeder;

class PublicacionIndiceMonedaTableSeeder extends Seeder
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
          'valores_indices.indice_tabla1','instancias'
        ])->get();

        foreach ($publicaciones as $publicacion) {
            
            $monedas = []; // Array que almacena las publicaciones por moneda
            $currencyPublication = $publicacion;

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
            
            // Si se creó una nueva publicación 
            if($publicacion->id != $currencyPublication->id){
                
                // Replicamos todas las instancias de la publicación original y las vinculamos con la nueva publicacion
                foreach ($publicacion->instancias as $instancia) {
                    
                    $currencyInstancia = \Indice\InstanciaPublicacionIndice::firstOrCreate(
                        ['publicacion_id' => $currencyPublication->id] ,
                        ['estado' => $instancia->estado,
                         'batch' => $instancia->batch,
                         'observaciones' => $instancia->observaciones,
                         'user_creator_id' => $instancia->user_creator_id]
                    );

                }       
            }

        }      

    }
}
