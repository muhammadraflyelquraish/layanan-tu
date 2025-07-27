<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class ArsipController extends Controller
{
    public function index(): View
    {
        $pemohon = User::where("role_id", 2)->get();
        return view('arsip.index', compact('pemohon'));
    }

    public function data(): JsonResponse
    {
        $app = Letter::query()->with(['pemohon', 'spjs', 'file', 'sk'])
            ->leftJoin('t_user as u', 'u.id', '=', 't_letter.pemohon_id')
            ->select('t_letter.*',  'u.name as user_name', 'u.email as user_email');

        if (auth()->user()->role_id === 2) {
            $app->where("t_letter.pemohon_id", auth()->user()->id);
        }

        $app->when(request('status'), function ($query) {
            $query->where('t_letter.status', request('status'));
        });

        $app->when(request('pemohon_id'), function ($query) {
            $query->where('t_letter.pemohon_id', request('pemohon_id'));
        });

        $app->when(request('disertai_dana'), function ($query) {
            $query->where('t_letter.disertai_dana', request('disertai_dana') == "Ya");
        });

        $app->when(request('search'), function ($query) {
            $searchTerm = "%" . request('search') . "%";

            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(t_letter.kode) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereHas('pemohon', function ($subQ) use ($searchTerm) {
                        $subQ->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(no_identity) LIKE ?', [strtolower($searchTerm)])
                            ->orWhereRaw('LOWER(email) LIKE ?', [strtolower($searchTerm)]);
                    })
                    ->orWhereRaw('LOWER(t_letter.nomor_agenda) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_letter.nomor_surat) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_letter.asal_surat) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_letter.hal) LIKE ?', [strtolower($searchTerm)])
                    ->orWhereRaw('LOWER(t_letter.untuk) LIKE ?', [strtolower($searchTerm)]);
            });
        });

        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDetail"><i class="fa fa-eye"></i></button>';
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
                return '' . $row->pemohon->name . ' <br> <small>' . $row->pemohon->email . '</small>';
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
            ->rawColumns(['action', 'status', 'kode', 'pemohon.name', 'file.original_name', 'sk.original_name'])
            ->toJson();
    }

    public function show(Letter $letter)
    {
        return $letter->load('pemohon', 'file', 'sk', 'dispositions.letter', 'dispositions.position', 'dispositions.verifikator', 'dispositions.disposition');
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
}
