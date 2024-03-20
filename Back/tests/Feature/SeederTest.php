<?php

namespace Tests\Feature;

use Tests\TestCase;
use Yacyreta\Seeds\AnalisisPrecios\AnalisisPreciosSeeder;

class SeederTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAnalisisPrecios()
    {
      $seeder = new AnalisisPreciosSeeder();
      $seeder->run();
    }
}
