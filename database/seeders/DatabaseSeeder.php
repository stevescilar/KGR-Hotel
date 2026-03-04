<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\{User, RoomType, Room, TicketType, EventPackage, MenuCategory, MenuItem, Table, JobListing, Setting};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Settings ──────────────────────────────────────────
        $settings = [
            ['key' => 'hotel_name',        'value' => 'Kitonga Garden Resort', 'group' => 'general'],
            ['key' => 'hotel_email',       'value' => 'info@kitongagardenresort.com', 'group' => 'general'],
            ['key' => 'hotel_phone',       'value' => '+254 113 262 688', 'group' => 'general'],
            ['key' => 'hotel_address',     'value' => 'Thika–Garissa Road, Ukasi, Kitui County, Kenya', 'group' => 'general'],
            ['key' => 'check_in_time',     'value' => '14:00', 'group' => 'operations'],
            ['key' => 'check_out_time',    'value' => '11:00', 'group' => 'operations'],
            ['key' => 'vat_rate',          'value' => '16', 'group' => 'finance'],
            ['key' => 'loyalty_rate',      'value' => '1', 'group' => 'loyalty'],  // 1 point per KES 100
            ['key' => 'currency',          'value' => 'KES', 'group' => 'finance'],
            ['key' => 'mpesa_shortcode',   'value' => '174379', 'group' => 'payments'],
        ];
        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        // ── Users ─────────────────────────────────────────────
        $users = [
            ['name' => 'Super Admin',    'email' => 'admin@kgr.co.ke',       'role' => 'super_admin'],
            ['name' => 'James Mutua',    'email' => 'james@kgr.co.ke',       'role' => 'manager'],
            ['name' => 'Grace Wanjiku',  'email' => 'grace@kgr.co.ke',       'role' => 'receptionist'],
            ['name' => 'Peter Nzomo',    'email' => 'peter@kgr.co.ke',       'role' => 'fnb_staff'],
            ['name' => 'Ann Muthoni',    'email' => 'ann@kgr.co.ke',         'role' => 'housekeeper'],
            ['name' => 'HR Admin',       'email' => 'hr@kgr.co.ke',          'role' => 'hr_admin'],
        ];
        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], [
                'name'     => $u['name'],
                'password' => Hash::make('password'),
            ]);
        }

        // ── Room Types ────────────────────────────────────────
        $roomTypes = [
            [
                'name' => 'Standard Room',
                'slug' => 'standard',
                'description' => 'Private balcony overlooking beautiful gardens, luxury bathrooms with shower, choice of king or queen bed.',
                'max_adults' => 2, 'max_children' => 1,
                'base_price' => 8500, 'weekend_price' => 9500,
                'amenities' => ['WiFi','Balcony','Air Conditioning','En-suite Bathroom','Safe','Flat Screen TV','Mini Fridge'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Deluxe Room',
                'slug' => 'deluxe',
                'description' => 'Broad balcony with seating, luxury bathrooms with indoor and outdoor shower, king or twin beds.',
                'max_adults' => 2, 'max_children' => 2,
                'base_price' => 12500, 'weekend_price' => 14000,
                'amenities' => ['WiFi','Large Balcony','Air Conditioning','Outdoor Shower','King Bed','Seating Area','Mini Bar'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Penthouse Suite',
                'slug' => 'penthouse',
                'description' => 'Top floor luxury with breathtaking views of gardens, Yatta Plateau, and select Mount Kenya panoramas.',
                'max_adults' => 2, 'max_children' => 2,
                'base_price' => 18500, 'weekend_price' => 22000,
                'amenities' => ['WiFi','Panoramic Views','Air Conditioning','Queen Bed','Sofa Bed','Luxury Bath','Coffee Machine','Mount Kenya View'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Presidential Family Suite',
                'slug' => 'presidential-family',
                'description' => 'Spacious suite with lounge, kitchenette, outdoor showers in each bedroom, sofa beds, and complimentary fruit basket.',
                'max_adults' => 4, 'max_children' => 2,
                'base_price' => 28000, 'weekend_price' => 32000,
                'amenities' => ['WiFi','Kitchenette','Lounge','Outdoor Shower','Fruit Basket','2 Bedrooms','Sofa Beds','Garden View'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Royal Presidential Suite',
                'slug' => 'royal-presidential',
                'description' => 'Ultimate luxury with private balcony, study, closet, bath, king bed, and complimentary fruit basket.',
                'max_adults' => 2, 'max_children' => 0,
                'base_price' => 38000, 'weekend_price' => 45000,
                'amenities' => ['WiFi','Study Room','Walk-in Closet','Luxury Bathtub','King Bed','Butler Service','Fruit Basket','Premium Minibar'],
                'sort_order' => 5,
            ],
        ];

        foreach ($roomTypes as $rt) {
            $type = RoomType::firstOrCreate(['slug' => $rt['slug']], $rt);

            // Create rooms per type
            $roomCounts = ['standard'=>6,'deluxe'=>6,'penthouse'=>3,'presidential-family'=>2,'royal-presidential'=>1];
            $count = $roomCounts[$rt['slug']] ?? 4;
            $cottages = ['A','B','C'];
            $floors = [1,2,3];
            $roomNum = Room::max(DB::raw('CAST(room_number AS UNSIGNED)')) ?? 100;

            for ($i = 0; $i < $count; $i++) {
                $roomNum++;
                Room::firstOrCreate(['room_number' => (string) $roomNum], [
                    'room_type_id' => $type->id,
                    'floor'        => $floors[$i % 3],
                    'cottage'      => 'Cottage ' . $cottages[$i % 3],
                    'status'       => $i < 2 ? 'occupied' : 'available',
                ]);
            }
        }

        // ── Ticket Types ──────────────────────────────────────
        $tickets = [
            ['name' => 'Adult',   'price' => 1500, 'min_age' => 13],
            ['name' => 'Child',   'price' => 800,  'min_age' => 3, 'max_age' => 12],
            ['name' => 'Infant',  'price' => 0,    'max_age' => 2],
            ['name' => 'Student', 'price' => 1000, 'description' => 'Valid student ID required'],
            ['name' => 'Group (10+)', 'price' => 1200, 'description' => 'Minimum 10 persons'],
        ];
        foreach ($tickets as $t) {
            TicketType::firstOrCreate(['name' => $t['name']], $t);
        }

        // ── Event Packages ────────────────────────────────────
        $packages = [
            [
                'name' => 'Garden Wedding',
                'slug' => 'garden-wedding',
                'description' => 'An enchanting outdoor ceremony surrounded by lush gardens, perfect for your dream wedding day.',
                'starting_price' => 250000,
                'min_guests' => 50, 'max_guests' => 300,
                'inclusions' => ['Venue decoration','Catering (2 courses)','Sound system','Bridal suite stay','Wedding cake','Flowers'],
            ],
            [
                'name' => 'Corporate Conference',
                'slug' => 'conference',
                'description' => 'Professional conference and team-building packages with full AV setup.',
                'starting_price' => 45000,
                'min_guests' => 20, 'max_guests' => 150,
                'inclusions' => ['Conference room','Projector & screen','Tea breaks (x2)','Buffet lunch','WiFi','Notepad & pens'],
            ],
            [
                'name' => 'Birthday Celebration',
                'slug' => 'birthday',
                'description' => 'Make your special day unforgettable with our tailored birthday packages.',
                'starting_price' => 35000,
                'min_guests' => 20, 'max_guests' => 100,
                'inclusions' => ['Venue decoration','Custom cake','DJ/Music','Buffet dinner','Photo booth','Party favors'],
            ],
        ];
        foreach ($packages as $p) {
            EventPackage::firstOrCreate(['slug' => $p['slug']], $p);
        }

        // ── Menu ──────────────────────────────────────────────
        $categories = [
            ['name' => 'Breakfast',  'slug' => 'breakfast',  'type' => 'food',   'icon' => '🥞'],
            ['name' => 'Starters',   'slug' => 'starters',   'type' => 'food',   'icon' => '🥗'],
            ['name' => 'Main Course','slug' => 'mains',      'type' => 'food',   'icon' => '🍖'],
            ['name' => 'Nyama Choma','slug' => 'nyama-choma','type' => 'food',   'icon' => '🔥'],
            ['name' => 'Desserts',   'slug' => 'desserts',   'type' => 'desserts','icon' => '🍰'],
            ['name' => 'Soft Drinks','slug' => 'soft-drinks','type' => 'drinks', 'icon' => '🥤'],
            ['name' => 'Cocktails',  'slug' => 'cocktails',  'type' => 'drinks', 'icon' => '🍹'],
            ['name' => 'Beer & Wine','slug' => 'beer-wine',  'type' => 'drinks', 'icon' => '🍷'],
        ];
        foreach ($categories as $i => $c) {
            $cat = MenuCategory::firstOrCreate(['slug' => $c['slug']], array_merge($c, ['sort_order' => $i]));
        }

        // ── Restaurant Tables ──────────────────────────────────
        $tableSections = [
            ['section' => 'Garden', 'count' => 8,  'capacity' => 4],
            ['section' => 'Indoor', 'count' => 6,  'capacity' => 4],
            ['section' => 'Poolside','count' => 4, 'capacity' => 6],
            ['section' => 'VIP',    'count' => 2,  'capacity' => 8],
        ];
        $tableNum = 1;
        foreach ($tableSections as $s) {
            for ($i = 0; $i < $s['count']; $i++) {
                Table::firstOrCreate(['table_number' => 'T' . str_pad($tableNum, 2, '0', STR_PAD_LEFT)], [
                    'capacity' => $s['capacity'],
                    'section'  => $s['section'],
                    'status'   => 'available',
                ]);
                $tableNum++;
            }
        }

        // ── Job Listings ──────────────────────────────────────
        $jobs = [
            ['title' => 'Front Desk Receptionist', 'department' => 'Front Office', 'type' => 'full_time', 'salary_min' => 35000, 'salary_max' => 45000],
            ['title' => 'Head Chef',               'department' => 'Food & Beverage', 'type' => 'full_time', 'salary_min' => 60000, 'salary_max' => 80000],
            ['title' => 'Housekeeper',             'department' => 'Housekeeping',   'type' => 'full_time', 'salary_min' => 25000, 'salary_max' => 30000],
            ['title' => 'Events Coordinator',      'department' => 'Events',          'type' => 'full_time', 'salary_min' => 45000, 'salary_max' => 60000],
            ['title' => 'Security Guard',          'department' => 'Security',        'type' => 'full_time', 'salary_min' => 22000, 'salary_max' => 28000],
        ];
        foreach ($jobs as $j) {
            JobListing::firstOrCreate(['title' => $j['title']], array_merge($j, [
                'location'     => 'Kitonga Garden Resort, Ukasi',
                'description'  => "We are looking for a motivated {$j['title']} to join our growing team at Kitonga Garden Resort.",
                'requirements' => "• Relevant diploma or degree\n• Minimum 2 years experience\n• Strong communication skills\n• Team player",
                'closing_date' => now()->addDays(30),
                'is_active'    => true,
            ]));
        }

        $this->call(RolesAndPermissionsSeeder::class);

        $this->command->info('✅ KGR database seeded successfully!');
        $this->command->table(['Role', 'Email', 'Password'], [
            ['Super Admin',  'admin@kgr.co.ke',  'password'],
            ['Manager',      'james@kgr.co.ke',  'password'],
            ['Receptionist', 'grace@kgr.co.ke',  'password'],
            ['F&B Staff',    'peter@kgr.co.ke',  'password'],
            ['Housekeeper',  'ann@kgr.co.ke',    'password'],
            ['HR Admin',     'hr@kgr.co.ke',     'password'],
        ]);
    }
}
