<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Okr;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OkrControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->manager()->create();
    }

    private function validOkrData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Increase platform reliability',
            'objective_description' => 'Improve uptime and reduce incidents',
            'measure_of_success' => '99.9% uptime',
            'weight' => 25,
            'is_active' => true,
            'key_results' => [],
        ], $overrides);
    }

    // -- Index --

    public function test_index_displays_okrs(): void
    {
        Okr::factory()->count(2)->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->get(route('okrs.index'));

        $response->assertOk();
        $response->assertViewIs('okrs.index');
        $response->assertViewHas(['okrs', 'activeOkrs', 'inactiveOkrs', 'totalWeight']);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('okrs.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_rejects_employee_role(): void
    {
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($employee)->get(route('okrs.index'));

        $response->assertForbidden();
    }

    // -- Create --

    public function test_create_shows_form(): void
    {
        $response = $this->actingAs($this->manager)->get(route('okrs.create'));

        $response->assertOk();
        $response->assertViewIs('okrs.create');
    }

    // -- Store --

    public function test_store_creates_okr(): void
    {
        $response = $this->actingAs($this->manager)->post(
            route('okrs.store'),
            $this->validOkrData(),
        );

        $response->assertRedirect(route('okrs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('okrs', [
            'user_id' => $this->manager->id,
            'title' => 'Increase platform reliability',
            'weight' => 25,
            'is_active' => true,
        ]);
    }

    public function test_store_validates_title_required(): void
    {
        $response = $this->actingAs($this->manager)->post(
            route('okrs.store'),
            $this->validOkrData(['title' => '']),
        );

        $response->assertSessionHasErrors('title');
    }

    // -- Edit --

    public function test_edit_shows_form_for_own_okr(): void
    {
        $okr = Okr::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->get(route('okrs.edit', $okr));

        $response->assertOk();
        $response->assertViewIs('okrs.edit');
    }

    public function test_edit_forbids_other_users_okr(): void
    {
        $other = User::factory()->manager()->create();
        $okr = Okr::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($this->manager)->get(route('okrs.edit', $okr));

        $response->assertForbidden();
    }

    // -- Update --

    public function test_update_modifies_okr(): void
    {
        $okr = Okr::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->put(
            route('okrs.update', $okr),
            $this->validOkrData(['title' => 'Updated OKR title']),
        );

        $response->assertRedirect(route('okrs.index'));
        $this->assertDatabaseHas('okrs', [
            'id' => $okr->id,
            'title' => 'Updated OKR title',
        ]);
    }

    // -- Toggle --

    public function test_toggle_deactivates_active_okr(): void
    {
        $okr = Okr::factory()->create(['user_id' => $this->manager->id, 'is_active' => true]);

        $response = $this->actingAs($this->manager)->patch(route('okrs.toggle', $okr));

        $response->assertRedirect(route('okrs.index'));
        $this->assertDatabaseHas('okrs', ['id' => $okr->id, 'is_active' => false]);
    }

    public function test_toggle_forbids_other_users_okr(): void
    {
        $other = User::factory()->manager()->create();
        $okr = Okr::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($this->manager)->patch(route('okrs.toggle', $okr));

        $response->assertForbidden();
    }

    // -- Destroy --

    public function test_destroy_deletes_own_okr(): void
    {
        $okr = Okr::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->delete(route('okrs.destroy', $okr));

        $response->assertRedirect(route('okrs.index'));
        $this->assertDatabaseMissing('okrs', ['id' => $okr->id]);
    }

    public function test_destroy_forbids_other_users_okr(): void
    {
        $other = User::factory()->manager()->create();
        $okr = Okr::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($this->manager)->delete(route('okrs.destroy', $okr));

        $response->assertForbidden();
    }

    // -- CEO access --

    public function test_ceo_can_access_okrs(): void
    {
        $ceo = User::factory()->ceo()->create();

        $response = $this->actingAs($ceo)->get(route('okrs.index'));

        $response->assertOk();
    }
}
