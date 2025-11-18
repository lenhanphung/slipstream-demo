<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = DB::table('customers')->get();
        
        $contacts = [];
        
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Jessica', 'William', 'Amanda', 'James', 'Lisa', 'Richard', 'Jennifer', 'Joseph', 'Michelle'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Wilson', 'Anderson', 'Thomas', 'Taylor'];
        
        foreach ($customers as $customer) {
            $numContacts = rand(2, 5);
            
            for ($i = 0; $i < $numContacts; $i++) {
                $contacts[] = [
                    'customer_id' => $customer->id,
                    'first_name' => $firstNames[array_rand($firstNames)],
                    'last_name' => $lastNames[array_rand($lastNames)],
                ];
            }
        }
        
        DB::table('contacts')->insert($contacts);
    }
}
