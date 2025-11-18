<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = DB::table('customer_categories')->pluck('id', 'name');
        
        $customers = [
            [
                'name' => 'Acme Corporation',
                'reference' => 'CUST001',
                'customer_category_id' => $categories['Gold'],
                'start_date' => '2024-01-15',
                'description' => 'Leading technology company specializing in software solutions.',
            ],
            [
                'name' => 'Global Trading Ltd',
                'reference' => 'CUST002',
                'customer_category_id' => $categories['Silver'],
                'start_date' => '2024-02-20',
                'description' => 'International trading company with operations worldwide.',
            ],
            [
                'name' => 'TechStart Inc',
                'reference' => 'CUST003',
                'customer_category_id' => $categories['Bronze'],
                'start_date' => '2024-03-10',
                'description' => 'Startup company focused on innovative technology products.',
            ],
            [
                'name' => 'Mega Industries',
                'reference' => 'CUST004',
                'customer_category_id' => $categories['Gold'],
                'start_date' => '2024-01-05',
                'description' => 'Large industrial manufacturing company.',
            ],
            [
                'name' => 'Digital Solutions Co',
                'reference' => 'CUST005',
                'customer_category_id' => $categories['Silver'],
                'start_date' => '2024-04-12',
                'description' => 'Digital transformation consultancy firm.',
            ],
            [
                'name' => 'Prime Services',
                'reference' => 'CUST006',
                'customer_category_id' => $categories['Bronze'],
                'start_date' => '2024-05-18',
                'description' => 'Professional services provider.',
            ],
            [
                'name' => 'Elite Business Group',
                'reference' => 'CUST007',
                'customer_category_id' => $categories['Gold'],
                'start_date' => '2024-02-01',
                'description' => 'Premium business consulting and advisory services.',
            ],
            [
                'name' => 'Future Systems',
                'reference' => 'CUST008',
                'customer_category_id' => $categories['Silver'],
                'start_date' => '2024-06-25',
                'description' => 'IT systems integration and support services.',
            ],
            [
                'name' => 'Innovation Labs',
                'reference' => 'CUST009',
                'customer_category_id' => $categories['Bronze'],
                'start_date' => '2024-07-08',
                'description' => 'Research and development laboratory.',
            ],
            [
                'name' => 'Stellar Enterprises',
                'reference' => 'CUST010',
                'customer_category_id' => $categories['Gold'],
                'start_date' => '2024-03-22',
                'description' => 'Enterprise-level solutions provider.',
            ],
            [
                'name' => 'Smart Solutions',
                'reference' => 'CUST011',
                'customer_category_id' => $categories['Silver'],
                'start_date' => '2024-08-14',
                'description' => 'Smart technology solutions for modern businesses.',
            ],
            [
                'name' => 'NextGen Technologies',
                'reference' => 'CUST012',
                'customer_category_id' => $categories['Bronze'],
                'start_date' => '2024-09-30',
                'description' => 'Next generation technology development company.',
            ],
            [
                'name' => 'Apex Corporation',
                'reference' => 'CUST013',
                'customer_category_id' => $categories['Gold'],
                'start_date' => '2024-04-05',
                'description' => 'Top-tier corporation with extensive market presence.',
            ],
            [
                'name' => 'Dynamic Ventures',
                'reference' => 'CUST014',
                'customer_category_id' => $categories['Silver'],
                'start_date' => '2024-10-11',
                'description' => 'Venture capital and business development firm.',
            ],
            [
                'name' => 'Quality First Ltd',
                'reference' => 'CUST015',
                'customer_category_id' => $categories['Bronze'],
                'start_date' => '2024-11-20',
                'description' => 'Quality assurance and testing services company.',
            ],
        ];

        DB::table('customers')->insert($customers);
    }
}
