<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Super Operator
        $user = User::query()->findOrFail(1);
        $profile = new UserProfile();
        $profile->fk_user_id = $user->id;
        $profile->user_phone = '01798435813';
        $profile->save();
    }
}
