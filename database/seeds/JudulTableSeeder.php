<?php

use Illuminate\Database\Seeder;
use App\Model\Judul;

class JudulTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $judul = array(
            array(
                'nama' => 'Semua Surat',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
            array(
                'nama' => 'Surat Perintah',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
            array(
                'nama' => 'Surat Permohonan',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
            array(
                'nama' => 'Surat Perbaikan',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ),
        );

        Judul::insert($judul);
    }
}
