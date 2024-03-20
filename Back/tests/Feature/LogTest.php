<?php

namespace Tests\Feature;

use SebastianBergmann\Diff\Differ;
use Tests\TestCase;
use Yacyreta\Causante;

class LogTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testDiff()
    {
        $causantes = Causante::all();
        $first = json_encode($causantes->first()->toArray(), JSON_PRETTY_PRINT);
        $last = json_encode($causantes->last()->toArray(), JSON_PRETTY_PRINT);

        $differ = new Differ;
        $diff = $differ->diff($first, $last);

        dd($diff);
    }
}
