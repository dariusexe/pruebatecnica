<?php

namespace Database\Seeders;

use App\Enum\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(5)->hasAttached(Project::factory()->count(3), ['role_id' => UserRole::MANAGER])->create();
    }
}
