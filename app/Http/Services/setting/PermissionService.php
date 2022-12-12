<?php

namespace App\Http\Services\setting;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * @return Collection|array
     */
    public function index(): Collection|array
    {
        return Permission::query()->orderBy('name','ASC')->get();
    }

    public function findById($payload): object|null
    {
        return Permission::query()->where('id', $payload)->first();
    }

    public function store($payload): void
    {
        $permission = new Permission();
        $permission->name = $payload;
        $permission->save();
    }

    public function update($permission, $payload): void
    {
        $permission->name = $payload;
        $permission->save();
    }

}
