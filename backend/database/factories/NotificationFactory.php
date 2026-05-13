<?php

namespace Database\Factories;

use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'report_id' => null,
            'claim_id' => null,
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(),
            'status' => NotificationStatus::Unread->value,
            'read_at' => null,
        ];
    }
}
