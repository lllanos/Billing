<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use SebastianBergmann\Diff\Differ;
use Tests\TestCase;
use Yacyreta\Causante;

class IndicesTest extends TestCase
{
    use WithoutMiddleware;

    protected $userMail = 'admin@admin.com';

    protected $user;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPublicar()
    {
        $id = 49;

        // Get user admin
        $user = $this->getUser();

        $input = [];

        // Request
        $response =  $this->actingAs($user)
            ->json('POST', "publicaciones/$id/publicar", $input);
    }

    protected function getUser() {

        if (!$this->user)
            $this->user = User::where('email', $this->userMail)->first();

        return $this->user;
    }
}
