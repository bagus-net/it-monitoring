<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\ItRepairTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItRepairTicketOwnerSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_keeps_original_pic_after_equipment_transfer(): void
    {
        $owner = User::create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'secret123',
            'role' => User::ROLE_USER,
            'department' => 'IT',
            'is_active' => true,
        ]);

        $receiver = User::create([
            'name' => 'Tono',
            'email' => 'tono@example.com',
            'password' => 'secret123',
            'role' => User::ROLE_USER,
            'department' => 'HR',
            'is_active' => true,
        ]);

        $equipment = Equipment::create([
            'name' => 'Laptop A',
            'user_id' => $owner->id,
            'owner_name' => 'Budi',
            'department' => 'IT',
        ]);

        $ticket = ItRepairTicket::create([
            'ticket_number' => 'IT-2026-0001',
            'equipment_id' => $equipment->id,
            'repair_category' => 'hardware',
            'equipment_category' => 'Laptop',
            'reported_at' => now(),
            'problem_description' => 'Layar mati',
            'priority' => 'normal',
            'status' => 'open',
            'reported_by' => 'Budi',
            'department' => 'IT',
            'user_id' => $owner->id,
            'equipment_owner_user_id' => $equipment->user_id,
            'equipment_owner_name' => $equipment->owner_name,
            'equipment_owner_department' => $equipment->department,
        ]);

        $equipment->update([
            'user_id' => $receiver->id,
            'owner_name' => 'Tono',
            'department' => 'HR',
        ]);

        $this->assertSame('Budi', $ticket->fresh()->equipment_owner_name);
        $this->assertSame('Budi', $ticket->fresh()->snapshotOwnerName());
    }
}
