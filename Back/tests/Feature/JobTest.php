<?php

namespace Tests\Feature;

use App\Jobs\CalculoVariacionEnPublicacion;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCalculoVariacionEnPublicacion()
    {
        dispatch((new CalculoVariacionEnPublicacion())->onQueue('calculos_variacion'));
    }
}
