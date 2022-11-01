<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('transaction_types')->truncate();
        $type = [
            ['id' => 1, 'name' => 'Deposit'],
            ['id' => 2, 'name' => 'Withdrawal'],
            ['id' => 3, 'name' => 'Transferred'],
            ['id' => 4, 'name' => 'Received'],
            ['id' => 5, 'name' => 'Exchange_From'],
            ['id' => 6, 'name' => 'Exchange_To'],
            ['id' => 9, 'name' => 'Request_From'],
            ['id' => 10, 'name' => 'Request_To'],
            ['id' => 11, 'name' => 'Payment_Sent'],
            ['id' => 12, 'name' => 'Payment_Received'],
            ['id' => 13, 'name' => 'Crypto_Sent'],
            ['id' => 14, 'name' => 'Crypto_Received'],
            ['id' => 15, 'name' => 'Escrow'],
            ['id' => 16, 'name' => 'Crypto_Exchange_From'],
            ['id' => 17, 'name' => 'Crypto_Exchange_To'],
            ['id' => 18, 'name' => 'Fiat_Exchange_From'],
            ['id' => 19, 'name' => 'Fiat_Exchange_To'],

        ];
        \DB::table('transaction_types')->insert($type);
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
