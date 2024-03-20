<?php

namespace Tests\Feature;

use Itemizado\Item;
use Tests\TestCase;

class ItemCloneTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testChildren()
    {
      $item = Item::find(1011);
      $item->clone([2]);
    }
}
