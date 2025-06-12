<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
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
        $pemohon = User::where("role_id", 2)->get();
        $disposisi = Disposisi::orderBy("urutan", "asc")->get();
        $nomorAgenda = Letter::whereYear('created_at', now()->year)->count() + 1;
        return view('letter.index', compact('pemohon', 'disposisi', 'nomorAgenda'));
    }

    public function data(): JsonResponse
    {
        $app = Letter::with(['pemohon', 'spjs', 'file']);

        if (auth()->user()->role_id === 2) {
            $app->where("t_letter.pemohon_id", auth()->user()->id);
        }

        $app->when(request('status'), function ($app) {
            $app->where('t_letter.status', request('status'));
        });
        $app->when(request('pemohon_id'), function ($app) {
            $app->where('t_letter.pemohon_id', request('pemohon_id'));
        });
        $app->when(request('disertai_dana'), function ($app) {
            $app->where('t_letter.disertai_dana', request('disertai_dana') == "Ya");
        });
        $app->when(request('search'), function ($app) {
            $searchTerm = "%" . request('search') . "%";

            $app->where('t_letter.kode', 'ilike', $searchTerm)
                ->orWhereHas('pemohon', function ($q) use ($searchTerm) {
                    $q->where('name', 'ilike', $searchTerm);
                    $q->orWhere('no_identity', 'ilike', $searchTerm);
                });
        });

        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $appHistory = LetterDisposition::where('letter_id', $row->id)->where('status', 'Diproses')->first();

                $button = '<div class="btn-group pull-right">';

                if ($row->status != 'Selesai' && $appHistory && $appHistory->position_id == auth()->user()->role_id) {
                    $button .= '<button class="btn btn-sm btn-info" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDisposition"><i class="fa fa-arrow-right"></i></button>';
                }

                if ($row->spjs->count() == 0 && $row->status == 'Selesai' && $row->disertai_dana && $row->pemohon_id == auth()->user()->id) {
                    $button .= '<a href="' . route('letter.spj', $row->id) . '" class="btn btn-sm btn-info" id="spj" data-integrity="' . $row->id . '"><i class="fa fa-book"></i> <small>SPJ</small></a>';
                }

                if (auth()->user()->role->name == 'Admin' || auth()->user()->role->name == 'TU') {
                    if ($row->status != 'Selesai' && $row->status != 'Ditolak' && $row->status != 'Menunggu Konfirmasi TU') {
                        $button .= '<button class="btn btn-sm btn-warning" data-mode="edit" data-integrity="' . $row->id . '" data-toggle="modal" data-target="#ModalAddEdit"><i class="fa fa-edit"></i></button>';
                    }
                }

                $button .= '<button class="btn btn-sm btn-primary" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDetail"><i class="fa fa-eye"></i></button>';

                if (auth()->user()->role->name == 'Admin') {
                    $button .= '<button class="btn btn-sm btn-danger" id="delete" data-integrity="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                }

                $button .= '</div>';
                return $button;
            })
            ->editColumn('tanggal_surat', function ($row) {
                return $row->tanggal_surat ? date('d M Y', strtotime($row->tanggal_surat)) : '-';
            })
            ->editColumn('file.original_name', function ($row) {
                return $row->file ? '<a href="' . $row->file->file_url . '" target="_blank"><i class="fa fa-file-pdf-o"></i> Dokumen Pengajuan</a>' : '-';
            })
            ->editColumn('disertai_dana', function ($row) {
                return $row->disertai_dana ? "Surat Pembayaran" : "Surat Masuk";
            })
            ->editColumn('tanggal_diterima', function ($row) {
                return $row->tanggal_diterima ? date('d M Y', strtotime($row->tanggal_diterima)) : '-';
            })
            ->editColumn('pemohon.name', function ($row) {
                return '' . $row->pemohon->name . ' <br> <small>(' . $row->pemohon->no_identity . ')</small>';
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
            ->rawColumns(['action', 'status', 'pemohon.name', 'file.original_name'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        try {
            if ($request->letter_id) {
                $letter = Letter::with('dispositions')->find($request->letter_id);

                $data = $request->all();
                $data['proposal_file'] = $letter->proposal_file;

                if ($request->proposal_file) {
                    $file = $request->file('proposal_file');
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

                    $data['proposal_file'] = $media->id;
                }

                if ($letter->dispositions()->count()  > 0 && $request->tanggal_diterima != date('Y-m-d', strtotime($letter->dispositions[0]->tanggal_diterima))) {
                    LetterDisposition::where('id', $letter->dispositions[0]->id)->update(['tanggal_diterima' => $request->tanggal_diterima]);
                }

                if (isset($request->disposisi_order)) {
                    foreach ($request->disposisi_order as $key => $disposisiId) {
                        $disposisi = Disposisi::find($disposisiId);

                        LetterDisposition::create([
                            'letter_id' => $letter->id,
                            'position_id' => $disposisi->approver_id,
                            'disposisi_id' => $disposisi->id,
                            'tanggal_diterima' => null,
                            'status' => null,
                            'urutan' => $key + 1,
                        ]);
                    }
                }

                $letter->update($data);

                return response()->json(['res' => 'success', 'msg' => 'Data berhasil diubah'], Response::HTTP_OK);
            } else {
                $nomorAgenda = Letter::whereYear('created_at', now()->year)->count() + 1;

                DB::transaction(function () use ($request, $nomorAgenda) {
                    $latestLetter = Letter::query()->latest()->first();
                    $code = $latestLetter ? sprintf('P' . date('Ym') . '%03s', substr($latestLetter->kode, 7) + 1) : 'P' . date('Ym') . '001';

                    $file = $request->file('proposal_file');
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

                    $data = $request->all();
                    $data['kode'] = $code;
                    $data['status'] = 'Diproses';
                    $data['disertai_dana'] = $request->disertai_dana == "1";
                    $data['proposal_file'] = $media->id;
                    $data['nomor_agenda'] = $nomorAgenda;

                    if (auth()->user()->role_id == 2) {
                        $data['pemohon_id'] = auth()->user()->id;
                    }

                    $app = Letter::create($data);

                    // Create disposition history
                    $disposition = Role::where('id', 3)->first();
                    LetterDisposition::create([
                        'letter_id' => $app->id,
                        'position_id' => $disposition->id,
                        'disposisi_id' => null,
                        'tanggal_diterima' => $request->tanggal_diterima,
                        'status' => 'Diproses',
                        'urutan' => 0,
                    ]);

                    // Create disposisi history by order
                    if (isset($request->disposisi_order)) {
                        foreach ($request->disposisi_order as $key => $disposisiId) {
                            $disposisi = Disposisi::find($disposisiId);

                            LetterDisposition::create([
                                'letter_id' => $app->id,
                                'position_id' => $disposisi->approver_id,
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
        return $letter->load('pemohon', 'file', 'dispositions.letter', 'dispositions.position', 'dispositions.verifikator', 'dispositions.disposition');
    }

    public function update(Request $request, Letter $letter): JsonResponse
    {
        try {
            $data = $request->all();
            $data['proposal_file'] = $letter->proposal_file;

            if ($request->proposal_file) {
                $file = $request->file('proposal_file');
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

                $data['proposal_file'] = $media->id;
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
            $currentDisposition = LetterDisposition::where('letter_id', $letter->id)->where('status', 'Diproses')->first();

            DB::transaction(function () use ($currentDisposition, $request, $letter) {
                if ($request->status == 'Setujui') {
                    $nextDisposition = LetterDisposition::with('disposition')
                        ->where("urutan", $currentDisposition->urutan + 1)
                        ->where('letter_id', $letter->id)
                        ->where("status", null)
                        ->first();

                    if (!$nextDisposition) {
                        $applicatoinStatus = 'Selesai';

                        // Create letter tu
                        LetterDisposition::create([
                            'letter_id' => $letter->id,
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

                    // Update letter status
                    $letter->update(['status' => $applicatoinStatus]);
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
        $currentDisposition = LetterDisposition::where('letter_id', $letter->id)->where('status', 'Diproses')->first();
        $nextDisposition = LetterDisposition::with('disposition')
            ->where("urutan", $currentDisposition->urutan + 1)
            ->where('letter_id', $letter->id)
            ->where("status", null)
            ->first();
        if (!$nextDisposition) {
            return "TU";
        }
        return $nextDisposition->disposition->name;
    }

    public function confirmation(Request $request, Letter $letter)
    {
        try {
            // Find letter where status diproses
            $appHistory = LetterDisposition::where('letter_id', $letter->id)->where('status', 'Diproses')->first();

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
        return view('letter.spj', compact('letter'));
    }
}
