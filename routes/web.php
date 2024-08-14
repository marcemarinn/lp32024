<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::resource('ciudades', App\Http\Controllers\CiudadController::class);

Route::resource('entidad_emisora', App\Http\Controllers\EntidadEmisoraController::class);

Route::resource('clientes', App\Http\Controllers\ClienteController::class);

Route::resource('departamento', App\Http\Controllers\DepartamentoController::class);

Route::resource('forma_pagos', App\Http\Controllers\FormaPagoController::class);

Route::resource('articulos', App\Http\Controllers\ArticuloController::class);

Route::resource('usuarios', App\Http\Controllers\UsuarioController::class);

Route::resource('ventas', App\Http\Controllers\VentaController::class);

Route::get('buscar-productos', [App\Http\Controllers\VentaController::class,'buscarProducto']);










