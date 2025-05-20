<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\SPJ;
use App\Models\SPJDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SPJController extends Controller
{
    public function index(): View
    {
        return view('spj.index');
    }

    public function data(): JsonResponse
    {
        $app = SPJ::with(['user']);
        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<a href="' . route('spj.show', $row->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>';
                $button .= '</div>';
                return $button;
            })
            ->editColumn('letter', function ($row) {
                return $row->letter->kode;
            })
            ->editColumn('tanggal_proses', function ($row) {
                return $row->tanggal_proses ? date('d M Y', strtotime($row->tanggal_proses)) : '-';
            })
            ->editColumn('tanggal_selesai', function ($row) {
                return $row->tanggal_selesai ? date('d M Y', strtotime($row->tanggal_selesai)) : '-';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'Selesai') {
                    return '<span class="label label-success">' . $row->status . '</span>';
                } else if ($row->status == 'Ditolak') {
                    return '<span class="label label-danger">' . $row->status . '</span>';
                } else {
                    return '<span class="label label-warning">' . $row->status . '</span>';
                }
            })
            ->rawColumns(['action', 'status'])
            ->addColumns(['letter'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_id' => 'required|string',
            'jenis' => 'required|string',
            'categories' => 'required|array',
            'files' => 'required|array'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $spj = SPJ::create([
                    "letter_id" => $request->letter_id,
                    "user_id" => $request->user()->id,
                    "jenis" => $request->jenis,
                    "status" => "Pending",
                    "tanggal_proses" => now(),
                ]);

                $uploadedFiles = $request->file('files');
                foreach ($uploadedFiles as $i => $file) {
                    if ($file->getSize() > 614400) {
                        throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 600 KB.");
                    }

                    $fileName = uniqid() . '_' . $file->getClientOriginalName();
                    // $filePath = $file->storeAs('spj', $fileName, 'public');

                    $media = Media::create([
                        "name" => $fileName,
                        "file_url" => "",
                    ]);

                    SPJDocument::create([
                        "spj_id" => $spj->id,
                        "spj_category_id" => $request['categories'][$i],
                        "spj_file" => $media->id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => $e->getMessage()])->withInput();
        }

        return redirect()->route('letter.index')->with('success', 'SPJ berhasil ditambahkan.');
    }

    public function show(SPJ $spj)
    {
        $spj = $spj->load(['letter', 'user', 'documents']);
        return view('spj.detail', compact('spj'));
    }
}
