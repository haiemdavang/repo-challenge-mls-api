<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    protected $fillable = ['name', 'parent_id', 'is_visible', 'description'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Đệ quy danh mục cha-con
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
