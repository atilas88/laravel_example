<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chirp;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use App\Events\ChirpCreated;
use Tests\TestCase;

class ChirpTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test user can see chirps view.
     */
    public function test_users_can_see_chirps(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/chirps');

        $response->assertStatus(200);
    }

    /**
     * Test user can see chirps view.
     */
    public function test_users_can_create_chirps(): void
    {
        $user = User::factory()->create();

        Event::fake();

        $chirp = [
            'message' => 'A new chirp'
        ];
        $response = $this->actingAs($user)->post('/chirps',$chirp);

        Event::assertDispatched(ChirpCreated::class);

        $this->assertDatabaseHas('chirps', $chirp);

        $response->assertRedirect('/chirps');
    }

    public function test_users_can_update_chirps(): void
    {
        $user = User::factory()->create();

        $chirp_message = 'A chirp for this user';
        $chirp = [
            'message' => $chirp_message
        ];

        $user->chirps()->create($chirp);

        $chirp_to_update = Chirp::where('message', $chirp_message)->first();

        $new_chirp = ['message' => 'New message'];

        $response = $this->actingAs($user)->put('/chirps/'.$chirp_to_update->id, $new_chirp);

        $this->assertDatabaseHas('chirps',$new_chirp);

        $response->assertRedirect('/chirps');

    }

    public function test_users_can_delete_chirps(): void
    {
        $user = User::factory()->create();

        $chirp_message = 'A chirp for this user';
        $chirp = [
            'message' => $chirp_message
        ];

        $user->chirps()->create($chirp);

        $chirp_to_update = Chirp::where('message', $chirp_message)->first();

        $response = $this->actingAs($user)->delete('/chirps/'.$chirp_to_update->id);

        $this->assertDatabaseMissing('chirps',$chirp);

        $response->assertRedirect('/chirps');

    }
}
