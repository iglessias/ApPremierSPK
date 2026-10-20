<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::all();
        return view('criteria.index', compact('criteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:criteria,code',
            'name' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0|max:1',
            'type' => 'required|in:benefit,cost'
        ]);

        Criterion::create($request->all());
        return redirect()->route('criteria.index')->with('success', 'Kriteria baru berhasil ditambahkan!');
    }

    public function update(Request $request, Criterion $criterion)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0|max:1',
            'type' => 'required|in:benefit,cost'
        ]);

        $criterion->update($request->only('name', 'weight', 'type'));
        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy(Criterion $criterion)
    {
        $criterion->delete();
        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil dihapus!');
    }
}
