<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::get('/test', [App\Http\Controllers\TestController::class, 'test'])->name('test');

Route::middleware(['auth'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    // Admin Users
    Route::livewire('admin/users', 'pages::admin.users.index')->name('admin.users.index');
    Route::livewire('admin/users/create', 'pages::admin.users.user')->name('admin.users.create');
    Route::livewire('admin/users/{id}/edit', 'pages::admin.users.user')->name('admin.users.edit');

    // Admin Company
    Route::livewire('admin/company-edit', 'admin.companies.company')->name('admin.company.edit');

    // Thirdparties
    Route::livewire('thirdparties', 'pages::thirdparties.index')->name('thirdparties.index');
    Route::livewire('thirdparties/create', 'pages::thirdparties.thirdparty')->name('thirdparties.create');
    Route::livewire('thirdparties/{id}/edit', 'pages::thirdparties.thirdparty')->name('thirdparties.edit');

    // Products
    Route::livewire('products', 'pages::products.index')->name('products.index');
    Route::livewire('products/create', 'pages::products.product')->name('products.create');
    Route::livewire('products/{id}/edit', 'pages::products.product')->name('products.edit');

    // Sales Budgets
    Route::livewire('sales/budgets', 'pages::sales.budgets.index')->name('sales.budgets.index');
    Route::livewire('sales/budgets/create', 'pages::sales.budgets.budget')->name('sales.budgets.create');
    Route::livewire('sales/budgets/{id}/edit', 'pages::sales.budgets.budget')->name('sales.budgets.edit');

    // Sales Orders
    Route::livewire('sales/orders', 'pages::sales.orders.index')->name('sales.orders.index');
    Route::livewire('sales/orders/create', 'pages::sales.orders.order')->name('sales.orders.create');
    Route::livewire('sales/orders/{id}/edit', 'pages::sales.orders.order')->name('sales.orders.edit');

    // Sales Invoices
    Route::livewire('sales/invoices', 'pages::sales.invoices.index')->name('sales.invoices.index');
    Route::livewire('sales/invoices/create', 'pages::sales.invoices.invoice')->name('sales.invoices.create');
    Route::livewire('sales/invoices/{id}/edit', 'pages::sales.invoices.invoice')->name('sales.invoices.edit');

    // Sales Notes
    Route::livewire('sales/notes', 'pages::sales.notes.index')->name('sales.notes.index');
    Route::livewire('sales/notes/create', 'pages::sales.notes.note')->name('sales.notes.create');
    Route::livewire('sales/notes/{id}/edit', 'pages::sales.notes.note')->name('sales.notes.edit');

    // User Settings
    Route::redirect('settings', 'settings/profile');
    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
    Route::livewire('settings/password', 'pages::settings.password')->name('user-password.edit');
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/two-factor', 'pages::settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
