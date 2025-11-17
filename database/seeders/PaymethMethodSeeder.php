<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymethMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Efectivo',
                'description' => 'Efectivo',
                'icon' => 'fa-money-bill-wave',
                'active' => true,
            ],
            [
                'name' => 'Clave',
                'description' => 'Clave',
                'icon' => 'fa-key',
                'active' => true,
            ],
            [
                'name' => 'Tarjeta',
                'description' => 'Tarjeta',
                'icon' => 'fa-credit-card',
                'active' => true,
            ],
            [
                'name' => 'Yappy',
                'description' => 'Yappy',
                'icon' => 'fa-mobile-alt',
                'active' => true,
            ],
            [
                'name' => 'Transferencia',
                'description' => 'Transferencia',
                'icon' => 'fa-exchange-alt',
                'active' => true,
            ],
        ];        
        foreach ($data as $item) {
            PaymentMethod::create($item);
        }
    }
}
