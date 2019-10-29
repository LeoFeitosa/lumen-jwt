<?php

namespace App\Http\Controllers;

use App\Models\Accesses;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccessesController extends Controller
{
    public function __construct()
    {
        $this->classe = Accesses::class;
    }

    public function roles()
    {
        return Role::all();
    }

    public function storeRoles(Request $request)
    {
        return Role::create(['name' => $request->name]);
    }

    public function destroyRoles(Request $request)
    {
        $perms = array();

        foreach (Role::all() as $perm) {
            $perms[] = $perm->id;
        }
        if (in_array($request->id, $perms)) {
            $perm = Role::findById($request->id);
            $perm->delete();
            return Role::all();
        } else {
            return response()->json(
                [
                    'error' => 'Regra não encontrada'
                ],
                412
            );
        }
    }

    public function permissions()
    {
        return Permission::all();
    }

    public function storePermissions(Request $request)
    {
        return Permission::create(['name' => $request->name]);
    }

    public function destroyPermissions(Request $request)
    {
        $perms = array();

        foreach (Permission::all() as $perm) {
            $perms[] = $perm->id;
        }
        if (in_array($request->id, $perms)) {
            $perm = Permission::findById($request->id);
            $perm->delete();
            return Permission::all();
        } else {
            return response()->json(
                [
                    'error' => 'Permissão não encontrada'
                ],
                412
            );
        }
    }
}
