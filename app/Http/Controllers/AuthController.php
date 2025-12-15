<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\BaseUserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = User::with('role')->where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new UnauthorizedException();
        }

        if (!$user->is_active) {
            throw new ForbiddenException("USER_INACTIVE");
        }
        $accessExpiry = now()->addMinutes(30);
        $accessToken = $user->createToken('access_token', ['access-api'], $accessExpiry)->plainTextToken;
        $refreshExpiry = now()->addDays(7);
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], $refreshExpiry)->plainTextToken;
        $cookieRefresh = cookie('refresh_token', $refreshToken, 60 * 24 * 7, null, null, false, true);

        return response()->json([
            'accessToken' => $accessToken,
            'expiresIn' => 1800,
            'user' => new BaseUserResource($user)
        ], 200)->withCookie($cookieRefresh);
    }

    public function refreshToken(Request $request)
    {
        $refreshTokenString = $request->cookie('refresh_token');

        if (!$refreshTokenString) {
            throw new UnauthorizedException();
        }

        $token = PersonalAccessToken::findToken($refreshTokenString);

        if (
            !$token ||
            !$token->can('issue-access-token') ||
            ($token->expires_at && $token->expires_at->isPast())
        ) {
            return response()->json(['message' => 'TOKEN_INVALID_OR_EXPIRED'], 401)
                ->withCookie(Cookie::forget('refresh_token'));
        }

        $user = $token->tokenable;
        $token->delete();

        $newRefreshExpiry = now()->addDays(7);
        $newRefreshToken = $user->createToken('refresh_token', ['issue-access-token'], $newRefreshExpiry)->plainTextToken;

        $expirationAccess = now()->addMinutes(30);
        $newAccessToken = $user->createToken('access_token', ['access-api'], $expirationAccess)->plainTextToken;

        $cookieRefresh = cookie('refresh_token', $newRefreshToken, 60 * 24 * 7, null, null, false, true);

        return response()->json([
            'accessToken' => $newAccessToken,
            'expiresIn' => 1800
        ])->withCookie($cookieRefresh);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('role');

        return response()->json([
            'user' => new BaseUserResource($user)
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $cookieRefresh = Cookie::forget('refresh_token');

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ])->withCookie($cookieRefresh);
    }
}
