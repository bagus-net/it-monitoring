<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
Route::post('/dashboard/check-now', [DashboardController::class, 'checkNow'])->name('dashboard.check-now');

Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
Route::patch('/sites/{site}/toggle', [SiteController::class, 'toggle'])->name('sites.toggle');

// Equipment routes
Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
Route::get('/equipments/create', [EquipmentController::class, 'create'])->name('equipments.create');
Route::post('/equipments', [EquipmentController::class, 'store'])->name('equipments.store');
Route::get('/equipments/{equipment}', [EquipmentController::class, 'show'])->name('equipments.show');
Route::get('/equipments/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipments.edit');
Route::put('/equipments/{equipment}', [EquipmentController::class, 'update'])->name('equipments.update');
Route::delete('/equipments/{equipment}', [EquipmentController::class, 'destroy'])->name('equipments.destroy');

// Maintenance routes
Route::get('/maintenances/schedules', [MaintenanceController::class, 'schedules'])->name('maintenances.schedules');
Route::get('/maintenances/schedules/{checklistItem}', [MaintenanceController::class, 'showSchedule'])->name('maintenances.schedules.show');
Route::get('/maintenances/schedules/{checklistItem}/edit', [MaintenanceController::class, 'editSchedule'])->name('maintenances.schedules.edit');
Route::put('/maintenances/schedules/{checklistItem}', [MaintenanceController::class, 'updateSchedule'])->name('maintenances.schedules.update');
Route::delete('/maintenances/schedules/{checklistItem}', [MaintenanceController::class, 'destroySchedule'])->name('maintenances.schedules.destroy');
Route::get('/maintenances/schedules/create', [MaintenanceController::class, 'createSchedule'])->name('maintenances.create_schedule');
Route::post('/maintenances/schedules', [MaintenanceController::class, 'storeSchedule'])->name('maintenances.store_schedule');
Route::get('/maintenances/checklists', [MaintenanceController::class, 'checklists'])->name('maintenances.checklists');
Route::post('/maintenances/logs', [MaintenanceController::class, 'storeLog'])->name('maintenances.store_log');
Route::get('/maintenances/grid', [MaintenanceController::class, 'grid'])->name('maintenances.grid');

// Convenience aliases / redirects for common URLs (fixes clients/bookmarks using alternate paths)
Route::get('/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
Route::get('/maintenances/schedules/new', [MaintenanceController::class, 'create'])->name('maintenances.schedules.new');

// Masters: manufacturers & locations
Route::prefix('masters')->name('masters.')->group(function(){
	Route::get('/manufacturers', [\App\Http\Controllers\ManufacturerController::class, 'index'])->name('manufacturers.index');
	Route::get('/manufacturers/create', [\App\Http\Controllers\ManufacturerController::class, 'create'])->name('manufacturers.create');
	Route::post('/manufacturers', [\App\Http\Controllers\ManufacturerController::class, 'store'])->name('manufacturers.store');
	Route::get('/manufacturers/{manufacturer}/edit', [\App\Http\Controllers\ManufacturerController::class, 'edit'])->name('manufacturers.edit');
	Route::put('/manufacturers/{manufacturer}', [\App\Http\Controllers\ManufacturerController::class, 'update'])->name('manufacturers.update');
	Route::delete('/manufacturers/{manufacturer}', [\App\Http\Controllers\ManufacturerController::class, 'destroy'])->name('manufacturers.destroy');

	Route::get('/locations', [\App\Http\Controllers\LocationController::class, 'index'])->name('locations.index');
	Route::get('/locations/create', [\App\Http\Controllers\LocationController::class, 'create'])->name('locations.create');
	Route::post('/locations', [\App\Http\Controllers\LocationController::class, 'store'])->name('locations.store');
	Route::get('/locations/{location}/edit', [\App\Http\Controllers\LocationController::class, 'edit'])->name('locations.edit');
	Route::put('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'update'])->name('locations.update');
	Route::delete('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'destroy'])->name('locations.destroy');

	// equipment types
	Route::get('/equipment-types', [\App\Http\Controllers\EquipmentTypeController::class, 'index'])->name('equipment-types.index');
	Route::get('/equipment-types/create', [\App\Http\Controllers\EquipmentTypeController::class, 'create'])->name('equipment-types.create');
	Route::post('/equipment-types', [\App\Http\Controllers\EquipmentTypeController::class, 'store'])->name('equipment-types.store');
	Route::get('/equipment-types/{equipmentType}/edit', [\App\Http\Controllers\EquipmentTypeController::class, 'edit'])->name('equipment-types.edit');
	Route::put('/equipment-types/{equipmentType}', [\App\Http\Controllers\EquipmentTypeController::class, 'update'])->name('equipment-types.update');
	Route::delete('/equipment-types/{equipmentType}', [\App\Http\Controllers\EquipmentTypeController::class, 'destroy'])->name('equipment-types.destroy');

	// checklist items (program perawatan)
	Route::get('/checklist-items', [\App\Http\Controllers\ChecklistItemController::class, 'index'])->name('checklist-items.index');
	Route::get('/checklist-items/create', [\App\Http\Controllers\ChecklistItemController::class, 'create'])->name('checklist-items.create');
	Route::post('/checklist-items', [\App\Http\Controllers\ChecklistItemController::class, 'store'])->name('checklist-items.store');
	Route::get('/checklist-items/{item}/edit', [\App\Http\Controllers\ChecklistItemController::class, 'edit'])->name('checklist-items.edit');
	Route::put('/checklist-items/{item}', [\App\Http\Controllers\ChecklistItemController::class, 'update'])->name('checklist-items.update');
	Route::delete('/checklist-items/{item}', [\App\Http\Controllers\ChecklistItemController::class, 'destroy'])->name('checklist-items.destroy');
});
