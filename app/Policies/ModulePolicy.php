<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    /**
     * Determine whether the user can view the module.
     * Logic: Student (or Teacher/Manager) can view if they are enrolled in the Course containing this Module.
     */
    public function view(User $user, Module $module): bool
    {
        if ($user->isManager()) {
            return true;
        }

        // Get the course this module belongs to
        $course = $module->course;

        if (!$course) {
            return false;
        }

        // Check if user is enrolled in the course
        return $course->users()->where('users.id', $user->id)->exists();
    }
}
