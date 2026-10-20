<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $totalDepts = Department::count();
        $totalCriteria = Criterion::count();
        
        $rankings = Evaluation::with(['employee.department'])
            ->where('period', date('Y-m'))
            ->get()
            ->groupBy('employee_id')
            ->map(function ($evals) {
                $first = $evals->first();
                $first->total_score = $evals->sum('total_score');
                return $first;
            })
            ->sortBy('rank')
            ->take(10);

        return view('dashboard', compact('totalEmployees', 'totalDepts', 'totalCriteria', 'rankings'));
    }
}
