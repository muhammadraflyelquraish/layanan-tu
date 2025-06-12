<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class DisposisiController extends Controller
{
    public function index(): View
    {
        $approver = Role::where('is_disposition', true)->get();
        return view('disposisi.index', compact('approver'));
    }

    public function data(): JsonResponse
    {
        $app = Disposisi::with(['approver']);
        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<button class="btn btn-sm btn-warning" data-mode="edit" data-integrity="' . $row->id . '" data-toggle="modal" data-target="#ModalAddEdit"><i class="fa fa-edit"></i></button>';
                $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            Disposisi::create($request->all());
            return response()->json(['res' => 'success', 'msg' => 'Data berhasil ditambahkan'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show(Disposisi $disposisi)
    {
        return $disposisi->load('approver');
    }

    public function update(Request $request, Disposisi $disposisi): JsonResponse
    {
        try {
            $data = $request->all();
            $disposisi->update($data);
            return response()->json(['res' => 'success', 'msg' => 'Data berhasil diubah'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy(Disposisi $disposisi): JsonResponse
    {
        try {
            $disposisi->delete();
            return response()->json(['res' => 'success'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }
}
