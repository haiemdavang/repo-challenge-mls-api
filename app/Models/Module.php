<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table = 'modules';

    protected $fillable = [
        'course_id',
        'type',        // assignment, resource, quiz
        'title',       // Tên bài
        'content',     // Mô tả hoặc nội dung HTML
        'file_url',    // Link tài liệu
        'section_order', // Thứ tự hiển thị
        'is_visible'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
