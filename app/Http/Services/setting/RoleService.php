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
        return Role::query()->with(['permissions'])->orderBy('name', 'ASC')->get();
    }

    public function findById($payload): object|null
    {
        return Role::query()->where('id', $payload)->first();
    }

    public function findByIdWithPermissions($payload): object|null
    {
        return Role::query()->with(['permissions'])->where('id', $payload)->first();
    }

    public function store($payload): Role
    {
        $role = new Role();
        $role->name = $payload['name'];
        $role->guard_name = 'web';
        $role->save();

        return $role;
    }

    public function update($role, $payload): void
    {
        $role->name = $payload;
        $role->save();
    }

}
