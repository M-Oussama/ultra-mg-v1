<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ClientController extends Controller
{
    //

    /**
     * get List Of All Users
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/clients/list",
        operationId: "getClients",
        description: "Returns the list of clients",
        tags: ["clients"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IClient",
        type: 'object'
    )])]
    public function getClients(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $clients = Client::when($searchValue, function ($queryBuilder) use ($searchValue) {
            // Search for users with matching name or email
            $queryBuilder->where('name', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('surname', 'LIKE', '%' . $searchValue . '%');
        })->paginate($perPage, ['*'], 'page', $currentPage);
        $totalUsers = $clients->total(); // Total number of users matching the query
        $totalPage = ceil($totalUsers / $perPage); // Calculate total pages

        return response()->json(["clients" => $clients, "totalPage" => $totalPage, "totalClients"=>$totalUsers]);
    }

    /**
     * create a new user
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Post(
        path: "/api/clients/store",
        operationId: "createClient",
        description: "Create a new Client",
        tags: ["clients"],
    )]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        required: ["name", "surname", "email", "address", "phone", "NRC", "NIS", "NART", "NIF"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "surname", type: "string", example: "john"),
            new OA\Property(property: "phone", type: "string", example: "0665461326"),
            new OA\Property(property: "address", type: "string", example: "address"),
            new OA\Property(property: "NRC", type: "string", example: "NRC"),
            new OA\Property(property: "NIS", type: "string", example: "NIS"),
            new OA\Property(property: "NART", type: "string", example: "NART"),
            new OA\Property(property: "NIF", type: "string", example: "NIF"),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        ]
    )])]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IClient",
        type: 'object'
    )])]
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'surname' => 'string|max:255',
            'phone' => 'string|max:255',
            'address' => 'string|max:255',
            'NRC' => 'string|max:255',
            'NIF' => 'string|max:255',
            'NART' => 'string|max:255',
            'NIS' => 'string|max:255',
            'email' => 'email|unique:users,email',
        ]);

        // Create a new user record in the database using User::create()
        $client = Client::create($validatedData);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Client created successfully', 'client' => $client]);

    }

    #[OA\Post(
        path: "/api/clients/update/{id}",
        operationId: "updateClient",
        description: "update Client",
        tags: ["clients"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'number'))]

    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        required: ["name", "surname", "email", "address", "phone", "NRC", "NIS", "NART", "NIF"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "surname", type: "string", example: "john"),
            new OA\Property(property: "phone", type: "string", example: "0665461326"),
            new OA\Property(property: "address", type: "string", example: "address"),
            new OA\Property(property: "NRC", type: "string", example: "NRC"),
            new OA\Property(property: "NIS", type: "string", example: "NIS"),
            new OA\Property(property: "NART", type: "string", example: "NART"),
            new OA\Property(property: "NIF", type: "string", example: "NIF"),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        ]
    )])]

    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IClient",
        type: 'object'
    )])]

    public function update(int $id,Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'surname' => 'string|max:255',
            'phone' => 'string|max:255',
            'address' => 'string|max:255',
            'NRC' => 'string|max:255',
            'NIF' => 'string|max:255',
            'NART' => 'string|max:255',
            'NIS' => 'string|max:255',
            'email' => 'email|unique:users,email',
        ]);

        $client = Client::find($id);


        $client->update($validatedData);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Client updated successfully', 'client' => $client]);

    }

    /**
     * Delete a client
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/clients/delete/{id}",
        operationId: "deleteClient",
        description: "delete a client",
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
        $client = Client::find($id);
        $client->delete();
        return response()->json(["message" => "User deleted successfully"]);
    }
}
