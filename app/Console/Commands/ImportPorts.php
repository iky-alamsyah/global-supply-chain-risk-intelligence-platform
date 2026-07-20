<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Console\Command;

class ImportPorts extends Command
{
    protected $signature = 'ports:import';

    protected $description = 'Import World Port Index';

    public function handle()
    {
        $path = database_path('data/world_port_index.csv');

        if (! file_exists($path)) {

            $this->error('CSV file not found.');

            return Command::FAILURE;

        }

        $file = fopen($path, 'r');

        $header = fgetcsv($file);

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {

            $data = array_combine($header, $row);

            $country = Country::where(
    'name',
    trim($data['Country Code'])
)->first();

            if (! $country) {

                continue;

            }

            Port::updateOrCreate(

                [

                    'port_code' => trim($data['UN/LOCODE'])

                ],

                [

                    'country_id' => $country->id,

                    'port_name' => trim($data['Main Port Name']),

                    'city' => trim($data['Main Port Name']),

                    'latitude' => $data['Latitude'] ?: 0,

                    'longitude' => $data['Longitude'] ?: 0,

                    'timezone' => null,

                    'status' => 'ACTIVE',

                    'description' => trim($data['World Water Body'])

                ]

            );

            $count++;

        }

        fclose($file);

        $this->info("Imported {$count} ports.");

        return Command::SUCCESS;
    }
}