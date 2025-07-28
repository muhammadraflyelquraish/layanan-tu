<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\QueryDataTable;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('role.index');
    }

    public function data(): JsonResponse
    {
        $query = DB::table('t_role')->orderBy('created_at', 'desc');
        return (new QueryDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';

                if ($row->name != "Admin") {
                    $button .= '<a class="btn btn-sm btn-warning" href="' .  route('role.edit', $row->id) . '"><i class="fa fa-edit"></i></a>';

                    if ($row->is_allow_deleted) {
                        $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    }
                }
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('role.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $permissions = [
            'DASHBOARD',
            'LETTER',
            'SPJ',
            'LABEL_SPJ',
            'USER',
            'ROLE',
            'DISPOSISI',
            'ARSIP',
        ];

        DB::transaction(function () use ($request, $permissions) {
            $role = Role::create([
                "name" => $request['name'],
                "is_disposition" => true,
            ]);

            foreach ($permissions as $i => $permission) {
                $is_permitted = $request[strtolower($permission) . "_permitted"] ? true : false;
                $role->permissions()->create([
                    "role_id" => $role->id,
                    "menu" => $permission,
                    "is_permitted" => $permission == 'SUBMISSION' ? true : $is_permitted,
                ]);
            }
        });
        return redirect()->route('role.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return $role->id;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        return view('role.update', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $permissions = [
            'DASHBOARD',
            'LETTER',
            'SPJ',
            'LABEL_SPJ',
            'USER',
            'ROLE',
            'DISPOSISI',
            'ARSIP',
        ];

        DB::transaction(function () use ($request, $permissions, $role) {
            $role->update([
                "name" => $request['name'],
                "is_disposition" => true,
            ]);
            $role->permissions()->delete();

            foreach ($permissions as $i => $permission) {
                $is_permitted = $request[strtolower($permission) . "_permitted"] ? true : false;
                $role->permissions()->create([
                    "role_id" => $role->id,
                    "menu" => $permission,
                    "is_permitted" => $permission == 'SUBMISSION' ? true : $is_permitted,
                ]);
            }
        });
        return redirect()->route('role.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            if ($role->is_allow_deleted) {
                $role->permissions()->delete();
                $role->delete();
            }

            return response()->json(['res' => 'success'], 204);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], 400);
        }
    }
}
