<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\DisposisiRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class DisposisiController extends Controller
{
    public function index(): View
    {
        return view('disposisi.index');
    }

    public function data(): JsonResponse
    {
        $app = Disposisi::query();
        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('approver.name', function ($row) {
                $roleName = "";
                $approvers = $row->approvers;
                foreach ($approvers as $key => $role) {
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
                $button .= '<a class="btn btn-sm btn-warning" href="' .  route('disposisi.edit', $row->id) . '"><i class="fa fa-edit"></i></a>';
                $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action', 'approver.name'])
            ->toJson();
    }

    public function create()
    {
        return view('disposisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'int'],
        ]);

        DB::transaction(function () use ($request) {
            $disposisi = Disposisi::create([
                'name' => $request->name,
                'urutan' => $request->urutan,
            ]);

            foreach ($request->roles as $i => $roleId) {
                DisposisiRole::create([
                    'disposisi_id' => $disposisi->id,
                    'role_id' => $roleId,
                    'prodi_id' => $request->prodis[$i],
                ]);
            }
        });

        return redirect()->route('disposisi.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(Disposisi $disposisi)
    {
        $disposisi->load('approvers');
        return view('disposisi.update', compact('disposisi'));
    }

    public function update(Request $request, Disposisi $disposisi)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'int'],
        ]);

        $data = [
            'name' => $request->name,
            'urutan' => $request->urutan,
        ];
        DB::transaction(function () use ($disposisi, $data, $request) {
            $disposisi->update($data);

            $disposisi->approvers()->delete();

            foreach ($request->roles as $i => $roleId) {
                DisposisiRole::create([
                    'disposisi_id' => $disposisi->id,
                    'role_id' => $roleId,
                    'prodi_id' => $request->prodis[$i],
                ]);
            }
        });

        return redirect()->route('disposisi.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy(Disposisi $disposisi): JsonResponse
    {
        try {
            $disposisi->approvers()->delete();
            $disposisi->delete();
            return response()->json(['res' => 'success'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }
}
