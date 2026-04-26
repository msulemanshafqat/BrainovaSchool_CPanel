<?php

namespace Database\Seeders;

use App\Models\SpecialDiscount;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SpecialDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SpecialDiscount::create([
            'name' => 'Topper Merit Scholarship',
            'type' => 'P',
            'discount' => 50,
            'min_discount_amount' => null,
            'max_discount_amount' => 5000,
            'min_eligible_amount' => 500,
            'max_eligible_amount' => null,
            'short_description' => 'For students with outstanding academic results.',
            'active_status' => true,
        ]);

        SpecialDiscount::create([
            'name' => 'Disability Discount',
            'type' => 'F',
            'discount' => 500,
            'min_discount_amount' => null,
            'max_discount_amount' => null,
            'min_eligible_amount' => 1000,
            'max_eligible_amount' => null,
            'short_description' => 'Discount for physically challenged students.',
            'active_status' => true,
        ]);

        SpecialDiscount::create([
            'name' => 'Staff Child Discount',
            'type' => 'P',
            'discount' => 20,
            'min_discount_amount' => null,
            'max_discount_amount' => null,
            'min_eligible_amount' => null,
            'max_eligible_amount' => null,
            'short_description' => 'Applicable to children of school staff.',
            'active_status' => true,
        ]);
        SpecialDiscount::create([
            'name' => 'Single Parent Discount',
            'type' => 'P',
            'discount' => 25,
            'min_discount_amount' => null,
            'max_discount_amount' => 5000,
            'min_eligible_amount' => null,
            'max_eligible_amount' => null,
            'short_description' => 'For students raised by a single parent.',
            'active_status' => true,
        ]);
        SpecialDiscount::create([
            'name' => 'Financial Aid',
            'type' => 'F',
            'discount' => 1500,
            'min_discount_amount' => null,
            'max_discount_amount' => null,
            'min_eligible_amount' => 5000,
            'max_eligible_amount' => null,
            'short_description' => 'For students from economically weaker sections.',
            'active_status' => true,
        ]);



        SpecialDiscount::create([
            'name' => 'Minority Group Discount',
            'type' => 'P',
            'discount' => 10,
            'min_discount_amount' => null,
            'max_discount_amount' => null,
            'min_eligible_amount' => null,
            'max_eligible_amount' => null,
            'short_description' => 'For students from underrepresented communities.',
            'active_status' => true,
        ]);
    }
}
