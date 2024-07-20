<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\ApiController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(name="Groups", description="Endpoints for managing groups")
 */
class GroupController extends ApiController
{
    #[OA\Get(
        path: '/groups',
        summary: 'List all groups',
        tags: ['Groups'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Group')
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error'
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $groups = Group::paginate(20);
        return $this->success($groups);
    }

    #[OA\Post(
        path: '/groups',
        summary: 'Create a new group',
        tags: ['Groups'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        description: 'Name of the group'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Group created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Group')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error'
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->failure('Validation error', 400, $validator->errors()->all());
        }

        $group = Group::create($validator->validated());
        return $this->success($group, 'Group created successfully', 201);
    }

    #[OA\Get(
        path: '/groups/{id}',
        summary: 'Get a specific group',
        tags: ['Groups'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'ID of the group'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(ref: '#/components/schemas/Group')
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $group = Group::find($id);

        if (!$group) {
            return $this->failure('Group not found', 404);
        }

        return $this->success($group);
    }

    #[OA\Put(
        path: '/groups/{id}',
        summary: 'Update a specific group',
        tags: ['Groups'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'ID of the group'
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        description: 'Name of the group'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Group updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Group')
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error'
            )
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->failure('Validation error', 400, $validator->errors()->all());
        }

        $group = Group::find($id);

        if (!$group) {
            return $this->failure('Group not found', 404);
        }

        $group->update($validator->validated());
        return $this->success($group, 'Group updated successfully');
    }

    #[OA\Delete(
        path: '/groups/{id}',
        summary: 'Delete a specific group',
        tags: ['Groups'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'ID of the group'
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Group deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error'
            )
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $group = Group::find($id);

        if (!$group) {
            return $this->failure('Group not found', 404);
        }

        $group->delete();
        return $this->success(null, 'Group deleted successfully', 204);
    }
}
