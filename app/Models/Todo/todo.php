<?php

namespace App\Models\Todo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Task\Task;
class todo extends Model {

    use HasFactory,
        SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'status'];

    public function tasks() {
        return $this->hasMany(Task::class);
    }

}
