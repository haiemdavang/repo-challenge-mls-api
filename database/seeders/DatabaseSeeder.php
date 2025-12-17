<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\Category;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 3 Role Cứng (Dùng Factory state để chuẩn hóa)
        // Dùng firstOrCreate để tránh lỗi nếu chạy seeder nhiều lần
        $roleManager = Role::firstOrCreate(['shortname' => 'manager'], [
            'name' => 'Ban Giám Hiệu',
            'description' => 'Quản trị hệ thống'
        ]);
        $roleTeacher = Role::firstOrCreate(['shortname' => 'teacher'], [
            'name' => 'Giáo viên',
            'description' => 'Giảng dạy và chấm điểm'
        ]);
        $roleStudent = Role::firstOrCreate(['shortname' => 'student'], [
            'name' => 'Học sinh',
            'description' => 'Tham gia học tập'
        ]);

        // 2. Tạo Admin User
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@school.edu',
            'password' => bcrypt('password'), // Pass mặc định
            'firstname' => 'Admin',
            'lastname' => 'System',
            'role_id' => $roleManager->id, // Gán role Manager
        ]); // Admin thì không cần gán vào course_user, hoặc tùy logic của bạn

        // 3. Tạo Danh mục (Categories)
        $categories = Category::factory()->count(5)->create();

        // 4. Tạo Giáo viên (10 người)
        $teachers = User::factory()->count(10)->teacher()->create([
            'role_id' => $roleTeacher->id, // Gán role Teacher
        ]);

        // 5. Tạo Học sinh (50 người)
        $students = User::factory()->count(50)->student()->create([
            'role_id' => $roleStudent->id, // Gán role Student
        ]);

        // 6. Tạo Khóa học & Gán người dùng
        foreach ($categories as $category) {
            // Mỗi category tạo 2-3 khóa học
            $courses = Course::factory()
                ->count(rand(2, 3))
                ->for($category) // Gán category_id
                ->has(Module::factory()->count(5)) // Mỗi khóa có 5 bài học
                ->create();

            foreach ($courses as $course) {
                // A. Gán 1 Giáo viên chủ nhiệm cho lớp
                // Insert thẳng vào bảng trung gian
                DB::table('course_user')->insert([
                    'course_id' => $course->id,
                    'user_id' => $teachers->random()->id,
                    'role_id' => $roleTeacher->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // B. Gán 15-20 Học sinh vào lớp
                $randomStudents = $students->random(rand(15, 20));
                foreach ($randomStudents as $student) {
                    // Check để tránh trùng lặp (dù random ít khi trùng nhưng cẩn thận vẫn hơn)
                    $exists = DB::table('course_user')
                        ->where('course_id', $course->id)
                        ->where('user_id', $student->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('course_user')->insert([
                            'course_id' => $course->id,
                            'user_id' => $student->id,
                            'role_id' => $roleStudent->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
