<?php

use Illuminate\Database\Seeder;
use App\Model\Divisi;

class DivisiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisi = array(
            array(
                'nama' => 'SUPER ADMIN',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
            array(
                'nama' => 'SEKERTARIS 1',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
            array(
                'nama' => 'SEKERTARIS 2',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
            array(
                'nama' => 'SEKERTARIS 3',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
            array(
                'nama' => 'SUPIR',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
        );

        Divisi::insert($divisi);
    }
}
