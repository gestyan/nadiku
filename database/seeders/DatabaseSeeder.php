<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Satker;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ConfigSeeder::class,
            LetterStatusSeeder::class,
            ClassificationSeeder::class,
            LetterSeeder::class,
            DispositionSeeder::class,
        ]);
        Satker::create([
                'kode' => '11110',
                'deskripsi' => 'Kepala BPS Kab. Aceh Utara',
        ]);
        Satker::create([
                'kode' => '11111',
                'deskripsi' => 'Kasubag. Umum BPS Kab. Aceh Utara',
        ]);
    }
}
