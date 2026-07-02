<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeCareer;
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
       // $this->createProducts();
       // $this->createClients();
       // $this->createSaleStatus();
        $this->companies();
       // $this->createEmployees();
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
           $product = Product::firstOrCreate(['product_code' => $productData['product_code']], $productData);
           ProductStock::firstOrCreate(["product_id"=> $product->id], ["quantity"=> 200]);
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
                'city_id' => '16',
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
                'city_id' => '16',
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
                'city_id' => '16',
            ],
            // Add more clients if needed
        ];

        foreach ($clients as $clientData) {
            $client = Client::firstOrCreate(['email' => $clientData['email']], $clientData);

            $client->full_name = $client->surname ? $client->name.' '.$client->surname : $client->name;
            $client->save();
        }
    }

    public function createSaleStatus() {
        foreach (SaleStatus::STATUS as $status) {
            SaleStatus::firstOrCreate(['name' => $status]);
        }
    }

    public function companies() {
        $now = now();

        Company::upsert(
            [
                [
                    'id' => 1,
                    'name' => 'EURL SETIFIS DETERGENTS',
                    'description' => 'COOPERATIVE IMMOBILIERE EL AFAK 270 GROUPE 65 LOT A SETIF',
                    'address' => '',
                    'address2' => '',
                    'email' => 'ultranew19@gmail.com',
                    'phone' => '0790.15.92.60',
                    'NRC' => '19/01-0090505B13',
                    'NIF' => '0013190090055719001',
                    'NART' => '19019071023',
                    'NIS' => '',
                    'capitale' => '11000000',
                    'show_company_info' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 2,
                    'name' => 'EURL SETIFIS DETERGENTS',
                    'description' => 'FABRICATION DES PRODUITS DE BLANCHISSANTS ET CONNEXES',
                    'address' => 'Lot N° 34 Section 6 Groupe 51 KASR EL ABTAL',
                    'address2' => '',
                    'email' => 'ultranew19@gmail.com',
                    'phone' => '0790.15.92.60',
                    'NRC' => '19/00-0090505B13',
                    'NIF' => '001319009050557',
                    'NART' => '19521701120',
                    'NIS' => '001319010024074',
                    'capitale' => '11000000',
                    'show_company_info' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['id'],
            [
                'name',
                'description',
                'address',
                'address2',
                'email',
                'phone',
                'NRC',
                'NIF',
                'NART',
                'NIS',
                'capitale',
                'show_company_info',
                'updated_at',
            ]
        );
    }

    public function createEmployees() {
        $employees = [
            [
              'name'=> 'John',
              'surname'=> 'Doe',
              'birthdate'=> '1999-01-01',
              'birthplace'=> 'UK',
              'email'=> 'john@gmail.com',
              'address'=> 'UK',
              'phone'=> '12345679',
              'NIN'=> '1',
              'NCN'=>'1',
              'CNAS'=> '1',
              'card_issue_date'=> '2005-02-02',
              'card_issue_place'=> 'UK'
            ],
            [
                'name'=> 'Bob',
                'surname'=> 'smith',
                'birthdate'=> '1999-01-01',
                'birthplace'=> 'UK',
                'email'=> 'john@gmail.com',
                'address'=> 'UK',
                'phone'=> '12345679',
                'NIN'=> '1',
                'NCN'=>'1',
                'CNAS'=> '1',
                'card_issue_date'=> '2005-02-02',
                'card_issue_place'=> 'UK'
            ]
        ];

        foreach ($employees as $employee) {
           $_employee =  Employee::firstOrCreate(['name' => $employee['name'], 'surname' => $employee['surname']], $employee);

            EmployeeCareer::firstOrCreate([
                'employee_id'=> $_employee->id,
                'start_date'=>'2023-01-01'
            ]);
        }
    }
}
