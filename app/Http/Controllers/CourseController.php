<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseUserResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\Role;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->where('is_visible', true);

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('shortname', 'like', "%{$search}%");
            });
        }

        $courses = $query->with('category')->paginate(10);

        return CourseResource::collection($courses);
    }

    public function show($id)
    {
        $course = Course::where('is_visible', true)
            ->with(['teachers', 'category'])
            ->findOrFail($id);

        return new CourseResource($course);
    }

    /**
     * POST /api/courses
     * Create a new course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string',
            'shortname' => 'required|string|unique:courses',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'summary' => 'nullable|string',
            'format' => 'string',
            'image_url' => 'nullable|url',
        ]);

        $course = Course::create($validated);

        // Assign current user as teacher
        $course->users()->attach($request->user()->id, ['role_id' => Role::TEACHER]);

        return new CourseResource($course);
    }

    /**
     * PUT /api/courses/{id}
     * Update course info
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'fullname' => 'string',
            'shortname' => 'string|unique:courses,shortname,' . $id,
            'category_id' => 'exists:categories,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'summary' => 'nullable|string',
            'is_visible' => 'boolean',
            'format' => 'string',
            'image_url' => 'nullable|url',
        ]);

        $course->update($validated);

        return new CourseResource($course);
    }

    /**
     * POST /api/courses/{id}/enroll
     * Enroll a student into the course
     */
    public function enroll(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $course = Course::findOrFail($id);
        $studentId = $request->user_id;

        // Check if already enrolled
        if (!$course->users()->where('user_id', $studentId)->exists()) {
            $course->users()->attach($studentId, ['role_id' => Role::STUDENT]);
        }

        return response()->json(['message' => 'ENROLL_SUCCESS']);
    }

    /**
     * GET /api/courses/{id}/students
     * Get list of students in the course
     */
    public function students($id)
    {
        $course = Course::findOrFail($id);
        $students = $course->students()->get();

        return BaseUserResource::collection($students);
    }
}
