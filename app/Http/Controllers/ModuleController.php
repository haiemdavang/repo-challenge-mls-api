<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * GET /api/courses/{id}/modules
     * Get list of modules for a course, grouped by section (simulated).
     */
    public function index($courseId)
    {
        $course = Course::with(['teachers'])->findOrFail($courseId);

        $modules = $course->modules()
            ->where('is_visible', true)
            ->orderBy('section_order')
            ->get();

        return response()->json([
            'course_info' => [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'teacher' => $course->teachers->map(fn($t) => $t->full_name)->join(', '),
            ],
            'sections' => [
                [
                    'title' => 'Nội dung khóa học', // Default section since DB has no sections table
                    'modules' => ModuleResource::collection($modules)
                ]
            ]
        ]);
    }

    /**
     * GET /api/modules/{id}
     * Get detail of a single module.
     */
    public function show($id)
    {
        $module = Module::where('is_visible', true)->findOrFail($id);

        return new ModuleResource($module);
    }

    /**
     * POST /api/courses/{id}/modules
     * Add new module to course
     */
    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:assignment,resource,quiz',
            'content' => 'nullable|string',
            'file_url' => 'nullable|string',
            'section_order' => 'integer',
            'is_visible' => 'boolean'
        ]);

        $module = $course->modules()->create($validated);

        return new ModuleResource($module);
    }

    /**
     * PUT /api/modules/{id}
     * Update module content
     */
    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string',
            'type' => 'in:assignment,resource,quiz',
            'content' => 'nullable|string',
            'file_url' => 'nullable|string',
            'section_order' => 'integer',
            'is_visible' => 'boolean'
        ]);

        $module->update($validated);

        return new ModuleResource($module);
    }

    /**
     * DELETE /api/modules/{id}
     * Delete module
     */
    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();

        return response()->json(['message' => 'MODULE_DELETED']);
    }
}
