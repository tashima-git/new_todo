<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskKillLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MvpCoreFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_stats_total_can_be_zero_but_must_not_exceed_six(): void
    {
        $user = User::factory()->create();

        $tooLarge = $this->validTaskPayload([
            'stat_patience' => 6,
            'stat_speed' => 1,
        ]);

        $this->actingAs($user)
            ->post(route('tasks.store'), $tooLarge)
            ->assertSessionHasErrors('stats_total');

        $zero = $this->validTaskPayload([
            'stat_patience' => 0,
            'stat_speed' => 0,
            'stat_focus' => 0,
            'stat_accuracy' => 0,
            'stat_life' => 0,
            'stat_strategy' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('tasks.store'), $zero)
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'MVP Task',
            'stat_patience' => 0,
            'stat_speed' => 0,
            'stat_focus' => 0,
            'stat_accuracy' => 0,
            'stat_life' => 0,
            'stat_strategy' => 0,
        ]);
    }

    public function test_task_can_move_between_pending_and_stocked(): void
    {
        $user = User::factory()->create();
        $task = Task::create($this->taskAttributes($user));

        $this->actingAs($user)
            ->patch(route('tasks.complete', $task))
            ->assertSessionHas('success');

        $this->assertSame(TaskStatus::Stocked, $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);

        $this->actingAs($user)
            ->patch(route('tasks.uncomplete', $task))
            ->assertSessionHas('success');

        $this->assertSame(TaskStatus::Pending, $task->fresh()->status);
        $this->assertNull($task->fresh()->completed_at);
    }

    public function test_completing_parent_also_stocks_children(): void
    {
        $user = User::factory()->create();

        $parent = Task::create($this->taskAttributes($user, [
            'title' => 'Parent Boss',
            'boss_type' => 'boss',
        ]));

        $child = Task::create($this->taskAttributes($user, [
            'title' => 'Child Task',
            'parent_task_id' => $parent->id,
        ]));

        $this->actingAs($user)
            ->patchJson(route('tasks.complete', $parent))
            ->assertStatus(409)
            ->assertJson(['requires_confirmation' => true]);

        $this->actingAs($user)
            ->patch(route('tasks.complete', $parent), [
                'confirm_children' => '1',
            ])
            ->assertSessionHas('success');

        $this->assertSame(TaskStatus::Stocked, $parent->fresh()->status);
        $this->assertSame(TaskStatus::Stocked, $child->fresh()->status);

        $this->actingAs($user)
            ->get(route('tasks.index', ['status' => 'stocked', 'view' => 'tree']))
            ->assertOk()
            ->assertSee('Parent Boss')
            ->assertSee('Child Task');
    }

    public function test_taskkill_confirms_stocked_tasks_and_adds_exact_stats(): void
    {
        $user = User::factory()->create();
        $task = Task::create($this->taskAttributes($user, [
            'status' => TaskStatus::Stocked->value,
            'completed_at' => now(),
            'stat_patience' => 2,
            'stat_speed' => 1,
            'stat_focus' => 0,
            'stat_accuracy' => 1,
            'stat_life' => 0,
            'stat_strategy' => 2,
            'importance' => 5,
            'boss_type' => 'boss',
        ]));

        $this->actingAs($user)
            ->postJson(route('taskkill.execute'), [
                'task_ids' => [$task->id],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(TaskStatus::Killed, $task->fresh()->status);

        $user->refresh();
        $this->assertSame(2, $user->total_patience);
        $this->assertSame(1, $user->total_speed);
        $this->assertSame(0, $user->total_focus);
        $this->assertSame(1, $user->total_accuracy);
        $this->assertSame(0, $user->total_life);
        $this->assertSame(2, $user->total_strategy);

        $log = TaskKillLog::first();
        $this->assertNotNull($log);
        $this->assertSame($task->id, $log->task_id);
        $this->assertSame($task->title, $log->task_title);
        $this->assertSame(2, $log->gained_patience);
        $this->assertSame(2, $log->gained_strategy);

        $this->actingAs($user)
            ->get(route('taskkill.result'))
            ->assertOk()
            ->assertSee('taskkill-result-root', false);

        $this->actingAs($user)
            ->get(route('record.index'))
            ->assertOk()
            ->assertSee($task->title);
    }

    private function validTaskPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'MVP Task',
            'category' => 'work',
            'due_date' => now()->addDay()->toDateString(),
            'importance' => 3,
            'is_urgent' => 0,
            'stat_patience' => 1,
            'stat_speed' => 1,
            'stat_focus' => 1,
            'stat_accuracy' => 1,
            'stat_life' => 1,
            'stat_strategy' => 1,
        ], $overrides);
    }

    private function taskAttributes(User $user, array $overrides = []): array
    {
        return array_merge($this->validTaskPayload(), [
            'user_id' => $user->id,
            'boss_type' => 'mob',
            'status' => TaskStatus::Pending->value,
        ], $overrides);
    }
}
