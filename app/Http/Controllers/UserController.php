<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rules;


class UserController extends Controller
{

    public function index(): View
    {
        return view('user.index');
    }

    public function data(): JsonResponse
    {
        $users = User::query();
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role.name', function ($row) {
                $roleName = "";
                $roles = $row->roles;
                foreach ($roles as $key => $role) {
                    if ($key >= 3) {
                        $roleName .= ', ...';
                        break;
                    }

                    $roleName .= (empty($roleName) ? '' : ', ')
                        . $role->role->name
                        . (isset($role->prodi) ? ' (' . $role->prodi->name . ')' : '');
                }

                return $roleName;
            })
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<a class="btn btn-sm btn-warning" href="' .  route('user.edit', $row->id) . '"><i class="fa fa-edit"></i></a>';
                $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action', 'role.name'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_identity' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'no_identity' => $request->no_identity,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->status,
            ]);

            foreach ($request->roles as $i => $roleId) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'prodi_id' => $request->prodis[$i],
                ]);
            }
        });

        return redirect()->route('user.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return view('user.update', compact('user'));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_identity' => ['required', 'string', 'max:255', 'unique:' . User::class . ',no_identity,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ]);

        $data = [
            'name' => $request->name,
            'no_identity' => $request->no_identity,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        DB::transaction(function () use ($user, $data, $request) {
            $user->update($data);

            $user->roles()->delete();

            foreach ($request->roles as $i => $roleId) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'prodi_id' => $request->prodis[$i],
                ]);
            }
        });

        return redirect()->route('user.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user->roles()->delete();
            $user->delete();
            return response()->json(['res' => 'success'], 204);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], 400);
        }
    }
}
