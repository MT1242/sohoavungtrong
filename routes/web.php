<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('page.index');
// });

Route::get('/',[HomeController::class,'index'])->name('home-index');


Route::post('/save-region', [RegionController::class, 'store']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
