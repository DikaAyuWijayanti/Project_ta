<?php

use Illuminate\Database\Seeder;
use App\Rekening;
class RekeningTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['bank_name' => 'BRI','atas_nama'=>'SWP SAMBUNGROSO','no_rekening'=>'03880-01-000-xxx']
            
        ];
        Rekening::insert($data);
    }
}
