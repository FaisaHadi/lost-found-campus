<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Claim>
 */
class ClaimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'report_id' => Report::factory()->approved(),
            'claimant_id' => User::factory(),
            'proof_text' => fake()->paragraph(),
            'status' => ClaimStatus::Pending->value,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}
