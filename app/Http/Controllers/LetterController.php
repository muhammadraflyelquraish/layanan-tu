<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class LetterController extends Controller
{
    public function index(): View
    {
        $pemohon = User::where("role_id", 2)->get();
        return view('letter.index', compact('pemohon'));
    }

    public function data(): JsonResponse
    {
        $app = Letter::with(['pemohon', 'spjs', 'file']);

        if (auth()->user()->role_id == 2) {
            $app->where("pemohon_id", auth()->user()->role_id);
        }

        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $appHistory = LetterDisposition::where('letter_id', $row->id)->where('status', 'Diproses')->first();

                $button = '<div class="btn-group pull-right">';

                if ($appHistory && $appHistory->position_id == auth()->user()->role_id) {
                    $button .= '<button class="btn btn-sm btn-info" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDisposition"><i class="fa fa-arrow-right"></i></button>';
                }

                if ($row->spjs->count() == 0 && $row->status == 'Selesai' && $row->disertai_dana) {
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
                return $row->file ? '<a href="#" >' . $row->file->original_name . '</a>' : '-';
            })
            ->editColumn('disertai_dana', function ($row) {
                return $row->disertai_dana ? "Disertai Pengajuan Dana" : "Tanpa Pengajuan Dana";
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
            DB::transaction(function () use ($request) {
                $latestLetter = Letter::query()->latest()->first();
                $code = $latestLetter ? sprintf('P' . date('Ym') . '%03s', substr($latestLetter->kode, 7) + 1) : 'P' . date('Ym') . '001';

                $file = $request->file('proposal_file');
                if ($file->getSize() > 614400) {
                    throw new \Exception("Ukuran file '" . $file->getClientOriginalName() . "' tidak boleh lebih dari 600 KB.");
                }
                $fileName = uniqid() . '_' . $file->getClientOriginalName();

                $media = Media::create([
                    "name" => $fileName,
                    "original_name" => $file->getClientOriginalName(),
                    "file_url" => "",
                ]);

                $data = $request->all();
                $data['kode'] = $code;
                $data['status'] = 'Diproses';
                $data['disertai_dana'] = $request->disertai_dana == "1";
                $data['proposal_file'] = $media->id;

                if (auth()->user()->role_id == 2) {
                    $data['pemohon_id'] = auth()->user()->id;
                }

                $app = Letter::create($data);

                // Create disposition history
                $disposition = Role::where('id', 3)->first();
                LetterDisposition::create([
                    'letter_id' => $app->id,
                    'position_id' => $disposition->id,
                    'tanggal_diterima' => $request->tanggal_diterima,
                    'status' => 'Diproses',
                ]);
            });

            return response()->json(['res' => 'success', 'msg' => 'Data berhasil ditambahkan'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show(Letter $letter)
    {
        return $letter->load('pemohon', 'file', 'dispositions.letter', 'dispositions.position', 'dispositions.verifikator');
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

                $media = Media::create([
                    "name" => $fileName,
                    "original_name" => $file->getClientOriginalName(),
                    "file_url" => "",
                ]);

                $data['proposal_file'] = $media->id;
            }

            $letter->update($data);
            return response()->json(['res' => 'success', 'msg' => 'Data berhasil diubah'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Letter::findOrFail($id)->delete();
            return response()->json(['res' => 'success'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    public function disposition(Request $request, Letter $letter)
    {
        try {
            // Find letter where status diproses
            $appHistory = LetterDisposition::with('position')->where('letter_id', $letter->id)->where('status', 'Diproses')->first();
            $position = Role::where('id', $request->position_id)->first();

            DB::transaction(function () use ($appHistory, $request, $letter, $position) {
                if ($request->status == 'Selesai') {
                    $letter->update(['status' => "Selesai"]);
                } else if ($request->status == 'Disposisi') {
                    // Position id default is follow the direction
                    // But if approved is 'Keuangan' will direcly to Staff TU
                    $positionId = $position ? $position->id : 3;
                    $applicatoinStatus = $appHistory->position->name == 'Keuangan' ? "Menunggu Konfirmasi TU" : 'Menunggu Approval ' . $position->name;

                    // Update letter status
                    $letter->update([
                        'status' => $applicatoinStatus,
                    ]);

                    // Create letter history
                    LetterDisposition::create([
                        'letter_id' => $letter->id,
                        'position_id' => $positionId,
                        'tanggal_diterima' => now(),
                        'status' => 'Diproses',
                    ]);
                } else {
                    $applicatoinStatus = 'Ditolak';

                    // Has been rejected
                    if ($appHistory->position->name == 'TU') {
                        // Update keterangan
                        $letter->update([
                            'status' => 'Ditolak',
                            'alasan_penolakan' => $request->keterangan
                        ]);
                    } else {
                        // Update keterangan
                        $letter->update([
                            'status' => 'Menunggu Konfirmasi TU',
                        ]);

                        // Create letter history
                        LetterDisposition::create([
                            'letter_id' => $letter->id,
                            'position_id' => 3,
                            'tanggal_diterima' => now(),
                            'status' => 'Diproses',
                        ]);
                    }
                }

                // Update prev letter history
                $appHistory->update([
                    'tanggal_diproses' => now(),
                    'verifikator_id' => auth()->user()->id,
                    'keterangan' => $request->keterangan,
                    'status' => $request->status == "Disposisi" || $request->status == "Selesai" ? "Disetujui" : "Ditolak",
                ]);
            });

            return response()->json(['res' => 'success', 'msg' => 'Berhasil disposisi'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['res' => 'error', 'msg' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
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
