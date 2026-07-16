<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Countries: " . \App\Models\Country::count() . "\n";
echo "Ports: " . \App\Models\Port::count() . "\n";
echo "NewsCache: " . \App\Models\NewsCache::count() . "\n";
echo "CountryRiskScore: " . \App\Models\CountryRiskScore::count() . "\n";
echo "Users: " . \App\Models\User::count() . "\n";
