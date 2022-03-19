<?php

use App\Http\Controllers\InitializePosController;
use App\Jobs\CreatePos;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use WebReinvent\CPanel\CPanel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Composer $composer) {
    return view('welcome');
});



Route::post('create/pos', [InitializePosController::class , 'initialize'])->name('initialize.pos');
