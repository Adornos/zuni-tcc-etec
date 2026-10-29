<?php

namespace Database\Factories;

use App\Enums\ConversationType;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => ConversationType::TEACHER_COORDINATOR,
            'pair_key' => fn () => 'f:'.fake()->unique()->numberBetween(1, 999999999),
            'initiated_by' => User::factory(),
        ];
    }
}
