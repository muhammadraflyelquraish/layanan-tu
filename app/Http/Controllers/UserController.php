<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

use App\Models\User;
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
        $user = User::with('role')->orderBy('created_at', 'desc');
        return DataTables::of($user)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<a class="btn btn-sm btn-warning" href="' .  route('user.edit', $row->id) . '"><i class="fa fa-edit"></i></a>';
                $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
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
        $roles = Role::pluck('name', 'id');
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_identity' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'string'],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ]);

        User::create([
            'name' => $request->name,
            'no_identity' => $request->no_identity,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        return redirect()->route('user.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('role');
        $roles = Role::pluck('name', 'id');
        return view('user.update', compact('user', 'roles'));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_identity' => ['required', 'string', 'max:255', 'unique:' . User::class . ',no_identity,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'string'],
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

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
            return response()->json(['res' => 'success'], 204);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], 400);
        }
    }
}
