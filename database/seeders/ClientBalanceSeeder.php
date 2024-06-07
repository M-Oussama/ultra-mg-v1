<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientBalance;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();
        foreach ($clients as $client) {
            $sales = Sale::where('client_id', $client->id)->sum('total_amount');
            $payment = Payment::where('client_id', $client->id)->sum('amount_paid');

            $balance = $sales - $payment;

            ClientBalance::create([
                'client_id' => $client->id,
                'balance' => $balance,
            ]);
        }
    }
}
