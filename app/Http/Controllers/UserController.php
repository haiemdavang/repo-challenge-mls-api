<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load('role');

        return new UserResource($user);
    }


    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();
        $user->update($validated);

        $user->load('role');

        return new UserResource($user);
    }
}
