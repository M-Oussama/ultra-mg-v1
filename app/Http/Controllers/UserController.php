<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * get List Of All Users
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function getUsers(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $users = User::paginate($perPage, ['*'], 'page', $currentPage);

        $totalUsers = $users->total(); // Total number of users matching the query
        $totalPage = ceil($totalUsers / $perPage); // Calculate total pages

        return response()->json(["users" => $users, "totalPage" => $totalPage, "totalUsers"=>$totalUsers]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'user.name' => 'string|max:255',
            'user.email' => 'email|unique:users,email',
            'user.password' => 'string',
        ]);

        // Create a new user record in the database using User::create()
        $user = User::create($validatedData['user']);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'User created successfully', 'user' => $user]);

    }
}
