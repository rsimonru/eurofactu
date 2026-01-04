<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    // Admin Users
    Volt::route('admin/users', 'admin.users.index')->name('admin.users.index');
    Volt::route('admin/users/create', 'admin.users.user')->name('admin.users.create');
    Volt::route('admin/users/{id}/edit', 'admin.users.user')->name('admin.users.edit');

    // Admin Company
    Volt::route('admin/company-edit', 'admin.companies.company')->name('admin.company.edit');

    // Sales Budgets
    Volt::route('sales/budgets', 'sales.budgets.index')->name('sales.budgets.index');
    Volt::route('sales/budgets/create', 'sales.budgets.budget')->name('sales.budgets.create');
    Volt::route('sales/budgets/{id}/edit', 'sales.budgets.budget')->name('sales.budgets.edit');

    // Thirdparties
    Volt::route('thirdparties', 'thirdparties.index')->name('thirdparties.index');
    Volt::route('thirdparties/create', 'thirdparties.thirdparty')->name('thirdparties.create');
    Volt::route('thirdparties/{id}/edit', 'thirdparties.thirdparty')->name('thirdparties.edit');

    // Products
    Volt::route('products', 'products.index')->name('products.index');
    Volt::route('products/create', 'products.product')->name('products.create');
    Volt::route('products/{id}/edit', 'products.product')->name('products.edit');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
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
