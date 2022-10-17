<?php

namespace App\Http\Services\setting;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * @return Collection|array
     */
    public function index(): Collection|array
    {
        return Role::query()->latest()->get();
    }

}
