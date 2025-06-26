<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SPJCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class LabelSPJController extends Controller
{
    public function index(): View
    {
        return view('label-spj.index');
    }

    public function data(): JsonResponse
    {
        $app = SPJCategory::orderBy('created_at', 'desc');
        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<button class="btn btn-sm btn-warning" data-mode="edit" data-integrity="' . $row->id . '" data-toggle="modal" data-target="#ModalAddEdit"><i class="fa fa-edit"></i></button>';
                $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                $button .= '</div>';
                return $button;
            })
            ->editColumn('jenis', function ($row) {
                if ($row->jenis == 'FILE') {
                    return "File";
                } else if ($row->jenis == 'LINK') {
                    return "Link";
                } else if ($row->jenis == 'FILE_LINK') {
                    return "File & Link";
                }
                return '-';
            })
            ->rawColumns(['action', 'jenis'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            SPJCategory::create($request->all());
            return response()->json(['res' => 'success', 'msg' => 'Data berhasil ditambahkan'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        $spjCategory = SPJCategory::find($id);
        return $spjCategory;
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $spjCategory = SPJCategory::find($id);
            $data = $request->all();
            $spjCategory->update($data);
            return response()->json(['res' => 'success', 'msg' => 'Data berhasil diubah'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $spjCategory = SPJCategory::find($id);
            $spjCategory->delete();
            return response()->json(['res' => 'success'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }
}
