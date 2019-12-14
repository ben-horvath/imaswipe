<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class WelcomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function permalink_passes_the_medium_id_to_view()
    {
        $this->withoutExceptionHandling();

        $medium_id = Str::random(40);

        $response = $this->get(route('welcome-start-with', [$medium_id]));

        $response->assertStatus(200);

        $response->assertViewIs('welcome');

        $response->assertViewHas('first_medium', $medium_id);
    }

    /**
     * @test
     */
    public function permalink_medium_id_does_not_exist()
    {
        $this->markTestIncomplete();

        $medium_id = Str::random(20);

        $response = $this->get(route('welcome-start-with', [$medium_id]));

        $response->assertRedirect(route('welcome'));
    }
}
