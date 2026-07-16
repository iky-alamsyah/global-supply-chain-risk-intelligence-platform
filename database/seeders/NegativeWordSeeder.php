<?php

namespace Database\Seeders;

use App\Models\NegativeWord;
use Illuminate\Database\Seeder;

class NegativeWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = [
            'delay',
            'strike',
            'block',
            'congestion',
            'disruption',
            'crisis',
            'accident',
            'inflation',
            'shutdown',
            'warning',
            'collapse',
            'risk',
            'conflict',
            'blockade',
            'tension',
            'sanction',
            'tariff',
            'closure',
            'storm',
            'cyclone',
            'typhoon',
            'disaster',
            'problem',
            'protest',
            'halt',
            'closed',
            'suspend',
            'danger',
            'high risk',
            'war',
            'threat',
            'shortage',
        ];

        foreach ($words as $word) {
            NegativeWord::firstOrCreate(['word' => $word]);
        }
    }
}
