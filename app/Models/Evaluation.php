<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['employee_id', 'evaluator_id', 'period', 'status', 'total_score', 'rank'];

    public function employee() { return $this->belongsTo(User::class, 'employee_id'); }
    public function evaluator() { return $this->belongsTo(User::class, 'evaluator_id'); }
    public function details() { return $this->hasMany(EvaluationDetail::class); }
}

// Separate files would be cleaner, but I can put them together. 
// No, I'll put them in separate files as per Laravel standards.
