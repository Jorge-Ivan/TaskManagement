<?php

use Illuminate\Database\Seeder;
use App\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Pendiente', 'En proceso', 'Completo'];

        foreach ($names as $name) {
            $status = new Status();
            $status->name = $name;
            $status->save();
        }
    }
}
