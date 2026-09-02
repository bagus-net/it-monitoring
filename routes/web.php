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
use App\Http\Controllers\InkController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\NetworkTopologyController;
use App\Http\Controllers\CctvController;
use App\Http\Controllers\InnovationController;
use App\Http\Controllers\ItWasteController;
use App\Http\Controllers\IsoDocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecycleBinController;
use App\Http\Controllers\TargetMonitoringController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\TodoListController;
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
	Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
	Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
	Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
	Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
	Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');

	// Tiket perbaikan IT: semua level boleh mengakses, karyawan hanya melihat tiket peralatannya sendiri
	Route::get('/it-repair-tickets/notifications', [ItRepairTicketController::class, 'notifications'])->name('it-repair-tickets.notifications');	Route::get('/it-repair-tickets', [ItRepairTicketController::class, 'index'])->name('it-repair-tickets.index');
	Route::get('/equipment-transfers/notifications', [EquipmentTransferController::class, 'notifications'])->name('equipment-transfers.notifications');
	Route::get('/it-repair-tickets/create', [ItRepairTicketController::class, 'create'])->name('it-repair-tickets.create');
	Route::post('/it-repair-tickets', [ItRepairTicketController::class, 'store'])->name('it-repair-tickets.store');
	Route::get('/it-repair-tickets/{itRepairTicket}', [ItRepairTicketController::class, 'show'])->name('it-repair-tickets.show');

	Route::get('/iso-documents/{isoDocument}/download', [IsoDocumentController::class, 'download'])->name('iso-documents.download');
	Route::get('/iso-documents/{isoDocument}/preview', [IsoDocumentController::class, 'preview'])->name('iso-documents.preview');
	Route::resource('iso-documents', IsoDocumentController::class);

	// Master + Admin IT
	Route::middleware('role:master,admin_it')->group(function () {
		Route::get('/target-monitorings', [TargetMonitoringController::class, 'index'])->name('target-monitorings.index');
		Route::post('/target-monitorings/manual', [TargetMonitoringController::class, 'updateManual'])->name('target-monitorings.manual.update');
		Route::get('/it-repair-tickets/{itRepairTicket}/repair', [ItRepairTicketController::class, 'repair'])->name('it-repair-tickets.repair');
		Route::put('/it-repair-tickets/{itRepairTicket}/repair', [ItRepairTicketController::class, 'updateRepair'])->name('it-repair-tickets.update-repair');
		Route::get('/it-repair-tickets/{itRepairTicket}/edit', [ItRepairTicketController::class, 'edit'])->name('it-repair-tickets.edit');
		Route::put('/it-repair-tickets/{itRepairTicket}', [ItRepairTicketController::class, 'update'])->name('it-repair-tickets.update');
		Route::delete('/it-repair-tickets/{itRepairTicket}', [ItRepairTicketController::class, 'destroy'])->name('it-repair-tickets.destroy');

		Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
		Route::middleware('role:master')->group(function () {
			Route::resource('campaigns', CampaignController::class);
			Route::resource('todo-list', TodoListController::class)->except('show')->parameters(['todo-list' => 'todoList']);
			Route::post('/campaigns/{campaign}/tasks', [CampaignController::class, 'storeTask'])->name('campaigns.tasks.store');
			Route::put('/campaigns/{campaign}/tasks/{campaignTask}', [CampaignController::class, 'updateTask'])->name('campaigns.tasks.update');
			Route::delete('/campaigns/{campaign}/tasks/{campaignTask}', [CampaignController::class, 'destroyTask'])->name('campaigns.tasks.destroy');
		});
		Route::get('/web-monitoring', [DashboardController::class, 'monitoring'])->name('web-monitoring.index');
		Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
		Route::post('/dashboard/check-now', [DashboardController::class, 'checkNow'])->name('dashboard.check-now');

		Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
		Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
		Route::patch('/sites/{site}/toggle', [SiteController::class, 'toggle'])->name('sites.toggle');

		Route::resource('web-monitoring-checklists', WebMonitoringChecklistController::class)
			->only(['index', 'create', 'store', 'show', 'destroy'])
			->parameters(['web-monitoring-checklists' => 'webMonitoringChecklist']);

		Route::resource('innovations', InnovationController::class);

		Route::get('/it-wastes/handover/print', [ItWasteController::class, 'printHandover'])->name('it-wastes.print-handover');
		Route::get('/it-wastes/batches/{itWasteBatch}', [ItWasteController::class, 'show'])->name('it-wastes.show');
		Route::post('/it-wastes/batches/{itWasteBatch}/wastes', [ItWasteController::class, 'storeWaste'])->name('it-wastes.wastes.store');
		Route::put('/it-wastes/batches/{itWasteBatch}', [ItWasteController::class, 'updateBatch'])->name('it-wastes.batches.update');
		Route::delete('/it-wastes/batches/{itWasteBatch}', [ItWasteController::class, 'destroyBatch'])->name('it-wastes.batches.destroy');
		Route::resource('it-wastes', ItWasteController::class)->except('show');

		// Equipment routes
		Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipments.index');
		Route::get('/equipments/create', [EquipmentController::class, 'create'])->name('equipments.create');
		Route::post('/equipments', [EquipmentController::class, 'store'])->name('equipments.store');
		Route::get('/equipments/{equipment}/label', [EquipmentController::class, 'label'])->name('equipments.label');
		Route::get('/equipments/{equipment}/label/download', [EquipmentController::class, 'downloadLabel'])->name('equipments.label.download');
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

		Route::get('/ink', [InkController::class, 'index'])->name('ink.index');
		Route::post('/ink/types', [InkController::class, 'storeType'])->name('ink.types.store');
		Route::put('/ink/types/{inkType}', [InkController::class, 'updateType'])->name('ink.types.update');
		Route::delete('/ink/types/{inkType}', [InkController::class, 'destroyType'])->name('ink.types.destroy');
		Route::post('/ink/transactions', [InkController::class, 'storeTransaction'])->name('ink.transactions.store');
		Route::get('/spareparts', [SparepartController::class, 'index'])->name('spareparts.index');
		Route::post('/spareparts/types', [SparepartController::class, 'storeType'])->name('spareparts.types.store');
		Route::put('/spareparts/types/{sparepartType}', [SparepartController::class, 'updateType'])->name('spareparts.types.update');
		Route::delete('/spareparts/types/{sparepartType}', [SparepartController::class, 'destroyType'])->name('spareparts.types.destroy');
		Route::post('/spareparts/transactions', [SparepartController::class, 'storeTransaction'])->name('spareparts.transactions.store');
		Route::get('/licenses', [LicenseController::class, 'index'])->name('licenses.index');
		Route::post('/licenses/types', [LicenseController::class, 'storeType'])->name('licenses.types.store');
		Route::put('/licenses/types/{licenseType}', [LicenseController::class, 'updateType'])->name('licenses.types.update');
		Route::delete('/licenses/types/{licenseType}', [LicenseController::class, 'destroyType'])->name('licenses.types.destroy');
		Route::post('/licenses/transactions', [LicenseController::class, 'storeTransaction'])->name('licenses.transactions.store');
		Route::get('/cctv', [CctvController::class, 'index'])->name('cctv.index');
		Route::post('/cctv', [CctvController::class, 'store'])->name('cctv.store');
		Route::put('/cctv/{cctv}', [CctvController::class, 'update'])->name('cctv.update');
		Route::patch('/cctv/{cctv}/toggle-status', [CctvController::class, 'toggleStatus'])->name('cctv.toggle-status');
		Route::delete('/cctv/{cctv}', [CctvController::class, 'destroy'])->name('cctv.destroy');
		Route::post('/cctv-connections', [CctvController::class, 'storeConnection'])->name('cctv.connections.store');
		Route::delete('/cctv-connections/{cctvConnection}', [CctvController::class, 'destroyConnection'])->name('cctv.connections.destroy');
		Route::get('/network-topology', [NetworkTopologyController::class, 'index'])->name('network.topology');
		Route::post('/network-topology/nodes', [NetworkTopologyController::class, 'storeNode'])->name('network.nodes.store');
		Route::put('/network-topology/nodes/{networkNode}', [NetworkTopologyController::class, 'updateNode'])->name('network.nodes.update');
		Route::delete('/network-topology/nodes/{networkNode}', [NetworkTopologyController::class, 'destroyNode'])->name('network.nodes.destroy');
		Route::post('/network-topology/links', [NetworkTopologyController::class, 'storeLink'])->name('network.links.store');
		Route::put('/network-topology/links/{networkLink}', [NetworkTopologyController::class, 'updateLink'])->name('network.links.update');
		Route::delete('/network-topology/links/{networkLink}', [NetworkTopologyController::class, 'destroyLink'])->name('network.links.destroy');
		Route::post('/network-topology/zones', [NetworkTopologyController::class, 'storeZone'])->name('network.zones.store');
		Route::delete('/network-topology/zones/{networkZone}', [NetworkTopologyController::class, 'destroyZone'])->name('network.zones.destroy');

		// Pelaksanaan perawatan
		Route::get('/maintenances/checklists', [MaintenanceController::class, 'checklists'])->name('maintenances.checklists');
		Route::post('/maintenances/logs', [MaintenanceController::class, 'storeLog'])->name('maintenances.store_log');
		Route::get('/maintenances/grid', [MaintenanceController::class, 'grid'])->name('maintenances.grid');

		Route::resource('maintenance-checklists', MaintenanceChecklistController::class)
			->parameters(['maintenance-checklists' => 'maintenanceChecklist']);
		Route::post('/maintenance-checklists/{maintenanceChecklist}/approve', [MaintenanceChecklistController::class, 'approve'])->name('maintenance-checklists.approve');

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
		Route::get('/recycle-bin', [RecycleBinController::class, 'index'])->name('recycle-bin.index');
		Route::post('/recycle-bin/{type}/{id}/restore', [RecycleBinController::class, 'restore'])->name('recycle-bin.restore');
		Route::delete('/recycle-bin/{type}/{id}', [RecycleBinController::class, 'forceDelete'])->name('recycle-bin.force-delete');
		Route::post('/equipment-transfers/{equipmentTransfer}/approve', [EquipmentTransferController::class, 'approve'])->name('equipment-transfers.approve');
		Route::delete('/equipment-transfers/{equipmentTransfer}', [EquipmentTransferController::class, 'destroy'])->name('equipment-transfers.destroy');
		Route::post('/it-repair-tickets/{itRepairTicket}/approve', [ItRepairTicketController::class, 'approve'])->name('it-repair-tickets.approve');
		Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
		Route::get('/reports/activities', [\App\Http\Controllers\ReportController::class, 'activities'])->name('reports.activities');

		Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
		Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
		Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
		Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
		Route::delete('/users/{user}/equipments/{equipment}', [\App\Http\Controllers\UserController::class, 'detachEquipment'])->name('users.equipments.detach');
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
		Route::get('/monthly-schedules/print', [MonthlyScheduleController::class, 'printMonth'])->name('monthly_schedules.print_month');
		Route::get('/monthly-schedules/create', [MonthlyScheduleController::class, 'create'])->name('monthly_schedules.create');
		Route::get('/monthly-schedules/template-dates', [MonthlyScheduleController::class, 'templateDates'])->name('monthly_schedules.template_dates');
		Route::post('/monthly-schedules', [MonthlyScheduleController::class, 'store'])->name('monthly_schedules.store');
		Route::get('/monthly-schedules/{checklistItemId}/{year}/months', [MonthlyScheduleController::class, 'selectMonths'])->name('monthly_schedules.select_months');
		Route::get('/monthly-schedules/{checklistItemId}/{year}/edit', [MonthlyScheduleController::class, 'edit'])->name('monthly_schedules.edit');
		Route::get('/monthly-schedules/{checklistItemId}/{year}', [MonthlyScheduleController::class, 'show'])->name('monthly_schedules.show');
		Route::delete('/monthly-schedules/{checklistItemId}/{year}', [MonthlyScheduleController::class, 'destroy'])->name('monthly_schedules.destroy');
	});
});
