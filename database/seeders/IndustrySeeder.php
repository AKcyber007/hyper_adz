<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            'Retail',
            'Jewellery',
            'Education',
            'Healthcare',
            'Real Estate',
            'Hospitality',
            'Automobile',
            'Technology',
            'Finance',
            'Other'
        ];

        foreach ($industries as $name) {
            Industry::updateOrCreate(
                ['name' => $name],
                ['status' => 'active']
            );
        }
    }
}
