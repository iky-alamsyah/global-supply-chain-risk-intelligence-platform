<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\CountryController as UserCountryController;
use App\Http\Controllers\User\PortController;
use App\Http\Controllers\User\NewsController;
use App\Http\Controllers\User\VisualizationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

/**
 * User Dashboard
 */
Route::middleware(['auth', 'role:user'])
    ->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'index'])
            ->name('dashboard');
Route::get(
        '/countries',
        [UserCountryController::class,'index']
    )->name('countries.index');
    Route::get(
    '/countries/{country}',
    [UserCountryController::class,'show']
)->name('countries.show');

    Route::post(
        '/countries/{country}/refresh',
        [UserCountryController::class, 'refresh']
    )->name('countries.refresh');

Route::get('/ports', [PortController::class, 'index'])
    ->name('ports.index');

Route::get('/ports/{port}', [PortController::class, 'show'])
    ->name('ports.show');

    Route::get('/news', [NewsController::class, 'index'])
    ->name('news.index');

Route::get('/news/{news}', [NewsController::class, 'show'])
    ->name('news.show');

Route::get('/articles/{slug}', [\App\Http\Controllers\Admin\AdminArticleController::class, 'publicShow'])
    ->name('articles.public.show');

Route::get('/weather', [App\Http\Controllers\User\WeatherController::class, 'index'])
    ->name('weather.index');
Route::post('/weather/refresh', [App\Http\Controllers\User\WeatherController::class, 'refresh'])
    ->name('weather.refresh');

Route::get('/currency', [App\Http\Controllers\User\CurrencyController::class, 'index'])
    ->name('currency.index');

Route::get('/risk-engine', [App\Http\Controllers\User\RiskEngineController::class, 'index'])
    ->name('risk-engine.index');

Route::get('/comparison', [App\Http\Controllers\User\ComparisonController::class, 'index'])
    ->name('comparison.index');

Route::get('/shipment-estimation', [\App\Http\Controllers\User\ShipmentEstimationController::class, 'index'])
    ->name('shipment-estimation.index');
Route::post('/shipment-estimation/estimate', [\App\Http\Controllers\User\ShipmentEstimationController::class, 'estimate'])
    ->name('shipment-estimation.estimate');

Route::get('/favorites', [App\Http\Controllers\User\FavoriteController::class, 'index'])
    ->name('favorites.index');
Route::post('/favorites/toggle', [App\Http\Controllers\User\FavoriteController::class, 'toggle'])
    ->name('favorites.toggle');

    Route::middleware(['auth'])
    ->group(function () {

        Route::get(
            '/visualization',
            [VisualizationController::class, 'index']
        )->name('visualization.index');

        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])
            ->name('profile.update');
        Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])
            ->name('profile.password');

    });
    });

/**
 * Admin Dashboard
 */
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('/users', \App\Http\Controllers\Admin\UserController::class)
            ->names('users')
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('/ports', \App\Http\Controllers\Admin\AdminPortController::class)
            ->names('ports');

        Route::resource('/articles', \App\Http\Controllers\Admin\AdminArticleController::class)
            ->names('articles');

        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])
            ->name('profile.update');

    });

require __DIR__.'/auth.php';