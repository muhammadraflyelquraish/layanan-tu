<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Media;
use App\Models\Prodi;
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
        $pemohon = User::where("role_id", "!=", 3)->get();
        $prodi = Prodi::get();
        return view('spj.index', compact('pemohon', 'prodi'));
    }

    public function data(): JsonResponse
    {
        $latestRatings = DB::table('t_rating as r1')
            ->select('r1.spj_id', 'r1.rating')
            ->whereRaw('r1.created_at = (SELECT MAX(r2.created_at) FROM t_rating r2 WHERE r2.spj_id = r1.spj_id)')
            ->groupBy('r1.spj_id', 'r1.rating');

        $app = SPJ::with(['user', 'letter'])
            ->leftJoin('t_surat as l', 'l.id', '=', 't_spj.surat_id')
            ->leftJoin('t_user as u', 'u.id', '=', 't_spj.user_id')
            ->leftJoinSub($latestRatings, 'latest_rating', function ($join) {
                $join->on('latest_rating.spj_id', '=', 't_spj.id');
            })
            ->select('t_spj.*', 'l.kode as letter_kode', 'u.name as user_name', 'latest_rating.rating as rating_value');

        if (auth()->user()->role_id == 2) {
            $app->where("t_spj.user_id", auth()->user()->id);
        }

        $app->when(request('status'), function ($query) {
            $query->where('t_spj.status', request('status'));
        });

        $app->when(request('pemohon_id'), function ($query) {
            $query->where('t_spj.user_id', request('pemohon_id'));
        });

        $app->when(request('prodi_id'), function ($query) {
            $query->where('l.prodi_id', request('prodi_id'));
        });

        $app->when(request('search'), function ($query) {
            $searchTerm = "%" . request('search') . "%";

            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(t_spj.jenis) LIKE ?', [strtolower($searchTerm)])

                    ->orWhereHas('letter', function ($subQ) use ($searchTerm) {
                        $subQ->whereRaw('LOWER(kode) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(nomor_agenda) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orWhereHas('user', function ($subQ) use ($searchTerm) {
                        $subQ->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(no_identity) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orWhereRaw('LOWER(t_spj.jenis) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_spj.catatan) LIKE ?', [strtolower($searchTerm)]);
            });
        });

        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';

                // User Login
                $userLogin = auth()->user();

                // Button Proses
                if ($row->status == "Diproses" && $userLogin->role_id == 5) {
                    $button .= '<a href="' . route('spj.approval.view', $row->id) . '" class="btn btn-sm btn-info"><i class="fa fa-arrow-right"></i></a>';
                }

                // Button Revisi
                if ($row->status == "Revisi" && $row->user_id == $userLogin->id) {
                    $button .= '<a href="' . route('spj.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                }

                // Button Rating
                if ($row->status == "Disetujui" && $row->user_id == $userLogin->id) {
                    $button .= '<button type="button" data-toggle="modal" data-id="' . $row->id . '" data-target="#modalRating" class="btn btn-sm btn-info"><i class="fa fa-star"></i></button>';
                }

                $button .= '<a href="' . route('spj.show', $row->id) . '" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>';
                $button .= '</div>';
                return $button;
            })
            ->editColumn('letter', function ($row) {
                return '' . $row->letter_kode . ' <br> <small>Nomor Agenda: ' . $row->letter->nomor_agenda . '</small>';
            })
            ->editColumn('user.name', function ($row) {
                return $row->user->name . ' <br> <small>' . $row->user->email . '</small>' . ' <br> <small>' . ($row->letter->prodi ? 'Prodi: ' . $row->letter->prodi->name : '') . '</small>';
            })
            ->editColumn('tanggal_proses', function ($row) {
                return $row->tanggal_proses ? date('d M Y - H:i', strtotime($row->tanggal_proses)) : '-';
            })
            ->editColumn('tanggal_selesai', function ($row) {
                return $row->tanggal_selesai ? date('d M Y - H:i', strtotime($row->tanggal_selesai)) : '-';
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
            // ->editColumn('rating', function ($row) {
            //     $rating = '';
            //     if (isset($row->ratings[0])) {
            //         $rating = $row->ratings[0]->rating;
            //         // for ($i = 1; $i <= 5; $i++) {
            //         //     $rating .= "<span data-value=\"" . $i . "\" class=\"star\" style=\"" . ($i <= $row->ratings[0]->rating ? 'color: #f5b301' : '') . "\">&#9733;</span>";
            //         // }
            //         // $rating .= '<br> <small>' . $row->ratings[0]->catatan . '</small>';
            //     }
            //     return $rating ? $rating : '-';
            // })
            // ->editColumn('rating', function ($row) {
            //     $firstRating = $row->ratings->first(); // safer than $ratings[0]

            //     if ($firstRating) {
            //         return $firstRating->rating;
            //     }

            //     return '-';
            // })
            ->editColumn('rating', function ($row) {
                return $row->rating_value ?? '-';
            })
            ->rawColumns(['action', 'status', 'letter', 'user.name', 'rating'])
            ->addColumns(['letter', 'rating'])
            ->orderColumn('letter', 'letter_kode $1')
            ->orderColumn('rating', 'rating_value $1')
            ->toJson();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'surat_id' => 'required|string',
                'jenis' => 'required|string',
                'categories' => 'required|array',
                'files' => 'array',
                'links' => 'array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        try {
            DB::transaction(function () use ($request) {
                $letter = Letter::with('pemohon')->find($request->surat_id);

                $spj = SPJ::create([
                    "surat_id" => $request->surat_id,
                    "user_id" => $letter->pemohon_id,
                    "jenis" => $request->jenis,
                    "status" => "Diproses",
                    "tanggal_proses" => now(),
                ]);

                $uploadedFiles = $request->file('files');

                foreach ($request->categories as $i => $category) {
                    // Link
                    $link = isset($request->links[$i]) ? $request->links[$i] : null;

                    // Dokumen
                    $mediaId = null;
                    if (isset($uploadedFiles[$i])) {
                        $file = $uploadedFiles[$i];

                        if ($file->getSize() > 5000000) {
                            throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 5MB.");
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
                        $mediaId = $media->id;
                    }

                    SPJDocument::create([
                        "spj_id" => $spj->id,
                        "spj_label_id" => $category,
                        "file_id" => $mediaId,
                        "link" => $link
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['msg' => 'SPJ berhasil ditambahkan.'], 201);
    }

    public function show(SPJ $spj)
    {
        // check the user is not pemohon
        if (auth()->user()->role_id == 2 && $spj->user_id != auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $spj = $spj->load(['letter', 'user', 'documents', 'histories', 'ratings']);
        return view('spj.detail', compact('spj'));
    }

    public function approvalView(SPJ $spj)
    {
        $spj = $spj->load(['letter', 'user', 'documents', 'histories', 'ratings']);
        return view('spj.approval', compact('spj'));
    }

    public function edit(SPJ $spj)
    {
        $spj = $spj->load(['letter', 'user', 'documents', 'histories', 'ratings']);
        return view('spj.edit', compact('spj'));
    }

    public function update(SPJ $spj, Request $request)
    {
        // skip if status already approved
        if (auth()->user()->role_id == 2 && $spj->status == 'Disetujui') {
            return response()->json(['error' => 'SPJ sudah disetujui'], 400);
        }

        DB::transaction(function () use ($spj, $request) {
            $status = $request->type == 'revisi' ? 'Revisi' : 'Disetujui';
            $tanggalSelesai = $request->type == 'setuju' ? now() : null;
            $catatan = $request->type == 'revisi' ? $request->catatan : 'Mohon kirimkan hard file ke Divisi Keuangan' . ($request->catatan != '' ? ', ' . $request->catatan : '');

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
            // skip if status already approved
            if (auth()->user()->role_id == 2 && $spj->status == 'Disetujui') {
                return response()->json(['error' => 'SPJ sudah disetujui'], 400);
            }

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
                    if (isset($spjDocumentIds[$i])) {
                        $spjDocument = SPJDocument::find($spjDocumentIds[$i]);

                        // Link
                        $link = isset($request->links[$i]) ? $request->links[$i] : null;

                        // Media
                        $mediaId = $spjDocument->file_id;
                        if (isset($uploadedFiles[$i])) {
                            $file = $uploadedFiles[$i];

                            if ($file->getSize() > 5000000) {
                                throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 5MB.");
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
                            $mediaId = $media->id;
                        }

                        $spjDocument->update([
                            "spj_id" => $spj->id,
                            "spj_label_id" => $categoryId,
                            "file_id" => $mediaId,
                            "link" => $link
                        ]);
                        array_push($updatedSpjDocumentIds, $spjDocument->id);
                        continue;
                    } else {
                        // Link
                        $link = isset($request->links[$i]) ? $request->links[$i] : null;

                        // Dokumen
                        $mediaId = null;
                        if (isset($uploadedFiles[$i])) {
                            $file = $uploadedFiles[$i];

                            if ($file->getSize() > 5000000) {
                                throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 5MB.");
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
                            $mediaId = $media->id;
                        }

                        $spjDocument = SPJDocument::create([
                            "spj_id" => $spj->id,
                            "spj_label_id" => $categoryId,
                            "file_id" => $mediaId,
                            "link" => $link
                        ]);
                        array_push($updatedSpjDocumentIds, $spjDocument->id);
                        continue;
                    }
                }

                // Remove document that not include on updated or new data
                SPJDocument::where('spj_id', $spj->id)->whereNotIn('id', $updatedSpjDocumentIds)->delete();
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['msg' => 'SPJ berhasil diubah.'], 201);
    }

    public function getRating(SPJ $spj)
    {
        $rating = SPJRating::where('spj_id', $spj->id)->where('user_id', request()->user()->id)->first();
        return response()->json(['data' => $rating], 200);
    }

    public function rating(Request $request)
    {
        $rating = SPJRating::where('spj_id', $request->spj_id)->where('user_id', $request->user()->id)->first();
        if ($rating) {
            $rating->update([
                'rating' => (int)$request->rating,
                'catatan' => $request->catatan,
            ]);
        } else {
            SPJRating::create([
                'spj_id' => $request->spj_id,
                'user_id' => $request->user()->id,
                'rating' => (int)$request->rating,
                'catatan' => $request->catatan,
                'tipe' => 'SPJ',
            ]);
        }
        return redirect()->route('spj.index')->with('success', 'Rating berhasil ditetapkan.');
    }
}
