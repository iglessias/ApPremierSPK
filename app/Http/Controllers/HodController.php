<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HodController extends Controller
{
    public function index()
    {
        $hods = User::with('department')->where('role', 'hod')->get();
        $departments = Department::all();
        return view('hods.index', compact('hods', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:users,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'password' => 'required|min:6',
        ]);

        User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'position' => 'Head of Department',
            'password' => Hash::make($request->password),
            'role' => 'hod'
        ]);

        return redirect()->route('hods.index')->with('success', 'Akun HOD baru berhasil ditambahkan!');
    }

    public function update(Request $request, User $hod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $data = $request->only('name', 'department_id');
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $hod->update($data);
        return redirect()->route('hods.index')->with('success', 'Data HOD berhasil diperbarui!');
    }

    public function destroy(User $hod)
    {
        $hod->delete();
        return redirect()->route('hods.index')->with('success', 'Akun HOD berhasil dihapus!');
    }
}
