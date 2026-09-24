<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('Index');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/register', [RegisterController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [RegisterController::class, 'register'])
    ->name('register.post');

Route::resource('productos', ProductoController::class);

// ==========================================
// PANEL DE ADMINISTRACIÓN
// ==========================================
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Usuarios
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/estado', [UserController::class, 'toggleStatus'])->name('usuarios.toggle-status');
    Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');

    // Menú
    Route::get('/menu', [App\Http\Controllers\Admin\MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [App\Http\Controllers\Admin\MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{producto}', [App\Http\Controllers\Admin\MenuController::class, 'update'])->name('menu.update');
    Route::patch('/menu/{producto}/estado', [App\Http\Controllers\Admin\MenuController::class, 'toggleStatus'])->name('menu.toggle-status');
    Route::delete('/menu/{producto}', [App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('menu.destroy');

    // Pedidos
    Route::get('/pedidos', [App\Http\Controllers\Admin\PedidoController::class, 'index'])->name('pedidos.index');
    Route::patch('/pedidos/{pedido}/estado', [App\Http\Controllers\Admin\PedidoController::class, 'updateEstado'])->name('pedidos.update-estado');
    Route::get('/pedidos/{pedido}/detalles', [App\Http\Controllers\Admin\PedidoController::class, 'detalles'])->name('pedidos.detalles');

    // Reportes
    Route::get('/reportes', [App\Http\Controllers\Admin\ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/live-data', [App\Http\Controllers\Admin\ReporteController::class, 'liveData'])->name('reportes.live-data');
    Route::post('/reportes/generar', [App\Http\Controllers\Admin\ReporteController::class, 'generar'])->name('reportes.generar');
    Route::get('/reportes/{reporte}/ver', [App\Http\Controllers\Admin\ReporteController::class, 'ver'])->name('reportes.ver');
    Route::delete('/reportes/{reporte}', [App\Http\Controllers\Admin\ReporteController::class, 'destroy'])->name('reportes.destroy');

    // Reservas
    Route::get('/reservas', [App\Http\Controllers\Admin\ReservaController::class, 'index'])->name('reservas.index');
    Route::post('/reservas', [App\Http\Controllers\Admin\ReservaController::class, 'store'])->name('reservas.store');
    Route::put('/reservas/{reserva}', [App\Http\Controllers\Admin\ReservaController::class, 'update'])->name('reservas.update');
    Route::delete('/reservas/{reserva}', [App\Http\Controllers\Admin\ReservaController::class, 'destroy'])->name('reservas.destroy');

    // Domicilios
    Route::get('/domicilios', [App\Http\Controllers\Admin\DomicilioController::class, 'index'])->name('domicilios.index');
    Route::post('/domicilios', [App\Http\Controllers\Admin\DomicilioController::class, 'store'])->name('domicilios.store');
    Route::patch('/domicilios/{pedido}/estado', [App\Http\Controllers\Admin\DomicilioController::class, 'updateEstado'])->name('domicilios.update-estado');
    Route::get('/domicilios/{pedido}/detalles', [App\Http\Controllers\Admin\DomicilioController::class, 'detalles'])->name('domicilios.detalles');
    Route::delete('/domicilios/{pedido}', [App\Http\Controllers\Admin\DomicilioController::class, 'destroy'])->name('domicilios.destroy');
});

// ==========================================
// PANEL DEL CLIENTE
// ==========================================

Route::middleware('auth')->group(function () {

    // Reservas
    Route::get('/mis-reservas', function () {
        return view('cliente.reservas');
    })->name('reservas.cliente');


    // Pedidos
    Route::get('/mis-pedidos', function () {
        return view('cliente.pedidos');
    })->name('pedidos.cliente');


    // Domicilios
    Route::get('/domicilios', function () {
        return view('cliente.domicilios');
    })->name('domicilios.cliente');


    // Perfil
    Route::get('/perfil', [App\Http\Controllers\PerfilController::class, 'index'])->name('perfil');
    Route::put('/perfil', [App\Http\Controllers\PerfilController::class, 'update'])->name('perfil.update');

    // Notificaciones
    Route::get('/notificaciones', [App\Http\Controllers\NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/notificaciones/latest', [App\Http\Controllers\NotificacionController::class, 'getLatest'])->name('notificaciones.latest');
    Route::post('/notificaciones/{id}/leer', [App\Http\Controllers\NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');
    Route::post('/notificaciones/leer-todas', [App\Http\Controllers\NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leer-todas');
    Route::delete('/notificaciones/{id}', [App\Http\Controllers\NotificacionController::class, 'destroy'])->name('notificaciones.destroy');
});

// ==========================================
// INVENTARIO
// ==========================================
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/inventario', [App\Http\Controllers\Admin\InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/{producto}/stock', [App\Http\Controllers\Admin\InventarioController::class, 'actualizarStock'])->name('inventario.actualizar-stock');
    Route::get('/inventario/{producto}/historial', [App\Http\Controllers\Admin\InventarioController::class, 'historial'])->name('inventario.historial');
    Route::get('/inventario/sugerencias', [App\Http\Controllers\Admin\InventarioController::class, 'sugerenciasStock'])->name('inventario.sugerencias');
    Route::put('/inventario/{producto}/editar-minimos', [App\Http\Controllers\Admin\InventarioController::class, 'editarMinimos'])->name('inventario.editar-minimos');

});