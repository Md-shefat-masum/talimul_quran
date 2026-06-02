<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $userTypes = [
            ['name' => 'Admin', 'code' => 'admin'],
            ['name' => 'Staff', 'code' => 'staff'],
            ['name' => 'Manager', 'code' => 'manager'],
            ['name' => 'Operator', 'code' => 'operator'],
        ];

        foreach ($userTypes as $userType) {
            UserType::query()->updateOrCreate(
                ['code' => $userType['code']],
                ['name' => $userType['name'], 'status' => true],
            );
        }
    }
}
