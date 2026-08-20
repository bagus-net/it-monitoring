<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MaintenanceChecklistController;
use App\Http\Controllers\MonthlyScheduleController;
use App\Http\Controllers\ScheduleReportController;
use App\Http\Controllers\ItRepairTicketController;
use App\Http\Controllers\WebMonitoringChecklistController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\EquipmentTransferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
	Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// URL ini dibaca dari barcode pada label fisik peralatan.
Route::get('/equipment-scan/{equipment}', [EquipmentController::class, 'scan'])->name('equipments.scan');

Route::middleware('auth')->group(function () {
	Route::get('/', fn () => redirect(auth()->user()->isEmployee() ? route('it-repair-tickets.index') : route('dashboard')));

	// Tanda tangan digital: semua level
	Route::get('/profile/signature', [\App\Http\Controllers\SignatureController::class, 'edit'])->name('signature.edit');
	Route::put('/profile/signature', [\App\Http\Controllers\SignatureController::class, 'update'])->name('signature.update');
	Route::delete('/profile/signature', [\App\Http\Controllers\SignatureController::class, 'destroy'])->name('signature.destroy');

	// Tiket perbaikan IT: semua level boleh mengakses, karyawan hanya melihat tiket peralatannya sendiri
	Route::get('/it-repair-tickets/notifications', [ItRepairTicketController::class, 'notifications'])->name('it-repair-tickets.notifications');	Route::get('/it-repair-tickets', [ItRepairTicketController::class, 'index'])->name('it-repair-tickets.index');
	Route::get('/it-repair-tickets/create', [ItRepairTicketController::class, 'create'])->name('it-repair-tickets.create');
	Route::post('/it-repair-tickets', [ItRepairTicketController::class, 'store'])->name('it-repair-tickets.store');
	Route::get('/it-repair-tickets/{itRepairTicket}', [ItRepairTicketController::class, 'show'])->name('it-repair-tickets.show');

	// Master + Admin IT
	Route::middleware('role:master,admin_it')->group(function () {
		Route::get('/it-repair-tickets/{itRepairTicket}/repair', [ItRepairTicketController::class, 'repair'])->name('it-repair-tickets.repair');
		Route::put('/it-repair-tickets/{itRepairTicket}/repair', [ItRepairTicketController::class, 'updateRepair'])->name('it-repair-tickets.update-repair');
		Route::get('/it-repair-tickets/{itRepairTicket}/edit', [ItRepairTicketController::class, 'edit'])->name('it-repair-tickets.edit');
		Route::put('/it-repair-tickets/{itRepairTicket}', [ItRepairTicketController::class, 'update'])->name('it-repair-tickets.update');
		Route::delete('/it-repair-tickets/{itRepairTicket}', [ItRepairTicketController::class, 'destroy'])->name('it-repair-tickets.destroy');

		Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
		Route::get('/web-monitoring', [DashboardController::class, 'monitoring'])->name('web-monitoring.index');
		Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
		Route::post('/dashboard/check-now', [DashboardController::class, 'checkNow'])->name('dashboard.check-now');

		Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
		Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
		Route::patch('/sites/{site}/toggle', [SiteController::class, 'toggle'])->name('sites.toggle');

		Route::resource('web-monitoring-checklists', WebMonitoringChecklistController::class)
			->only(['index', 'create', 'store', 'show', 'destroy'])
			->parameters(['web-monitoring-checklists' => 'webMonitoringChecklist']);

		// Equipment routes
		Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
		Route::get('/equipments/create', [EquipmentController::class, 'create'])->name('equipments.create');
		Route::post('/equipments', [EquipmentController::class, 'store'])->name('equipments.store');
		Route::get('/equipments/{equipment}/label', [EquipmentController::class, 'label'])->name('equipments.label');
		Route::get('/equipments/{equipment}', [EquipmentController::class, 'show'])->name('equipments.show');
		Route::get('/equipments/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipments.edit');
		Route::put('/equipments/{equipment}', [EquipmentController::class, 'update'])->name('equipments.update');
		Route::delete('/equipments/{equipment}', [EquipmentController::class, 'destroy'])->name('equipments.destroy');

		Route::get('/equipment-transfers', [EquipmentTransferController::class, 'index'])->name('equipment-transfers.index');
		Route::get('/equipment-transfers/create', [EquipmentTransferController::class, 'create'])->name('equipment-transfers.create');
		Route::post('/equipment-transfers', [EquipmentTransferController::class, 'store'])->name('equipment-transfers.store');
		Route::get('/equipment-transfers/{equipmentTransfer}/print', [EquipmentTransferController::class, 'print'])->name('equipment-transfers.print');
		Route::get('/equipment-transfers/{equipmentTransfer}', [EquipmentTransferController::class, 'show'])->name('equipment-transfers.show');
		Route::post('/equipment-transfers/{equipmentTransfer}/complete', [EquipmentTransferController::class, 'complete'])->name('equipment-transfers.complete');

		// Pelaksanaan perawatan
		Route::get('/maintenances/checklists', [MaintenanceController::class, 'checklists'])->name('maintenances.checklists');
		Route::post('/maintenances/logs', [MaintenanceController::class, 'storeLog'])->name('maintenances.store_log');
		Route::get('/maintenances/grid', [MaintenanceController::class, 'grid'])->name('maintenances.grid');

		Route::resource('maintenance-checklists', MaintenanceChecklistController::class)
			->parameters(['maintenance-checklists' => 'maintenanceChecklist']);

		// Laporan jadwal
		Route::get('/reports/schedules/annual', [ScheduleReportController::class, 'annual'])->name('reports.annual');
		Route::get('/reports/schedules/monthly', [ScheduleReportController::class, 'monthly'])->name('reports.monthly');

		// Laporan operasional
		Route::get('/reports/equipments', [\App\Http\Controllers\ReportController::class, 'equipments'])->name('reports.equipments');
		Route::get('/reports/repairs', [\App\Http\Controllers\ReportController::class, 'repairs'])->name('reports.repairs');
		Route::get('/reports/checklists', [\App\Http\Controllers\ReportController::class, 'checklists'])->name('reports.checklists');

		// Masters: manufacturers, locations, equipment types, checklist items
		Route::prefix('masters')->name('masters.')->group(function () {
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

			Route::get('/equipment-types', [\App\Http\Controllers\EquipmentTypeController::class, 'index'])->name('equipment-types.index');
			Route::get('/equipment-types/create', [\App\Http\Controllers\EquipmentTypeController::class, 'create'])->name('equipment-types.create');
			Route::post('/equipment-types', [\App\Http\Controllers\EquipmentTypeController::class, 'store'])->name('equipment-types.store');
			Route::get('/equipment-types/{equipmentType}/edit', [\App\Http\Controllers\EquipmentTypeController::class, 'edit'])->name('equipment-types.edit');
			Route::put('/equipment-types/{equipmentType}', [\App\Http\Controllers\EquipmentTypeController::class, 'update'])->name('equipment-types.update');
			Route::delete('/equipment-types/{equipmentType}', [\App\Http\Controllers\EquipmentTypeController::class, 'destroy'])->name('equipment-types.destroy');

			Route::get('/checklist-items', [\App\Http\Controllers\ChecklistItemController::class, 'index'])->name('checklist-items.index');
			Route::get('/checklist-items/create', [\App\Http\Controllers\ChecklistItemController::class, 'create'])->name('checklist-items.create');
			Route::post('/checklist-items', [\App\Http\Controllers\ChecklistItemController::class, 'store'])->name('checklist-items.store');
			Route::get('/checklist-items/{item}/edit', [\App\Http\Controllers\ChecklistItemController::class, 'edit'])->name('checklist-items.edit');
			Route::put('/checklist-items/{item}', [\App\Http\Controllers\ChecklistItemController::class, 'update'])->name('checklist-items.update');
			Route::delete('/checklist-items/{item}', [\App\Http\Controllers\ChecklistItemController::class, 'destroy'])->name('checklist-items.destroy');
		});
	});

	// Khusus Master: menu jadwal, log aktivitas & pengaturan user
	Route::middleware('role:master')->group(function () {
		Route::post('/equipment-transfers/{equipmentTransfer}/approve', [EquipmentTransferController::class, 'approve'])->name('equipment-transfers.approve');
		Route::delete('/equipment-transfers/{equipmentTransfer}', [EquipmentTransferController::class, 'destroy'])->name('equipment-transfers.destroy');
		Route::post('/it-repair-tickets/{itRepairTicket}/approve', [ItRepairTicketController::class, 'approve'])->name('it-repair-tickets.approve');
		Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
		Route::get('/reports/activities', [\App\Http\Controllers\ReportController::class, 'activities'])->name('reports.activities');

		Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
		Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
		Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
		Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
		Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
		Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

		Route::get('/maintenances/schedules', [MaintenanceController::class, 'schedules'])->name('maintenances.schedules');
		Route::get('/maintenances/schedules/create', [MaintenanceController::class, 'createSchedule'])->name('maintenances.create_schedule');
		Route::post('/maintenances/schedules', [MaintenanceController::class, 'storeSchedule'])->name('maintenances.store_schedule');
		Route::get('/maintenances/schedules/new', [MaintenanceController::class, 'create'])->name('maintenances.schedules.new');
		Route::get('/maintenances/schedules/{checklistItem}', [MaintenanceController::class, 'showSchedule'])->name('maintenances.schedules.show');
		Route::get('/maintenances/schedules/{checklistItem}/edit', [MaintenanceController::class, 'editSchedule'])->name('maintenances.schedules.edit');
		Route::put('/maintenances/schedules/{checklistItem}', [MaintenanceController::class, 'updateSchedule'])->name('maintenances.schedules.update');
		Route::delete('/maintenances/schedules/{checklistItem}', [MaintenanceController::class, 'destroySchedule'])->name('maintenances.schedules.destroy');
		Route::get('/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');

		// Monthly schedules
		Route::get('/monthly-schedules', [MonthlyScheduleController::class, 'index'])->name('monthly_schedules.index');
		Route::get('/monthly-schedules/create', [MonthlyScheduleController::class, 'create'])->name('monthly_schedules.create');
		Route::post('/monthly-schedules', [MonthlyScheduleController::class, 'store'])->name('monthly_schedules.store');
		Route::get('/monthly-schedules/{checklistItemId}/{year}/months', [MonthlyScheduleController::class, 'selectMonths'])->name('monthly_schedules.select_months');
		Route::get('/monthly-schedules/{checklistItemId}/{year}/edit', [MonthlyScheduleController::class, 'edit'])->name('monthly_schedules.edit');
		Route::get('/monthly-schedules/{checklistItemId}/{year}', [MonthlyScheduleController::class, 'show'])->name('monthly_schedules.show');
		Route::delete('/monthly-schedules/{checklistItemId}/{year}', [MonthlyScheduleController::class, 'destroy'])->name('monthly_schedules.destroy');
	});
});
