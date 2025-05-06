<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create([
            'name' => StatusEnum::MENUNGGU_PERSETUJUAN->value,
        ]);

        Status::create([
            'name' => StatusEnum::DISETUJUI->value,
        ]);
    }
}
