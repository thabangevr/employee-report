<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\Okr;
use App\Models\User;
use App\Models\WeeklySubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WeeklySubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->manager()->create();
    }

    private function validSubmissionData(array $overrides = []): array
    {
        return array_merge([
            'week_start_date' => Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString(),
            'one_number_value' => '85%',
            'one_number_label' => 'Sprint completion rate',
            'okr_focus_ids' => [],
            'areas' => [
                [
                    'name' => 'Platform',
                    'status' => 'green',
                    'status_justification' => 'On track',
                    'outcomes' => [
                        ['description' => 'Shipped auth flow', 'okr_id' => ''],
                    ],
                    'priorities' => [
                        ['description' => 'API rate limiting', 'okr_id' => ''],
                    ],
                ],
            ],
            'flags' => [],
            'cross_team_actions' => [],
        ], $overrides);
    }

    // -- Index --

    public function test_index_displays_submissions_for_authenticated_manager(): void
    {
        foreach (range(0, 2) as $i) {
            WeeklySubmission::factory()->create([
                'user_id' => $this->manager->id,
                'week_start_date' => Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeeks($i),
            ]);
        }

        $response = $this->actingAs($this->manager)->get(route('weekly-submissions.index'));

        $response->assertOk();
        $response->assertViewIs('weekly-submissions.index');
        $response->assertViewHas('submissions');
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('weekly-submissions.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_rejects_employee_role(): void
    {
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($employee)->get(route('weekly-submissions.index'));

        $response->assertForbidden();
    }

    // -- Create --

    public function test_create_shows_form(): void
    {
        $response = $this->actingAs($this->manager)->get(route('weekly-submissions.create'));

        $response->assertOk();
        $response->assertViewIs('weekly-submissions.create');
        $response->assertViewHas(['okrs', 'managerAreas', 'weekStartDate']);
    }

    // -- Store --

    public function test_store_creates_draft_submission(): void
    {
        $response = $this->actingAs($this->manager)->post(
            route('weekly-submissions.store'),
            $this->validSubmissionData(),
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('weekly_submissions', [
            'user_id' => $this->manager->id,
            'status' => 'draft',
            'one_number_value' => '85%',
        ]);
    }

    public function test_store_creates_areas_with_outcomes_and_priorities(): void
    {
        $this->actingAs($this->manager)->post(
            route('weekly-submissions.store'),
            $this->validSubmissionData(),
        );

        $submission = WeeklySubmission::where('user_id', $this->manager->id)->first();

        $this->assertNotNull($submission);
        $this->assertCount(1, $submission->areas);
        $this->assertEquals('Platform', $submission->areas->first()->name);
        $this->assertCount(1, $submission->areas->first()->outcomes);
        $this->assertCount(1, $submission->areas->first()->priorities);
    }

    public function test_store_creates_flags(): void
    {
        $data = $this->validSubmissionData([
            'flags' => [
                ['risk' => 'API delay', 'cause' => 'Vendor dependency', 'consequence' => 'Launch slip'],
            ],
        ]);

        $this->actingAs($this->manager)->post(route('weekly-submissions.store'), $data);

        $submission = WeeklySubmission::where('user_id', $this->manager->id)->first();
        $this->assertCount(1, $submission->flags);
        $this->assertEquals('API delay', $submission->flags->first()->risk);
    }

    public function test_store_syncs_okr_focus(): void
    {
        $okr = Okr::factory()->create(['user_id' => $this->manager->id]);

        $data = $this->validSubmissionData(['okr_focus_ids' => [$okr->id]]);

        $this->actingAs($this->manager)->post(route('weekly-submissions.store'), $data);

        $submission = WeeklySubmission::where('user_id', $this->manager->id)->first();
        $this->assertTrue($submission->okrFocus->contains($okr));
    }

    public function test_store_validates_areas_required(): void
    {
        $data = $this->validSubmissionData(['areas' => []]);

        $response = $this->actingAs($this->manager)->post(route('weekly-submissions.store'), $data);

        $response->assertSessionHasErrors('areas');
    }

    public function test_store_validates_area_name_required(): void
    {
        $data = $this->validSubmissionData([
            'areas' => [
                ['name' => '', 'status' => 'green', 'outcomes' => [['description' => 'test']], 'priorities' => [['description' => 'test']]],
            ],
        ]);

        $response = $this->actingAs($this->manager)->post(route('weekly-submissions.store'), $data);

        $response->assertSessionHasErrors('areas.0.name');
    }

    // -- Show --

    public function test_show_displays_submission(): void
    {
        $submission = WeeklySubmission::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->get(route('weekly-submissions.show', $submission));

        $response->assertOk();
        $response->assertViewIs('weekly-submissions.show');
    }

    // -- Edit --

    public function test_edit_shows_form_for_draft(): void
    {
        $submission = WeeklySubmission::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->get(route('weekly-submissions.edit', $submission));

        $response->assertOk();
        $response->assertViewIs('weekly-submissions.edit');
    }

    public function test_edit_redirects_for_submitted(): void
    {
        $submission = WeeklySubmission::factory()->submitted()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->get(route('weekly-submissions.edit', $submission));

        $response->assertRedirect(route('weekly-submissions.show', $submission));
        $response->assertSessionHas('error');
    }

    // -- Update --

    public function test_update_modifies_draft_submission(): void
    {
        $submission = WeeklySubmission::factory()->create(['user_id' => $this->manager->id]);

        $data = $this->validSubmissionData(['one_number_value' => '92%']);
        unset($data['week_start_date']);

        $response = $this->actingAs($this->manager)->put(
            route('weekly-submissions.update', $submission),
            $data,
        );

        $response->assertRedirect(route('weekly-submissions.show', $submission));
        $this->assertDatabaseHas('weekly_submissions', [
            'id' => $submission->id,
            'one_number_value' => '92%',
        ]);
    }

    public function test_update_rejects_submitted_submission(): void
    {
        $submission = WeeklySubmission::factory()->submitted()->create(['user_id' => $this->manager->id]);

        $data = $this->validSubmissionData();
        unset($data['week_start_date']);

        $response = $this->actingAs($this->manager)->put(
            route('weekly-submissions.update', $submission),
            $data,
        );

        $response->assertRedirect(route('weekly-submissions.show', $submission));
        $response->assertSessionHas('error');
    }

    // -- Destroy --

    public function test_destroy_deletes_draft_submission(): void
    {
        $submission = WeeklySubmission::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->delete(route('weekly-submissions.destroy', $submission));

        $response->assertRedirect(route('weekly-submissions.index'));
        $this->assertDatabaseMissing('weekly_submissions', ['id' => $submission->id]);
    }

    public function test_destroy_rejects_submitted_submission(): void
    {
        $submission = WeeklySubmission::factory()->submitted()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->delete(route('weekly-submissions.destroy', $submission));

        $response->assertRedirect(route('weekly-submissions.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('weekly_submissions', ['id' => $submission->id]);
    }

    // -- Submit --

    public function test_submit_changes_status_to_submitted(): void
    {
        $submission = WeeklySubmission::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->post(route('weekly-submissions.submit', $submission));

        $response->assertRedirect(route('weekly-submissions.show', $submission));
        $response->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals(SubmissionStatus::Submitted, $submission->status);
        $this->assertNotNull($submission->submitted_at);
    }

    public function test_submit_rejects_already_submitted(): void
    {
        $submission = WeeklySubmission::factory()->submitted()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)->post(route('weekly-submissions.submit', $submission));

        $response->assertRedirect(route('weekly-submissions.show', $submission));
        $response->assertSessionHas('error');
    }

    // -- Previous Data --

    public function test_previous_data_returns_last_submission_structure(): void
    {
        $submission = WeeklySubmission::factory()->create([
            'user_id' => $this->manager->id,
            'one_number_value' => '75%',
            'one_number_label' => 'Uptime',
        ]);

        $submission->areas()->create([
            'name' => 'Platform',
            'status' => 'green',
            'status_justification' => 'All good',
            'sort_order' => 0,
        ]);

        $submission->crossTeamActions()->create([
            'owner_name' => 'Design Team',
            'ask' => 'Review mockups',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($this->manager)->getJson(route('weekly-submissions.previous-data'));

        $response->assertOk();
        $response->assertJsonStructure([
            'one_number_value',
            'one_number_label',
            'okr_focus_ids',
            'areas' => [['name', 'status', 'outcomes', 'priorities']],
            'cross_team_actions' => [['owner_name', 'ask']],
        ]);

        $data = $response->json();
        $this->assertEquals('75%', $data['one_number_value']);
        $this->assertEquals('Platform', $data['areas'][0]['name']);
        $this->assertNull($data['areas'][0]['status']);
        $this->assertEquals('Design Team', $data['cross_team_actions'][0]['owner_name']);
        $this->assertEquals('', $data['cross_team_actions'][0]['ask']);
    }

    public function test_previous_data_returns_404_when_no_submissions(): void
    {
        $response = $this->actingAs($this->manager)->getJson(route('weekly-submissions.previous-data'));

        $response->assertNotFound();
        $response->assertJson(['error' => 'No previous submission found.']);
    }

    // -- CEO access --

    public function test_ceo_can_access_submissions(): void
    {
        $ceo = User::factory()->ceo()->create();

        $response = $this->actingAs($ceo)->get(route('weekly-submissions.index'));

        $response->assertOk();
    }
}
