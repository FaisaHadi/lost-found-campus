<?php

namespace Database\Factories;

use App\Enums\ModerationStatus;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'report_type' => fake()->randomElement(ReportType::values()),
            'image_path' => null,
            'latitude' => fake()->latitude(-90, 90),
            'longitude' => fake()->longitude(-180, 180),
            'location_text' => fake()->streetAddress(),
            'status' => ReportStatus::Pending->value,
            'moderation_status' => ModerationStatus::Pending->value,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => ReportStatus::Approved->value,
            'moderation_status' => ModerationStatus::Approved->value,
        ]);
    }
}
