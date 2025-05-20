<?php

namespace App\Http\Controllers;

use App\Models\SPJCategory;
use Illuminate\Http\Request;

class SPJCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string']);

        $existingCategory = SPJCategory::whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])->first();
        if (!$existingCategory) {
            SPJCategory::create(["nama" => $request->nama]);
        }

        return redirect()->back();
    }
}
