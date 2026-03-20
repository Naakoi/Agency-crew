<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Hotel;
use App\Models\Crew;
use App\Models\Booking;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Staff account
        $admin = User::create([
            'name' => 'Agency Admin',
            'email' => 'admin@cppltd.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
        ]);

        $staff = User::create([
            'name' => 'Agency Staff',
            'email' => 'staff@cppltd.com',
            'password' => Hash::make('staff1234'),
            'role' => 'staff',
        ]);

        // Sample companies
        $company = Company::create(['company_name' => 'Pacific Fishing Co.', 'ship_name' => 'MV Pacific Star', 'contact' => '+686 71234']);
        $company2 = Company::create(['company_name' => 'Ocean Harvest Ltd.', 'ship_name' => 'MV Sea Eagle', 'contact' => '+686 73456']);

        // Sample hotels
        $hotel1 = Hotel::create(['hotel_name' => 'Betio Inn', 'location' => 'Betio, Tarawa', 'contact' => '+686 26000']);
        $hotel2 = Hotel::create(['hotel_name' => 'Captain\'s Lodge', 'location' => 'Bairiki, Tarawa', 'contact' => '+686 21000']);

        // Sample crews
        $crew1 = Crew::create(['full_name' => 'Taaroaiti Tearo', 'nationality' => 'Kiribati', 'passport_number' => 'KI123456', 'date_of_birth' => '1985-04-12']);
        $crew2 = Crew::create(['full_name' => 'Nei Mikuero Rikiaua', 'nationality' => 'Kiribati', 'passport_number' => 'KI789012', 'date_of_birth' => '1990-07-22']);
        $crew3 = Crew::create(['full_name' => 'James Tong Foon', 'nationality' => 'Chinese', 'passport_number' => 'CN345678', 'date_of_birth' => '1978-11-05']);

        // Sample bookings
        Booking::create([
            'crew_id' => $crew1->id, 'company_id' => $company->id, 'hotel_id' => $hotel1->id,
            'crew_title' => 'Captain', 'check_in' => now()->subDays(3), 'check_out' => now()->addDays(4),
            'invoice_number' => 'INV-2026-001', 'remarks' => 'VIP guest, needs quiet room.', 'status' => 'in_hotel',
        ]);
        Booking::create([
            'crew_id' => $crew2->id, 'company_id' => $company->id, 'hotel_id' => $hotel2->id,
            'crew_title' => 'Engineer', 'check_in' => now()->subDays(10), 'check_out' => now()->subDays(2),
            'invoice_number' => 'INV-2026-002', 'remarks' => 'Departed on schedule.', 'status' => 'departed',
        ]);
        Booking::create([
            'crew_id' => $crew3->id, 'company_id' => $company2->id, 'hotel_id' => $hotel1->id,
            'crew_title' => 'Chief Mate', 'check_in' => now()->subDay(), 'check_out' => now()->addDays(7),
            'invoice_number' => 'INV-2026-003', 'remarks' => '', 'status' => 'in_hotel',
        ]);
    }
}
