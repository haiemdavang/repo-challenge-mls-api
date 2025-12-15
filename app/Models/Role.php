<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';

    // Định nghĩa hằng số để code dễ đọc hơn
    const MANAGER = 1;
    const TEACHER = 2;
    const STUDENT = 3;

    protected $fillable = ['name', 'shortname', 'description'];
}
