<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::with('department')->where('role', 'employee')->get();
        $departments = Department::all();
        return view('employees.index', compact('employees', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:users,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string',
        ]);

        User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'position' => $request->position,
            'password' => bcrypt('password123'), // Default password
            'role' => 'employee'
        ]);

        return redirect()->route('employees.index')->with('success', 'Karyawan baru berhasil ditambahkan!');
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string',
        ]);

        $employee->update($request->only('name', 'department_id', 'position'));
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus!');
    }
}
