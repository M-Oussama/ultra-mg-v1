<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\SaleStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createProducts();
        $this->createClients();
        $this->createSaleStatus();
    }

    public function createProducts(){
        $products = [
            [
                'name' => 'Texapon',
                'brand' => 'BrandA',
                'description' => 'Description for Texapon',
                'product_code' => 'TX001',
                'category_id' => 1, // Replace with the actual category ID
                'SKU' => 'SKU001',
                'min_stock_level' => 10,
                'price' => 20.50,
                'stockable' => 1,
                'tax_rate' => 8.5,
            ],
            [
                'name' => 'HPMC-china',
                'brand' => 'BrandB',
                'description' => 'Description for HPMC-china',
                'product_code' => 'HPMC001',
                'category_id' => 2, // Replace with the actual category ID
                'SKU' => 'SKU002',
                'min_stock_level' => 15,
                'price' => 15.75,
                'stockable' => 1,
                'tax_rate' => 7.5,
            ],
            [
                'name' => 'Betaine',
                'brand' => 'BrandC',
                'description' => 'Description for Betaine',
                'product_code' => 'BET001',
                'category_id' => 3, // Replace with the actual category ID
                'SKU' => 'SKU003',
                'min_stock_level' => 8,
                'price' => 30.00,
                'stockable' => 1,
                'tax_rate' => 10.0,
            ],
            // Add more products if needed
        ];
        foreach ($products as $productData) {
           $product = Product::create($productData);
           ProductStock::create(["product_id"=> $product->id,"quantity"=> 200]);
        }
    }

    public function createClients() {
        $clients = [
            [
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'email' => 'john.doe@example.com',
                'phone' => '123-456-7890',
                'NRC' => 'NRC001',
                'NIF' => 'NIF001',
                'NART' => 'NART001',
                'NIS' => 'NIS001',
            ],
            [
                'name' => 'Jane',
                'surname' => 'Doe',
                'address' => '456 Oak St',
                'email' => 'jane.doe@example.com',
                'phone' => '987-654-3210',
                'NRC' => 'NRC002',
                'NIF' => 'NIF002',
                'NART' => 'NART002',
                'NIS' => 'NIS002',
            ],
            [
                'name' => 'Bob',
                'surname' => 'Smith',
                'address' => '789 Elm St',
                'email' => 'bob.smith@example.com',
                'phone' => '555-123-4567',
                'NRC' => 'NRC003',
                'NIF' => 'NIF003',
                'NART' => 'NART003',
                'NIS' => 'NIS003',
            ],
            // Add more clients if needed
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }

    public function createSaleStatus() {
        foreach (SaleStatus::STATUS as $status) {
            SaleStatus::create(['name' => $status]);
        }
    }
}
