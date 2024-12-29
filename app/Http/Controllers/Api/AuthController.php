<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

/**
 * @group Authentication
 * 
 * APIs for user registration and authentication
 */
class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register User
     * 
     * Create a new user account and return an access token.
     * 
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $token = $this->userService->register($validator->validated());
        return $this->respondWithToken($token);
    }

    /**
     * Login
     * 
     * Authenticate user and return an access token.
     * 
     * @unauthenticated
     *
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $validator->validated();
        $token = $this->userService->login($credentials);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Format Token Response
     * 
     * @hideFromAPIDocumentation
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}