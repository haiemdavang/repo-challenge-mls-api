<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'firstname',
        'lastname',
        'email',
        'phone',
        'address',
        'birthday',
        'gender',
        'id_number',
        'department',
        'is_active' // Thay cho deleted, dùng soft delete hoặc flag
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'birthday' => 'date',
        'is_active' => 'boolean',
    ];

    // Helper: Họ tên đầy đủ
    public function getFullNameAttribute()
    {
        return "{$this->lastname} {$this->firstname}";
    }

    // Relationship: Lấy tất cả khóa học mà user tham gia (bất kể vai trò)
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot('role_id') // Lấy thêm cột role_id trong bảng trung gian
            ->withTimestamps();
    }

    // Helper: Lấy khóa học mà user là Giáo viên
    public function teachingCourses()
    {
        return $this->courses()->wherePivot('role_id', Role::TEACHER);
    }

    // Helper: Lấy khóa học mà user là Học sinh
    public function studyingCourses()
    {
        return $this->courses()->wherePivot('role_id', Role::STUDENT);
    }
}
