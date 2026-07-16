<?php

namespace Database\Seeders;

use App\Models\PositiveWord;
use Illuminate\Database\Seeder;

class PositiveWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = [
            'stable',
            'safe',
            'growth',
            'recovery',
            'boost',
            'efficient',
            'expansion',
            'upgrade',
            'green',
            'resolution',
            'positive',
            'open',
            'agreement',
            'alliance',
            'security',
            'active',
            'increase',
            'benefit',
            'success',
            'resolve',
            'progress',
            'optimistic',
            'normal',
            'restored',
            'smooth',
        ];

        foreach ($words as $word) {
            PositiveWord::firstOrCreate(['word' => $word]);
        }
    }
}
