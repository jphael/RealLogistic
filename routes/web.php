<?php

use App\Http\Controllers\ThreeDController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('3d.home_3d_empty');
    //return view('welcome');
});
Route::get('/logistics', function () {
    return view('3d.home_3d');
})->name('logistics');


Route::get('/api/boxes/free', [ThreeDController::class, 'getFreeBoxes'])->name('api.boxes.free');
Route::post('api/boxes/data', [ThreeDController::class, 'reserveBoxesWithData'])->name('api.boxes.data');

Route::get('/test', function () {
    return view('3d.test');

})->name('test');
