<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Todo\Todo;

class task extends Model {

    use HasFactory,
        SoftDeletes;

    protected $fillable = ['title', 'slug', 'todo_id', 'status', 'due_date'];

    public function todos() {
        return $this->belongsTo(Todo::class, 'todo_id');
    }

}
