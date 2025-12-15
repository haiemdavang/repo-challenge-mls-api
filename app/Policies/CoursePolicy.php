<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view the course.
     * Logic: Manager, or Teacher of this course, or Student enrolled in this course.
     */
    public function view(User $user, Course $course): bool
    {
        if ($user->isManager()) {
            return true;
        }

        // Check if user is enrolled in the course (either as Teacher or Student)
        return $course->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the course.
     * Logic: Manager, or Teacher of this specific course.
     */
    public function update(User $user, Course $course): bool
    {
        if ($user->isManager()) {
            return true;
        }

        // Check if user is a teacher of this course
        return $course->teachers()->where('users.id', $user->id)->exists();
    }
}
