<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

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

    #[OA\Get(
        path: "/api/users/list",
        operationId: "getUsers",
        description: "Returns the list of users",
        tags: ["users"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IUser",
        type: 'object'
    )])]
    public function getUsers(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value

        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided

        $users = User::when($searchValue, function ($queryBuilder) use ($searchValue) {
            // Search for users with matching name or email
            $queryBuilder->where('name', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('email', 'LIKE', '%' . $searchValue . '%');
        })->paginate($perPage, ['*'], 'page', $currentPage);
        $totalUsers = $users->total(); // Total number of users matching the query
        $totalPage = ceil($totalUsers / $perPage); // Calculate total pages
        $roles = Role::all();

        return response()->json(["users" => $users, "totalPage" => $totalPage, "totalUsers"=>$totalUsers, "roles" => $roles]);
    }

    /**
     * create a new user
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Post(
        path: "/api/users/store",
        operationId: "createUser",
        description: "Create a new user",
        tags: ["users"],
    )]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        required: ["name", "email", "password"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
            new OA\Property(property: "password", type: "string", example: "password"),
        ]
    )])]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IUser",
        type: 'object'
    )])]
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email',
            'password' => 'string',
            'role' => 'integer'
        ]);

        // Create a new user record in the database using User::create()
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role_id' => $validatedData['role']
        ]);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'User created successfully', 'user' => $user]);

    }

    #[OA\Post(
        path: "/api/users/update/{id}",
        operationId: "updateUser",
        description: "Updates the user",
        tags: ["users"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'number'))]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
            new OA\Property(property: "password", type: "string", example: "password"),
        ]
    )])]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IUser",
        type: 'object'
    )])]

    public function update(int $id,Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email',
            'role_id' => 'integer'
        ]);

        // Check if the passwordChanged field is present in the request data,and it is true the user want to change password
        if(isset($validatedData['passwordChanged']) && $validatedData['passwordChanged']){
            // Check if the password field is present in the request data
            if(isset($validatedData['password'])) {
                // Hash the password before storing it in the database
                $validatedData['password'] = bcrypt($validatedData['password']);
            }
        }


        $user = User::find($id);

        // Create a new user record in the database using User::create()
        $user->update($validatedData);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);

    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/users/delete/{id}",
        operationId: "deleteUser",
        description: "delete a user",
        tags: ["users"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: "Successfully deleted",
        content: new OA\JsonContent(type: "boolean", example: true)
    )]
    public function delete(int $id): JsonResponse
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(["message" => "User deleted successfully"]);
    }
}
