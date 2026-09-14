<?php

use App\Http\Controllers\Installer\InstallerController;
use Illuminate\Support\Facades\Route;

Route::prefix('install')->name('installer.')->middleware(['web', 'installer.check'])->group(function () {

  // Welcome
  Route::get('/',                    [InstallerController::class, 'welcome'])->name('welcome');

  // Step 1 — Requirements
  Route::get('/requirements',        [InstallerController::class, 'requirements'])->name('requirements');

  // Step 2 — Database
  Route::get('/database',            [InstallerController::class, 'database'])->name('database');
  Route::post('/database',           [InstallerController::class, 'databaseSave'])->name('database.save');
  Route::post('/database/test',      [InstallerController::class, 'testConnection'])->name('database.test');

  // Step 3 — Environment
  Route::get('/environment',         [InstallerController::class, 'environment'])->name('environment');
  Route::post('/environment',        [InstallerController::class, 'environmentSave'])->name('environment.save');

  // Step 4 — License
  Route::get('/license',             [InstallerController::class, 'license'])->name('license');
  Route::post('/license',            [InstallerController::class, 'licenseSave'])->name('license.save');

  // Step 5 — Admin Account
  Route::get('/admin-account',       [InstallerController::class, 'admin'])->name('admin');
  Route::post('/admin-account',      [InstallerController::class, 'adminSave'])->name('admin.save');

  // Step 6 — Finish
  Route::get('/finish',              [InstallerController::class, 'finish'])->name('finish');
});

// Complete is outside the middleware group so it runs even after installation
Route::get('/install/complete', [InstallerController::class, 'complete'])
  ->middleware('web')
  ->name('installer.complete');

