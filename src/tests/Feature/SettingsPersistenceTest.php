<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_audio_volume_settings_are_fifty_percent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('name="settings[se_volume]" min="0" max="100" value="50"', false)
            ->assertSee('name="settings[taskkill_se_volume]" min="0" max="100" value="50"', false)
            ->assertSee('name="settings[status_se_volume]" min="0" max="100" value="50"', false)
            ->assertSee('name="settings[voice_volume]" min="0" max="100" value="50"', false);
    }

    public function test_user_can_save_settings(): void
    {
        $user = User::factory()->create([
            'name' => 'Before',
        ]);

        $payload = [
            'name' => 'After',
            'settings' => [
                'se_volume' => 80,
                'taskkill_se_volume' => 70,
                'status_se_volume' => 60,
                'voice_type' => 'samurai',
                'voice_volume' => 55,
                'default_task_view' => 'flat',
                'confirm_important_actions' => '0',
                'deadline_notification_enabled' => '1',
                'deadline_notification_timing' => 'three_days_before',
                'tasks_per_page' => 20,
                'auto_strategy_on_create' => 2,
            ],
        ];

        $this->actingAs($user)
            ->post(route('settings.update'), $payload)
            ->assertRedirect(route('settings.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'After',
        ]);

        $this->assertDatabaseHas('user_settings', [
            'user_id' => $user->id,
            'se_volume' => 80,
            'voice_type' => 'samurai',
            'default_task_view' => 'flat',
            'confirm_important_actions' => false,
            'deadline_notification_enabled' => true,
            'deadline_notification_timing' => 'three_days_before',
            'tasks_per_page' => 20,
            'auto_strategy_on_create' => 2,
        ]);

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('value="After"', false)
            ->assertSee('value="80"', false)
            ->assertSee('value="samurai" checked', false)
            ->assertSee('value="flat" selected', false)
            ->assertSee('value="20" selected', false);
    }

    public function test_settings_reject_invalid_values(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('settings.update'), [
                'name' => '',
                'settings' => [
                    'se_volume' => 101,
                    'taskkill_se_volume' => 50,
                    'status_se_volume' => 30,
                    'voice_type' => 'unknown',
                    'voice_volume' => 50,
                    'default_task_view' => 'tree',
                    'confirm_important_actions' => '1',
                    'deadline_notification_enabled' => '0',
                    'deadline_notification_timing' => 'same_day',
                    'tasks_per_page' => 10,
                    'auto_strategy_on_create' => 1,
                ],
            ])
            ->assertSessionHasErrors(['name', 'settings.se_volume', 'settings.voice_type']);

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);
    }
}
