<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class TrackingController extends Controller
{
    public function index(): View
    {
        return view('tracking');
    }

    public function data(): JsonResponse
    {
        $app = Letter::with(['pemohon', 'spjs', 'file', 'sk'])->orderBy('created_at', 'desc');

        if (auth()->user()->role_id == 2) {
            $app->where("t_surat.pemohon_id", auth()->user()->id);
        }

        $app->where(function ($query) {
            $query->where(function ($q) {
                $q->where('disertai_dana', false)
                    ->whereNotIn('status', ['Selesai', 'Ditolak']);
            })->orWhere(function ($q) {
                $q->where('disertai_dana', true)
                    ->whereNotIn('status', ['Ditolak'])
                    ->whereDoesntHave('spjs');
            });
        });

        return DataTables::of($app)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group pull-right">';
                $button .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-integrity="' . $row->id . '" data-target="#ModalDetail"><i class="fa fa-eye"></i></button>';
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
                return '' . $row->pemohon->name . ' <br> <small>(' . $row->pemohon->email . ')</small>';
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
            ->rawColumns(['action', 'status', 'pemohon.name', 'file.original_name', 'sk.original_name'])
            ->toJson();
    }
}
