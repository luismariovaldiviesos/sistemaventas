<?php

use App\Http\Livewire\Arqueos;
use App\Http\Livewire\Asignar;
use App\Http\Livewire\Cajas;
use App\Http\Livewire\Categories;
use App\Http\Livewire\Customers;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Descuentos;
use App\Http\Livewire\Diario;
use App\Http\Livewire\Impuestos;
use App\Http\Livewire\Permisos;
use App\Http\Livewire\Products;
use App\Http\Livewire\Reports;
use App\Http\Livewire\Sales;
use App\Http\Livewire\Settings;
use App\Http\Livewire\Users;
use App\Http\Livewire\Roles;
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

Route::middleware(['auth'])->group(function () {

    Route::get('categories', Categories::class)->name('categories');
    Route::get('products', Products::class)->name('products');
    Route::get('customers', Customers::class)->name('customers');
    Route::get('users', Users::class)->name('users');
    Route::get('sales', Sales::class)->name('sales');
    Route::get('reports', Reports::class)->name('reports');
    Route::get('dash', Dashboard::class)->name('dash');
    Route::get('settings', Settings::class)->name('settings');
    Route::get('diarios', Diario::class)->name('diario');
    Route::get('cajas', Cajas::class)->name('cajas');
    Route::get('arqueos', Arqueos::class)->name('arqueos');
    Route::get('roles', Roles::class)->name('roles');
    Route::get('permisos', Permisos::class)->name('permisos');
    Route::get('asignar', Asignar::class)->name('asignar');
    Route::get('descuentos', Descuentos::class)->name('descuentos');



});



// ruta principal
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
