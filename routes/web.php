<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('workshops', 'pages::workshops')->name('workshops');

    // Admin Routes
    Route::middleware(['can:admin'])->group(function () {
            Route::livewire('admin/dashboard', 'pages::admin.dashboard')->name('admin.dashboard');
            Route::livewire('admin/activities', 'pages::admin.activities')->name('admin.activities');
            Route::livewire('admin/registrations', 'pages::admin.registrations')->name('admin.registrations');
        }
        );
    });

require __DIR__ . '/settings.php';
