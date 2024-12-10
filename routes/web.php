<?php

use App\Http\Controllers\ThreeDController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('3d.rl_3d');
    //return view('3d.home_3d_empty');
    //return view('welcome');
});
Route::get('/logistics', function () {
    return view('3d.home_3d');
})->name('logistics');


Route::get('/empty', function () {
    return view('3d.home_3d_empty');
})->name('empty');

Route::get('/api/boxes/free', [ThreeDController::class, 'getFreeBoxes'])->name('api.boxes.free');
Route::post('api/boxes/data', [ThreeDController::class, 'reserveBoxesWithData'])->name('api.boxes.data');
Route::post('/reserveBoxesRL', [ThreeDController::class, 'reserveBoxesRL'])->name('reserveBoxesRL');

Route::get('/test', function () {
    return view('3d.test');

})->name('test');

Route::get('getBoxesReserved', [ThreeDController::class, 'geReservedBoxes'])->name('getBoxesReserved');

