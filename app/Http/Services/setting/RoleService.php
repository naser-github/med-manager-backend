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
        return Role::query()->orderBy('name','ASC')->get();
    }

    public function findById($payload): object|null
    {
        return Role::query()->where('id', $payload)->first();
    }

    public function store($payload): void
    {
        $role = new Role();
        $role->name = $payload;
        $role->save();
    }

    public function update($role, $payload): void
    {
        $role->name = $payload;
        $role->save();
    }

}
