<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProfileService
{

    /**
     * @return Model|Builder|null
     */
    public function userData(): Model|Builder|null
    {
        return User::query()->with(['profile'])->where('id', Auth::id())->first();
    }

}
