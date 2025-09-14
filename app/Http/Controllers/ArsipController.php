<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\Prodi;
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
        // User Login
        $userLogin = auth()->user();

        $pemohon = User::where("role_id", "!=", 3)->get();
        $disposisi = Disposisi::orderBy("urutan", "asc")->get();
        $prodi = Prodi::query();
        if ($userLogin->role_id == 7 || $userLogin->role_id == 8) {
            $prodi->where("id", $userLogin->prodi_id);
        }
        $prodi = $prodi->get();
        return view('arsip.index', compact('pemohon', 'disposisi', 'prodi'));
    }

    public function data(): JsonResponse
    {
        // User Login
        $userLogin = auth()->user();

        $app = Letter::query()->with(['pemohon', 'spjs', 'file', 'sk'])
            ->leftJoin('t_user as u', 'u.id', '=', 't_surat.pemohon_id')
            ->select('t_surat.*',  'u.name as user_name', 'u.email as user_email');

        if ($userLogin->role_id == 2 || $userLogin->role_id == 6) { // Role (Pemohon, Dosen)
            $app->where("t_surat.pemohon_id", $userLogin->id);
        } else if ($userLogin->role_id == 7 || $userLogin->role_id == 8) { // Role (Prodi, Sek Prodi)
            $app->where("t_surat.prodi_id", $userLogin->prodi_id);
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
                $userLogin = auth()->user();

                $button = '<div class="btn-group pull-right">';
                $button .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDetail"><i class="fa fa-eye"></i></button>';
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
            ->rawColumns(['action', 'status', 'kode', 'pemohon.name', 'file.original_name', 'sk.original_name'])
            ->toJson();
    }

    public function show(Letter $letter)
    {
        return $letter->load('pemohon', 'ratings', 'prodi', 'role', 'file', 'sk', 'pembuat_sk', 'dispositions.surat', 'dispositions.disposition', 'dispositions.verifikator', 'dispositions.verifikatorRole');
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
