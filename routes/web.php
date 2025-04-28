<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SalesProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SalesServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ServiceController;


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);


Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
});

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
Route::get('/employees/relatorio/pdf', [EmployeeController::class, 'gerarRelatorioPDF'])->name('employees.relatorio.pdf');
Route::delete('/employees/{sales_service}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
Route::post('/employees/{id}/create-access', [EmployeeController::class, 'createAccess'])->name('employees.createAccess');


Route::get('/sales_service', [SalesServiceController::class, 'index'])->name('sales_service.index');
Route::post('/sales_service', [SalesServiceController::class, 'store'])->name('sales_service.store');
Route::put('/sales_service/{id}', [SalesServiceController::class, 'update'])->name('sales_service.update');
Route::delete('/sales_service/{sales_service}', [SalesServiceController::class, 'destroy'])->name('sales_service.destroy');
Route::patch('/sales-service/{id}/mark-paid', [SalesServiceController::class, 'markAsPaid'])->name('sales_service.markPaid');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
Route::get('/services/relatorio/pdf', [ServiceController::class, 'gerarRelatorioPDF'])->name('services.relatorio.pdf');
Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

Route::get('/sales_product', [SalesProductController::class, 'index'])->name('sales_product.index');
Route::post('/sales_product', [SalesProductController::class, 'store'])->name('sales_product.store');
Route::post('/sales_product/{sales_product}/generate-boleto', [SalesProductController::class, 'generateBoleto'])->name('sales_product.generateBoleto');
Route::delete('/sales_product/{id}', [SalesProductController::class, 'destroy'])->name('sales_product.destroy');
Route::patch('/sales/{id}/mark-paid', [SalesProductController::class, 'markAsPaid'])->name('sales.markPaid');

Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
Route::get('/schedules/events', [ScheduleController::class, 'getEvents']);


Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::get('/customers/relatorio/pdf', [CustomerController::class, 'gerarRelatorioPDF'])->name('customers.relatorio.pdf');

Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::post('/product', [ProductController::class, 'store'])->name('product.store');
Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update');
Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
Route::get('/product/relatorio/pdf', [ProductController::class, 'gerarRelatorioPDF'])->name('product.relatorio.pdf');


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
