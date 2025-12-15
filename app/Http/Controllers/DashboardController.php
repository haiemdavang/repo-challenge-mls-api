<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/my-courses
     * List courses the current user is enrolled in.
     */
    public function myCourses(Request $request)
    {
        $user = $request->user();
        $query = $user->courses()->where('is_visible', true);

        // Filter by status based on dates
        if ($request->has('status')) {
            $now = now();
            switch ($request->status) {
                case 'inprogress':
                    $query->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                    break;
                case 'finished':
                    $query->where('end_date', '<', $now);
                    break;
                case 'upcoming':
                    $query->where('start_date', '>', $now);
                    break;
            }
        }

        $courses = $query->with(['teachers', 'category'])
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return CourseResource::collection($courses);
    }

    /**
     * GET /api/schedule
     * List courses active during the current week.
     */
    public function schedule(Request $request)
    {
        $user = $request->user();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        // Find courses that overlap with the current week
        $courses = $user->courses()
            ->where('is_visible', true)
            ->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->where('start_date', '<=', $endOfWeek)
                    ->where('end_date', '>=', $startOfWeek);
            })
            ->with(['teachers'])
            ->get();

        return response()->json([
            'week_start' => $startOfWeek->format('Y-m-d'),
            'week_end' => $endOfWeek->format('Y-m-d'),
            'courses' => CourseResource::collection($courses)
        ]);
    }
}
