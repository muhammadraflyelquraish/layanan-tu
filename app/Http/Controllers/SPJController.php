<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Media;
use App\Models\SPJ;
use App\Models\SPJDocument;
use App\Models\SPJHistory;
use App\Models\SPJRating;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SPJController extends Controller
{
    public function index(): View
    {
        $pemohon = User::where("role_id", 2)->get();
        return view('spj.index', compact('pemohon'));
    }

    public function data(): JsonResponse
    {
        $app = SPJ::with(['user', 'ratings']);

        if (auth()->user()->role_id == 2) {
            $app->where("t_spj.user_id", auth()->user()->id);
        }

        $app->when(request('status'), function ($query) {
            $query->where('t_spj.status', request('status'));
        });

        $app->when(request('pemohon_id'), function ($query) {
            $query->where('t_spj.user_id', request('pemohon_id'));
        });

        $app->when(request('search'), function ($query) {
            $searchTerm = "%" . request('search') . "%";

            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(t_spj.jenis) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereHas('letter', function ($subQ) use ($searchTerm) {
                        $subQ->whereRaw('LOWER(kode) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orWhereHas('user', function ($subQ) use ($searchTerm) {
                        $subQ->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(no_identity) LIKE ?', [strtolower($searchTerm)]);
                    });
            });
        });

        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';

                if ($row->status == "Diproses" && auth()->user()->role_id != 2) {
                    $button .= '<a href="' . route('spj.approval.view', $row->id) . '" class="btn btn-sm btn-info"><i class="fa fa-arrow-right"></i></a>';
                }

                if ($row->status == "Revisi") {
                    $button .= '<a href="' . route('spj.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                }

                if ($row->status == "Disetujui" && $row->ratings->count() == 0 && auth()->user()->role_id == 2) {
                    $button .= '<button type="button" data-toggle="modal" data-id="' . $row->id . '" data-target="#modalRating" class="btn btn-sm btn-info"><i class="fa fa-star"></i></button>';
                }

                $button .= '<a href="' . route('spj.show', $row->id) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>';
                $button .= '</div>';
                return $button;
            })
            ->editColumn('letter', function ($row) {
                return '' . $row->letter->kode . ' <br> <small>(' . $row->letter->untuk . ')</small>';
            })
            ->editColumn('user.name', function ($row) {
                return '' . $row->user->name . ' <br> <small>(' . $row->user->no_identity . ')</small>';
            })
            ->editColumn('tanggal_proses', function ($row) {
                return $row->tanggal_proses ? date('d M Y', strtotime($row->tanggal_proses)) : '-';
            })
            ->editColumn('tanggal_selesai', function ($row) {
                return $row->tanggal_selesai ? date('d M Y', strtotime($row->tanggal_selesai)) : '-';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'Disetujui') {
                    return '<span class="label label-success">' . $row->status . '</span>';
                } else if ($row->status == 'Ditolak') {
                    return '<span class="label label-danger">' . $row->status . '</span>';
                } else {
                    return '<span class="label label-warning">' . $row->status . '</span>';
                }
            })
            ->editColumn('rating', function ($row) {
                $rating = '';
                if (isset($row->ratings[0])) {
                    for ($i = 1; $i <= 5; $i++) {
                        $rating .= "<span data-value=\"" . $i . "\" class=\"star\" style=\"" . ($i <= $row->ratings[0]->rating ? 'color: #f5b301' : '') . "\">&#9733;</span>";
                    }
                    $rating .= "<br> <small>\"" . $row->ratings[0]->catatan . "\"</small>";
                }
                return $rating ? $rating : '-';
            })
            ->rawColumns(['action', 'status', 'letter', 'user.name', 'rating'])
            ->addColumns(['letter', 'rating'])
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
                $letter = Letter::with('pemohon')->find($request->letter_id);

                $spj = SPJ::create([
                    "letter_id" => $request->letter_id,
                    "user_id" => $letter->pemohon_id,
                    "jenis" => $request->jenis,
                    "status" => "Diproses",
                    "tanggal_proses" => now(),
                ]);

                $uploadedFiles = $request->file('files');
                foreach ($uploadedFiles as $i => $file) {
                    if ($file->getSize() > 614400) {
                        throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 600 KB.");
                    }

                    $fileName = uniqid() . '_' . $file->getClientOriginalName();

                    $disk = 'pdf';
                    $uploadedPath = Storage::disk($disk)->put($disk, $file);
                    $url = Storage::disk($disk)->url($uploadedPath);

                    $media = Media::create([
                        "name" => $fileName,
                        "original_name" => $file->getClientOriginalName(),
                        "path" => $uploadedPath,
                        "file_url" => $url,
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
        $spj = $spj->load(['letter', 'user', 'documents', 'histories']);
        return view('spj.detail', compact('spj'));
    }

    public function approvalView(SPJ $spj)
    {
        $spj = $spj->load(['letter', 'user', 'documents', 'histories']);
        return view('spj.approval', compact('spj'));
    }

    public function edit(SPJ $spj)
    {
        $spj = $spj->load(['letter', 'user', 'documents', 'histories']);
        return view('spj.edit', compact('spj'));
    }

    public function update(SPJ $spj, Request $request)
    {
        DB::transaction(function () use ($spj, $request) {
            $status = $request->type == 'revisi' ? 'Revisi' : 'Disetujui';
            $tanggalSelesai = $request->type == 'setuju' ? now() : null;
            $catatan =  $request->type == 'revisi' ? $request->catatan : 'Mohon kirimkan hard file ke divisi Keuangan';

            $spj->update([
                'status' => $status,
                'catatan' => $catatan,
                'tanggal_selesai' => $tanggalSelesai,
            ]);

            SPJHistory::create([
                'spj_id' => $spj->id,
                'user_id' => auth()->user()->id,
                'status' => $status,
                'catatan' => $catatan,
            ]);
        });

        return redirect()->route('spj.index')->with('success', 'Status pengajuan berhasil diubah.');
    }

    public function revisi(SPJ $spj, Request $request)
    {
        try {
            DB::transaction(function () use ($request, $spj) {
                $spj->update([
                    "jenis" => $request->jenis,
                    "status" => "Diproses",
                    "tanggal_selesai" => null,
                    "updated_at" => now(),
                ]);

                $spjDocumentIds = $request->document_ids;
                $spjDocumentCategories = $request->categories;
                $uploadedFiles = $request->file('files');
                $updatedSpjDocumentIds = [];

                foreach ($spjDocumentCategories as $i => $categoryId) {
                    if (!isset($uploadedFiles[$i])) {
                        array_push($updatedSpjDocumentIds, $spjDocumentIds[$i]);
                        continue;
                    }

                    if (isset($uploadedFiles[$i]) && !isset($spjDocumentIds[$i])) {
                        $file = $uploadedFiles[$i];
                        if ($file->getSize() > 614400) {
                            throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 600 KB.");
                        }

                        $fileName = uniqid() . '_' . $file->getClientOriginalName();

                        $disk = 'pdf';
                        $uploadedPath = Storage::disk($disk)->put($disk, $file);
                        $url = Storage::disk($disk)->url($uploadedPath);

                        $media = Media::create([
                            "name" => $fileName,
                            "original_name" => $file->getClientOriginalName(),
                            "path" => $uploadedPath,
                            "file_url" => $url,
                        ]);

                        $spjDocument = SPJDocument::create([
                            "spj_id" => $spj->id,
                            "spj_category_id" => $spjDocumentCategories[$i],
                            "spj_file" => $media->id,
                        ]);
                        array_push($updatedSpjDocumentIds, $spjDocument->id);
                        continue;
                    }

                    if (isset($uploadedFiles[$i]) && isset($spjDocumentIds[$i])) {
                        $spjDocument = SPJDocument::find($spjDocumentIds[$i]);

                        $file = $uploadedFiles[$i];
                        if ($file->getSize() > 614400) {
                            throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 600 KB.");
                        }

                        $fileName = uniqid() . '_' . $file->getClientOriginalName();

                        $disk = 'pdf';
                        $uploadedPath = Storage::disk($disk)->put($disk, $file);
                        $url = Storage::disk($disk)->url($uploadedPath);

                        $media = Media::create([
                            "name" => $fileName,
                            "original_name" => $file->getClientOriginalName(),
                            "path" => $uploadedPath,
                            "file_url" => $url,
                        ]);

                        $spjDocument->update([
                            "spj_id" => $spj->id,
                            "spj_category_id" => $categoryId,
                            "spj_file" => $media->id,
                        ]);
                        array_push($updatedSpjDocumentIds, $spjDocument->id);
                    }
                }

                // Remove document that not include on updated or new data
                SPJDocument::where('spj_id', $spj->id)->whereNotIn('id', $updatedSpjDocumentIds)->delete();
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => $e->getMessage()])->withInput();
        }

        return redirect()->route('spj.index')->with('success', 'Data SPJ berhasil diubah.');
    }

    public function rating(Request $request)
    {
        SPJRating::create([
            'spj_id' => $request->spj_id,
            'user_id' => $request->user()->id,
            'rating' => (int)$request->rating,
            'catatan' => $request->catatan,
        ]);
        return redirect()->route('spj.index')->with('success', 'Rating SPJ berhasil simpan.');
    }
}
