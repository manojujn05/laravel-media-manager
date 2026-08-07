<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')
    ->prefix('asset-manager')
    ->group(function () {

        Route::view(
            '/',
            'asset-manager::browser'
        )->name('asset-manager.browser');

    });