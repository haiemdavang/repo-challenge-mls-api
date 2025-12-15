<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Để xóa mềm (thùng rác)

class Course extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'category_id',
        'fullname',
        'shortname',
        'summary',
        'start_date',
        'end_date',
        'is_visible',
        'format',
        'image_url'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_visible' => 'boolean',
    ];

    // Relationship: Thuộc danh mục nào (Khối 6, Khối 7...)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: Nội dung bài học (Modules)
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('section_order');
    }

    // Relationship: Lấy danh sách thành viên (cả GV và HS)
    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    // Helper: Lấy danh sách giáo viên của lớp này
    public function teachers()
    {
        return $this->users()->wherePivot('role_id', Role::TEACHER);
    }

    // Helper: Lấy danh sách học sinh của lớp này
    public function students()
    {
        return $this->users()->wherePivot('role_id', Role::STUDENT);
    }
}
