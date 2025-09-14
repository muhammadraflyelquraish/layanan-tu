<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\Media;
use App\Models\Prodi;
use App\Models\SPJRating;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class LetterController extends Controller
{
    public function index(): View
    {
        // User Login
        $userLogin = auth()->user();

        $pemohon = User::where("role_id", "!=", 3)->get();
        $disposisi = Disposisi::orderBy("urutan", "asc")->get();
        $nomorAgenda = Letter::whereYear('created_at', now()->year)->count() + 1;

        $prodi = Prodi::query();
        if ($userLogin->role_id == 7 || $userLogin->role_id == 8) {
            $prodi->where("id", $userLogin->prodi_id);
        }
        $prodi = $prodi->get();

        return view('letter.index', compact('pemohon', 'disposisi', 'nomorAgenda', 'prodi'));
    }

    public function data(): JsonResponse
    {
        // User Login
        $userLogin = auth()->user();

        $app = Letter::query()->with(['pemohon', 'spjs', 'file', 'sk', 'prodi'])
            ->leftJoin('t_user as u', 'u.id', '=', 't_surat.pemohon_id')
            ->select('t_surat.*',  'u.name as user_name', 'u.email as user_email');

        if ($userLogin->role_id == 2 || $userLogin->role_id == 6) { // Role (Pemohon, Dosen)
            $app->where("t_surat.pemohon_id", $userLogin->id);
        } else if ($userLogin->role_id == 7 || $userLogin->role_id == 8) { // Role (Prodi, Sek Prodi)
            $app->where("t_surat.prodi_id", $userLogin->prodi_id);
            $app->whereNotIn('t_surat.status', ['Selesai', 'Ditolak']);
        } else {
            // $app->where(function ($query) {
            //     $query->where(function ($q) {
            //         $q->where('disertai_dana', false)
            //             ->whereNotIn('status', ['Selesai', 'Ditolak']);
            //     })->orWhere(function ($q) {
            //         $q->where('disertai_dana', true)
            //             ->whereNotIn('status', ['Ditolak'])
            //             ->whereDoesntHave('spjs');
            //     });
            // });
            $app->whereNotIn('t_surat.status', ['Selesai', 'Ditolak']);
        }

        $app->when(request('status'), function ($query) {
            $query->where('t_surat.status', request('status'));
        });

        $app->when(request('pemohon_id'), function ($query) {
            $query->where('t_surat.pemohon_id', request('pemohon_id'));
        });

        $app->when(request('disertai_dana'), function ($query) {
            $query->where('t_surat.disertai_dana', request('disertai_dana') == "Ya");
        });

        $app->when(request('prodi_id'), function ($query) {
            $query->where('t_surat.prodi_id', request('prodi_id'));
        });

        $app->when(request('search'), function ($query) {
            $searchTerm = "%" . request('search') . "%";

            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(t_surat.kode) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereHas('pemohon', function ($subQ) use ($searchTerm) {
                        $subQ->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(no_identity) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orWhereRaw('LOWER(t_surat.nomor_agenda) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_surat.nomor_surat) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_surat.asal_surat) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_surat.hal) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_surat.untuk) LIKE ?', [strtolower($searchTerm)]);
            });
        });


        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';

                // User Login
                $userLogin = auth()->user();

                // Find Disposisi Diproses
                $letterDisposition = LetterDisposition::with('disposition.approvers')->where('surat_id', $row->id)->where('status', 'Diproses')->first();

                // Included Role Approvers
                $approverRoleIds = [];
                if (isset($letterDisposition->disposition)) {
                    foreach ($letterDisposition->disposition->approvers as $dis) {
                        array_push($approverRoleIds, $dis->role_id);
                    }
                }

                // Button Edit (Admin, TU)
                if ($userLogin->role_id == 1 || $userLogin->role_id == 3) {
                    if ($row->status != 'Selesai' && $row->status != 'Ditolak') {
                        $button .= '<button class="btn btn-sm btn-warning" data-mode="edit" data-integrity="' . $row->id . '" data-toggle="modal" data-target="#ModalAddEdit"><i class="fa fa-edit"></i></button>';
                    }
                }

                // Button Edit Pemohon
                if ($userLogin->role_id == 2 && $row->tanggal_diterima == null && $row->status == 'Diproses') {
                    $button .= '<button class="btn btn-sm btn-warning" data-mode="edit" data-integrity="' . $row->id . '" data-toggle="modal" data-target="#ModalAddEditPemohon"><i class="fa fa-edit"></i></button>';
                }

                // Button Disposisi (Included approverRoleIds)
                if ($row->status != 'Selesai' && $letterDisposition && in_array($userLogin->role_id, $approverRoleIds)) {
                    $button .= '<button class="btn btn-sm btn-info" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDisposition"><i class="fa fa-arrow-right"></i></button>';
                } else if ($userLogin->role_id == 3 && $letterDisposition->disposisi_id == null) {
                    $button .= '<button class="btn btn-sm btn-info" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDisposition"><i class="fa fa-arrow-right"></i></button>';
                }

                // Button Add SPJ
                if ($row->spjs->count() == 0 && $row->status == 'Selesai' && $row->disertai_dana && $row->pemohon_id == $userLogin->id) {
                    $button .= '<a href="' . route('letter.spj', $row->id) . '" class="btn btn-sm btn-info" id="spj" data-integrity="' . $row->id . '"><i class="fa fa-book"></i> <small>SPJ</small></a>';
                }

                // Button Rating
                if ($row->status == "Selesai" && $row->pemohon_id == $userLogin->id) {
                    $button .= '<button type="button" data-toggle="modal" data-id="' . $row->id . '" data-target="#modalRating" class="btn btn-sm btn-info"><i class="fa fa-star"></i></button>';
                }

                // Button Detail
                $button .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDetail"><i class="fa fa-eye"></i></button>';

                // Button Delete (Admin)
                if ($userLogin->role_id == 1) {
                    $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('tanggal_surat', function ($row) {
                return $row->tanggal_surat ? date('d M Y', strtotime($row->tanggal_surat)) : '-';
            })
            ->editColumn('file.original_name', function ($row) {
                return $row->file ? '<a href="' . $row->file->file_url . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Dok Proposal</a>' : '-';
            })
            ->editColumn('sk.original_name', function ($row) {
                return $row->sk ? '<a href="' . $row->sk->file_url . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Dok SK</a>' : '-';
            })
            ->editColumn('disertai_dana', function ($row) {
                return $row->disertai_dana ? "Surat Pembayaran" : "Surat Masuk";
            })
            ->editColumn('tanggal_diterima', function ($row) {
                return $row->tanggal_diterima ? date('d M Y - H:i', strtotime($row->tanggal_diterima)) : '-';
            })
            ->editColumn('pemohon.name', function ($row) {
                return $row->pemohon->name . ' <br> <small>' . $row->pemohon->email . '</small>' . ' <br> <small>' . ($row->prodi ? 'Prodi: ' . $row->prodi->name : '') . '</small>';
            })
            ->editColumn('kode', function ($row) {
                return '' . $row->kode . ' <br> <small>Nomor Agenda: ' . $row->nomor_agenda . '</small>';
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
            ->rawColumns(['action', 'status', 'pemohon.name', 'file.original_name', 'sk.original_name', 'kode'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            if ($request->surat_id) {
                $letter = Letter::with('dispositions')->find($request->surat_id);

                DB::transaction(function () use ($letter, $request) {
                    $data = $request->all();
                    $data['proposal_id'] = $letter->proposal_id;
                    $data['perlu_sk'] = $request->pembuat_sk_id ? true : false;

                    if ($request->proposal_id) {
                        $file = $request->file('proposal_id');
                        if ($file->getSize() > 1048576) {
                            throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 1MB.");
                        }
                        $fileName = uniqid() . '_' . $file->getClientOriginalName();

                        $disk = 'pdf';
                        $uploadedPath = Storage::disk($disk)->put($disk, $file, 'public');
                        $url = Storage::disk($disk)->url($uploadedPath);

                        $media = Media::create([
                            "name" => $fileName,
                            "original_name" => $file->getClientOriginalName(),
                            "path" => $uploadedPath,
                            "file_url" => $url,
                        ]);

                        $data['proposal_id'] = $media->id;
                    }

                    if ($letter->dispositions()->count() > 0 && $request->tanggal_diterima != date('Y-m-d', strtotime($letter->dispositions[0]->tanggal_diterima))) {
                        LetterDisposition::where('surat_id', $letter->id)->where('urutan', 0)->update(['tanggal_diterima' => $request->tanggal_diterima]);
                    }

                    if (isset($request->disposisi_order)) {
                        // Un Removed disposition
                        $unRemovedDisposition = [];

                        foreach ($request->disposisi_order as $key => $disposisiId) {
                            $urutan = $key + 1;

                            $disposisi = Disposisi::find($disposisiId);
                            if (!$disposisi) {
                                throw new Exception("Disposisi tidak ditemukan");
                            }

                            $existingLetterDisposition = LetterDisposition::where('surat_id', $letter->id)->where('disposisi_id', $disposisi->id)->where('urutan', $urutan)->first();
                            if ($existingLetterDisposition) {
                                array_push($unRemovedDisposition, $existingLetterDisposition->id);
                                continue;
                            }

                            $movedLetterDisposition = LetterDisposition::where('surat_id', $letter->id)->where('disposisi_id', $disposisi->id)->where('urutan', '!=', $urutan)->first();
                            if ($movedLetterDisposition) {
                                $movedLetterDisposition->update([
                                    'status' => null,
                                    'tanggal_diterima' => null,
                                    'tanggal_diproses' => null,
                                    'verifikator_id' => null,
                                    'verifikator_role_id' => null,
                                    'keterangan' => null,
                                    'urutan' => $urutan,
                                ]);
                                array_push($unRemovedDisposition, $movedLetterDisposition->id);

                                LetterDisposition::where('surat_id', $letter->id)->where('urutan', 0)->update([
                                    'status' => 'Diproses',
                                    'tanggal_diproses' => null,
                                    'verifikator_id' => null,
                                    'verifikator_role_id' => null,
                                    'keterangan' => null,
                                ]);
                                $letter->update(['status' => 'Diproses']);

                                continue;
                            } else {
                                $letterDisposition = LetterDisposition::create([
                                    'surat_id' => $letter->id,
                                    'disposisi_id' => $disposisi->id,
                                    'tanggal_diterima' => null,
                                    'status' => null,
                                    'urutan' => $urutan,
                                ]);
                                array_push($unRemovedDisposition, $letterDisposition->id);
                                continue;
                            }
                        }

                        // Remove disposition
                        if (count($unRemovedDisposition) > 0) {
                            LetterDisposition::where('surat_id', $letter->id)->where('disposisi_id', '!=', null)->whereNotIn('id', $unRemovedDisposition)->delete();
                        }
                    }

                    $letter->update($data);
                });

                return response()->json(['res' => 'success', 'msg' => 'Data berhasil diubah'], Response::HTTP_OK);
            } else {
                $nomorAgenda = Letter::whereYear('created_at', now()->year)->count() + 1;

                DB::transaction(function () use ($request, $nomorAgenda) {
                    $latestLetter = Letter::query()->latest()->first();
                    $code = $latestLetter ? sprintf('P' . date('Ym') . '%03s', substr($latestLetter->kode, 7) + 1) : 'P' . date('Ym') . '001';

                    $file = $request->file('proposal_id');
                    if ($file->getSize() > 1048576) {
                        throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 1MB.");
                    }
                    $fileName = uniqid() . '_' . $file->getClientOriginalName();

                    $disk = 'pdf';
                    $uploadedPath = Storage::disk($disk)->put($disk, $file, 'public');
                    $url = Storage::disk($disk)->url($uploadedPath);

                    $media = Media::create([
                        "name" => $fileName,
                        "original_name" => $file->getClientOriginalName(),
                        "path" => $uploadedPath,
                        "file_url" => $url,
                    ]);

                    $data = $request->all();
                    $data['kode'] = $code;
                    $data['status'] = 'Diproses';
                    $data['disertai_dana'] = $request->disertai_dana == "1";
                    $data['proposal_id'] = $media->id;
                    $data['nomor_agenda'] = $nomorAgenda;
                    $data['perlu_sk'] = $request->pembuat_sk_id ? true : false;
                    $data['role_id'] = auth()->user()->role_id;
                    $data['prodi_id'] = auth()->user()->prodi_id;
                    if (auth()->user()->role_id != 3) {
                        $data['pemohon_id'] = auth()->user()->id;
                    }

                    $app = Letter::create($data);

                    // Create disposition history
                    LetterDisposition::create([
                        'surat_id' => $app->id,
                        'disposisi_id' => null, // null indicate tata usaha
                        'tanggal_diterima' => $request->tanggal_diterima,
                        'status' => 'Diproses',
                        'urutan' => 0,
                    ]);

                    // Create disposisi history by order
                    if (isset($request->disposisi_order)) {
                        foreach ($request->disposisi_order as $key => $disposisiId) {
                            $disposisi = Disposisi::find($disposisiId);

                            LetterDisposition::create([
                                'surat_id' => $app->id,
                                'disposisi_id' => $disposisi->id,
                                'tanggal_diterima' => null,
                                'status' => null,
                                'urutan' => $key + 1,
                            ]);
                        }
                    }
                });

                return response()->json(['res' => 'success', 'msg' => 'Data berhasil ditambahkan'], Response::HTTP_CREATED);
            }
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show(Letter $letter)
    {
        // check the user is not pemohon
        if (auth()->user()->role_id == 2 && $letter->pemohon_id != auth()->user()->id) {
            return response()->json([]);
        }

        $letter = $letter->load('pemohon', 'ratings', 'prodi', 'role', 'file', 'sk', 'pembuat_sk', 'dispositions.surat', 'dispositions.disposition', 'dispositions.verifikator', 'dispositions.verifikatorRole');

        $selesaiDalam = '-';
        if ($letter->tanggal_diterima && $letter->tanggal_selesai) {
            $selesaiDalam = Carbon::parse($letter->tanggal_diterima)
                ->diff(\Carbon\Carbon::parse($letter->tanggal_selesai))
                ->format('%m bulan, %d hari, %h jam, %i menit');
        }

        return response()->json([
            'letter' => $letter,
            'selesai_dalam' => $selesaiDalam,
        ]);
    }

    public function update(Request $request, Letter $letter): JsonResponse
    {
        try {
            $data = $request->all();
            $data['proposal_id'] = $letter->proposal_id;

            if ($request->proposal_id) {
                $file = $request->file('proposal_id');
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

                $data['proposal_id'] = $media->id;
            }

            $letter->update($data);
            return response()->json(['res' => 'success', 'msg' => 'Data berhasil diubah'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy(Letter $letter): JsonResponse
    {
        try {
            DB::transaction(function () use ($letter) {
                // Remove spj
                $letter = $letter->load('spjs');
                foreach ($letter->spjs as $spj) {
                    $spj->ratings()->delete();
                    $spj->histories()->delete();
                    $spj->documents()->delete();
                    $spj->delete();
                }

                // Remove disposition & letter
                $letter->dispositions()->delete();
                $letter->delete();
            });
            return response()->json(['res' => 'success'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    public function disposition(Request $request, Letter $letter)
    {
        try {
            // Find letter where status diproses
            $currentDisposition = LetterDisposition::where('surat_id', $letter->id)->where('status', 'Diproses')->first();

            DB::transaction(function () use ($currentDisposition, $request, $letter) {
                if ($request->status == 'Setujui') {
                    $nextDisposition = LetterDisposition::with('disposition')
                        ->where("urutan", $currentDisposition->urutan + 1)
                        ->where('surat_id', $letter->id)
                        ->where("status", null)
                        ->first();

                    if (!$nextDisposition) {
                        $applicatoinStatus = 'Selesai';

                        // Create letter tu
                        LetterDisposition::create([
                            'surat_id' => $letter->id,
                            'position_id' => null,
                            'disposisi_id' => null,
                            'tanggal_diterima' => now(),
                            'status' => 'Disetujui',
                            'urutan' => $currentDisposition->urutan + 1
                        ]);
                    } else {
                        $applicatoinStatus =  'Menunggu Approval ' . $nextDisposition->disposition->name;

                        // next disposisi update
                        $nextDisposition->update([
                            'status' => 'Diproses',
                            'tanggal_diterima' => now(),
                        ]);
                    }

                    $skfileId = null;
                    if ($request->sk_id) {
                        $file = $request->file('sk_id');
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

                        $skfileId  = $media->id;
                    }

                    // Update letter status
                    $letter->update([
                        'status' => $applicatoinStatus,
                        'tanggal_selesai' => $applicatoinStatus == 'Selesai' ? now() : null,
                        'sk_id' => $request->sk_id ? $skfileId : $letter->sk_id,
                    ]);
                } else {
                    $letter->update([
                        'status' => 'Ditolak',
                        'alasan_penolakan' => $request->keterangan
                    ]);
                }

                // Update prev letter history
                $currentDisposition->update([
                    'tanggal_diproses' => now(),
                    'verifikator_id' => auth()->user()->id,
                    'keterangan' => $request->keterangan,
                    'status' => $request->status == "Setujui" ? "Disetujui" : "Ditolak",
                ]);
            });

            return response()->json(['res' => 'success', 'msg' => 'Berhasil disposisi'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    public function targetDisposition(Letter $letter)
    {
        $currentDisposition = LetterDisposition::with('surat.pembuat_sk')->where('surat_id', $letter->id)->where('status', 'Diproses')->first();
        $nextDisposition = LetterDisposition::with('disposition')
            ->where("urutan", $currentDisposition->urutan + 1)
            ->where('surat_id', $letter->id)
            ->where("status", null)
            ->first();
        $nextDispositionName = "TU";

        return response()->json([
            "needUpdate" =>  $currentDisposition->surat->tanggal_diterima == null,
            "nextDisposition" => !$nextDisposition ? $nextDispositionName : $nextDisposition->disposition->name,
            "perlu_sk" => $currentDisposition->surat->perlu_sk
                ? $currentDisposition->surat->pembuat_sk_id == $currentDisposition->disposisi_id
                : false,
        ]);
    }

    public function confirmation(Request $request, Letter $letter)
    {
        try {
            // Find letter where status diproses
            $appHistory = LetterDisposition::where('surat_id', $letter->id)->where('status', 'Diproses')->first();

            DB::transaction(function () use ($appHistory, $request, $letter) {
                // Update letter status
                $letter->update([
                    'status' => $request->status,
                ]);

                // Update prev application history
                $appHistory->update([
                    'approved_date' => now(),
                    'approved_by_id' => auth()->user()->id,
                    'notes' => 'Selesai',
                    'status' => 'Disetujui',
                ]);
            });

            return response()->json(['res' => 'success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    public function spj(Letter $letter): View
    {
        // check the user is not pemohon
        if (auth()->user()->role_id == 2 && $letter->pemohon_id != auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('letter.spj', compact('letter'));
    }

    public function getRating(Letter $letter)
    {
        $rating = SPJRating::where('surat_id', $letter->id)->where('user_id', request()->user()->id)->first();
        return response()->json(['data' => $rating], 200);
    }

    public function rating(Request $request)
    {
        $rating = SPJRating::where('surat_id', $request->surat_id)->where('user_id', $request->user()->id)->first();
        if ($rating) {
            $rating->update([
                'rating' => (int)$request->rating,
                'catatan' => $request->catatan,
            ]);
        } else {
            SPJRating::create([
                'surat_id' => $request->surat_id,
                'user_id' => $request->user()->id,
                'rating' => (int)$request->rating,
                'catatan' => $request->catatan,
                'tipe' => 'PENGAJUAN',
            ]);
        }
        return redirect()->route('letter.index')->with('success', 'Rating berhasil ditetapkan.');
    }
}
