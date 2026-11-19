<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Demo 4 Role (Password semua akun: password)
        $superAdmin = User::create([
            'name' => 'Super Admin Tix',
            'email' => 'superadmin@tixandyou.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone' => '081200000001',
        ]);

        $admin = User::create([
            'name' => 'Admin Operasional',
            'email' => 'admin@tixandyou.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081200000002',
        ]);

        $eo = User::create([
            'name' => 'Rizky Pratama',
            'email' => 'eo@tixandyou.id',
            'password' => Hash::make('password'),
            'role' => 'eo',
            'phone' => '081234567890',
            'organization_name' => 'Sinergi Event Organizer',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Sinergi Event Organizer',
        ]);

        $buyer = User::create([
            'name' => 'Andi Saputra',
            'email' => 'buyer@tixandyou.id',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081298765432',
        ]);

        // 2. 6 Event + Kategori Tiket
        $eventsData = [
            [
                'title' => 'Bandung Indie Vibes Vol. 1',
                'slug' => 'bandung-indie-vibes',
                'category' => 'Musik & Konser',
                'description' => 'Bandung Indie Vibes Vol. 1 adalah perayaan musik independen paling intim di Kota Kembang tahun ini. Menghadirkan deretan musisi folk, pop-alternatif, dan indie-rock kebanggaan lokal dalam satu panggung terbuka yang hangat.',
                'lineup' => ['Senja di Dago', 'Kala & Rindu', 'Ruang Hening'],
                'event_date' => '2026-11-21',
                'start_time' => '18:30',
                'end_time' => '23:00',
                'open_gate' => '17:00',
                'venue_name' => 'Lapangan PPI Pussenif',
                'venue_address' => 'Jl. Supratman No. 60, Kota Bandung',
                'categories' => [
                    ['name' => 'Early Bird', 'price' => 150000, 'quota' => 100, 'sold' => 65],
                    ['name' => 'Reguler', 'price' => 200000, 'quota' => 250, 'sold' => 42],
                ],
            ],
            [
                'title' => 'Symphony of The Night Jakarta',
                'slug' => 'symphony-of-the-night',
                'category' => 'Musik & Konser',
                'description' => 'Symphony of The Night Jakarta memadukan aransemen orkestra modern dengan kolaborasi vokalis pop papan atas dalam suasana panggung megah bertata cahaya sinematik.',
                'lineup' => ['Jakarta Chamber Ensemble', 'Nadia Larasati', 'Raka & The Strings'],
                'event_date' => '2026-11-29',
                'start_time' => '19:00',
                'end_time' => '23:00',
                'open_gate' => '17:30',
                'venue_name' => 'Istora Senayan',
                'venue_address' => 'Jl. Pintu Satu Senayan, Jakarta Pusat',
                'categories' => [
                    ['name' => 'Festival Standing', 'price' => 275000, 'quota' => 300, 'sold' => 120],
                    ['name' => 'Tribune VIP', 'price' => 450000, 'quota' => 150, 'sold' => 50],
                ],
            ],
            [
                'title' => 'Festival Kuliner & Musik Nusantara',
                'slug' => 'festival-kuliner-nusantara',
                'category' => 'Festival',
                'description' => 'Nikmati lebih dari 80 tenant kuliner legendaris dari Sabang sampai Merauke yang dikurasi langsung untuk memanjakan lidah pecinta jajanan dan hidangan autentik Nusantara.',
                'lineup' => ['80+ Tenant Kuliner Nusantara', 'Chef Dimas Live Cooking', 'Orkes Sinten Remen'],
                'event_date' => '2026-12-05',
                'start_time' => '10:00',
                'end_time' => '22:00',
                'open_gate' => '09:30',
                'venue_name' => 'Summarecon Mall Bekasi',
                'venue_address' => 'Jl. Boulevard Ahmad Yani, Kota Bekasi',
                'categories' => [
                    ['name' => 'Daily Pass', 'price' => 50000, 'quota' => 500, 'sold' => 210],
                ],
            ],
            [
                'title' => 'Jakarta Creative & Startup Summit',
                'slug' => 'jakarta-creative-summit',
                'category' => 'Workshop',
                'description' => 'Konferensi dan lokakarya praktis bagi pelaku industri kreatif, pegiat UMKM digital, dan perintis startup untuk membahas strategi pertumbuhan produk di era teknologi.',
                'lineup' => ['Andra Wijaya (Product Lead)', 'Sinta Maharani (Creative Director)', 'Bima Pratama (Angel Investor)'],
                'event_date' => '2026-12-09',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'open_gate' => '08:15',
                'venue_name' => 'Jakarta Convention Center',
                'venue_address' => 'Jl. Gatot Subroto, Senayan, Jakarta Pusat',
                'categories' => [
                    ['name' => 'General Pass', 'price' => 200000, 'quota' => 200, 'sold' => 88],
                ],
            ],
            [
                'title' => 'Acoustic Night Under The Stars',
                'slug' => 'acoustic-night-stars',
                'category' => 'Musik & Konser',
                'description' => 'Pertunjukan musik akustik di ruang terbuka hijau dengan konsep piknik malam di bawah langit Jakarta. Suasana santai dan akrab menjadikan jarak antara musisi dan penonton terasa dekat.',
                'lineup' => ['Payung Teduh Tribute', 'Ardhito & Friends Acoustic', 'Laras Senja'],
                'event_date' => '2026-12-12',
                'start_time' => '19:30',
                'end_time' => '23:00',
                'open_gate' => '18:00',
                'venue_name' => 'Hutan Kota GBK',
                'venue_address' => 'Jl. Jenderal Sudirman, Jakarta Pusat',
                'categories' => [
                    ['name' => 'Picnic Area', 'price' => 125000, 'quota' => 200, 'sold' => 95],
                ],
            ],
            [
                'title' => 'Year End Electronic Music Fest',
                'slug' => 'year-end-electronic',
                'category' => 'Festival',
                'description' => 'Sambut pergantian tahun dengan festival musik elektronik berlatar tebing megah Garuda Wisnu Kencana, lengkap dengan tata suara imersif dan pertunjukan visual laser.',
                'lineup' => ['DJ Kaelen', 'Neon Mirage', 'Bali Bass Collective'],
                'event_date' => '2026-12-31',
                'start_time' => '20:00',
                'end_time' => '02:00',
                'open_gate' => '18:30',
                'venue_name' => 'GWK Cultural Park',
                'venue_address' => 'Jl. Raya Uluwatu, Ungasan, Badung, Bali',
                'categories' => [
                    ['name' => 'General Admission', 'price' => 350000, 'quota' => 400, 'sold' => 175],
                ],
            ],
        ];

        $firstEvent = null;
        $firstCategory = null;

        foreach ($eventsData as $idx => $data) {
            $categories = $data['categories'];
            unset($data['categories']);

            $event = Event::create(array_merge($data, [
                'eo_id' => $eo->id,
                'status' => 'published',
            ]));

            foreach ($categories as $cIdx => $cat) {
                $createdCat = TicketCategory::create(array_merge($cat, [
                    'event_id' => $event->id,
                ]));

                if ($idx === 0 && $cIdx === 0) {
                    $firstEvent = $event;
                    $firstCategory = $createdCat;
                }
            }
        }

        // 3. Contoh 1 Transaksi Lunas & 1 E-Ticket Siap Scan untuk Uji Web Scanner
        $order = Order::create([
            'order_code' => 'ORD-20261117-0001',
            'user_id' => $buyer->id,
            'event_id' => $firstEvent->id,
            'buyer_name' => $buyer->name,
            'buyer_email' => $buyer->email,
            'buyer_phone' => $buyer->phone,
            'total_amount' => 157500,
            'platform_fee' => 7500,
            'payment_method' => 'QRIS',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        Ticket::create([
            'order_id' => $order->id,
            'ticket_category_id' => $firstCategory->id,
            'event_id' => $firstEvent->id,
            'ticket_code' => 'TIX-BDG-2026-0001',
            'attendee_name' => $buyer->name,
            'status' => 'valid',
        ]);

        AuditLog::create([
            'user_id' => $superAdmin->id,
            'action' => 'SYSTEM_SEED',
            'description' => 'Inisialisasi data awal sistem Tix and You berhasil dijalankan.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}