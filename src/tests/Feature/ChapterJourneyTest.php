<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Chapter;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChapterJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_journey_chapter(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('chapters.index'))
            ->assertOk()
            ->assertSee('新しい旅を始める');

        $this->actingAs($user)
            ->post(route('chapters.store'), [
                'title' => 'エンジニアとして自立する',
            ])
            ->assertRedirect(route('chapters.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('chapters', [
            'user_id' => $user->id,
            'title' => 'エンジニアとして自立する',
            'ended_at' => null,
            'total_patience' => 0,
            'total_strategy' => 0,
        ]);
    }

    public function test_finishing_a_chapter_keeps_totals_and_starts_next_from_zero(): void
    {
        $user = User::factory()->create();

        $chapter = Chapter::create([
            'user_id' => $user->id,
            'title' => '最初の旅',
            'started_at' => now()->subDays(10),
            'total_patience' => 4,
            'total_strategy' => 7,
        ]);

        $this->actingAs($user)
            ->post(route('chapters.finish'), [
                'next_title' => '次の旅',
            ])
            ->assertRedirect(route('chapters.index'))
            ->assertSessionHas('success');

        $chapter->refresh();
        $this->assertNotNull($chapter->ended_at);
        $this->assertSame(4, $chapter->total_patience);
        $this->assertSame(7, $chapter->total_strategy);

        $nextChapter = $user->chapters()
            ->whereNull('ended_at')
            ->first();

        $this->assertNotNull($nextChapter);
        $this->assertSame('次の旅', $nextChapter->title);
        $this->assertSame(0, $nextChapter->total_patience);
        $this->assertSame(0, $nextChapter->total_strategy);
    }

    public function test_taskkill_adds_stats_to_active_chapter_and_lifetime_totals(): void
    {
        $user = User::factory()->create();

        $chapter = Chapter::create([
            'user_id' => $user->id,
            'title' => '個人開発で収益を作る',
            'started_at' => now(),
        ]);

        $task = Task::create($this->taskAttributes($user, [
            'status' => TaskStatus::Stocked->value,
            'completed_at' => now(),
            'stat_patience' => 2,
            'stat_speed' => 1,
            'stat_focus' => 0,
            'stat_accuracy' => 1,
            'stat_life' => 0,
            'stat_strategy' => 2,
        ]));

        $this->actingAs($user)
            ->postJson(route('taskkill.execute'), [
                'task_ids' => [$task->id],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $user->refresh();
        $chapter->refresh();

        $this->assertSame(2, $user->total_patience);
        $this->assertSame(2, $user->total_strategy);
        $this->assertSame(2, $chapter->total_patience);
        $this->assertSame(1, $chapter->total_speed);
        $this->assertSame(1, $chapter->total_accuracy);
        $this->assertSame(2, $chapter->total_strategy);
    }

    private function taskAttributes(User $user, array $overrides = []): array
    {
        return array_merge([
            'user_id' => $user->id,
            'title' => 'Journey Task',
            'category' => 'work',
            'due_date' => now()->addDay()->toDateString(),
            'importance' => 3,
            'is_urgent' => false,
            'boss_type' => 'mob',
            'status' => TaskStatus::Pending->value,
            'stat_patience' => 1,
            'stat_speed' => 1,
            'stat_focus' => 1,
            'stat_accuracy' => 1,
            'stat_life' => 1,
            'stat_strategy' => 1,
        ], $overrides);
    }
}
