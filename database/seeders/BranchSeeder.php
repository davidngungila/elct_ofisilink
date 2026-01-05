<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Headquarters branch
        Branch::updateOrCreate(
            ['code' => 'HQ'],
            [
                'name' => 'Headquarters',
                'code' => 'HQ',
                'address' => 'Main Office',
                'is_active' => true,
            ]
        );

        $this->command->info('Branches seeded successfully!');
    }
}
