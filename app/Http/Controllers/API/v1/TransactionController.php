<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\ApiController;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Transactions', description: 'Endpoints for managing transactions')]
class TransactionController extends ApiController
{
    #[OA\Get(
        path: '/transactions',
        summary: 'List all transactions',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                in: 'query',
                description: 'Type of transaction (income/expense)',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['income', 'expense'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Transaction'))
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid transaction type'
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');

        // Validate transaction type
        if (! in_array($type, ['income', 'expense'])) {
            return $this->failure('Invalid transaction type', 400);
        }

        // Fetch transactions based on type
        $transactions = ($type === 'income')
            ? Income::all()
            : Expense::paginate(20);

        return $this->success($transactions);
    }

    #[OA\Post(
        path: '/transactions',
        summary: 'Create a new transaction',
        tags: ['Transactions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['type', 'date', 'party_id', 'wallet_id', 'amount', 'group_id'],
                properties: [
                    new OA\Property(property: 'type', type: 'string', enum: ['income', 'expense']),
                    new OA\Property(property: 'date', type: 'string', format: 'date'),
                    new OA\Property(property: 'party_id', type: 'integer'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'wallet_id', type: 'integer'),
                    new OA\Property(property: 'amount', type: 'number', format: 'float'),
                    new OA\Property(property: 'group_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Transaction created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Transaction')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:income,expense',
            'date' => 'required|date',
            'party_id' => 'required|integer|exists:parties,id',
            'description' => 'nullable|string',
            'wallet_id' => 'required|integer|exists:wallets,id',
            'amount' => 'required|numeric',
            'group_id' => 'required|integer|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return $this->failure($validator->errors(), 400);
        }

        $data = $validator->validated();
        $transaction = ($data['type'] === 'income')
            ? Income::create($data)
            : Expense::create($data);

        return $this->success($transaction, 201);
    }

    #[OA\Get(
        path: '/transactions/{id}',
        summary: 'Get a specific transaction',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of the transaction',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'type',
                in: 'query',
                description: 'Type of transaction (income/expense)',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['income', 'expense'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(ref: '#/components/schemas/Transaction')
            ),
            new OA\Response(
                response: 404,
                description: 'Transaction not found'
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid transaction type'
            ),
        ]
    )]
    public function show($id, Request $request): JsonResponse
    {
        $type = $request->query('type');

        if (! in_array($type, ['income', 'expense'])) {
            return $this->failure('Invalid transaction type', 400);
        }

        $transaction = ($type === 'income')
            ? Income::find($id)
            : Expense::find($id);

        if (! $transaction) {
            return $this->failure('Transaction not found', 404);
        }

        return $this->success($transaction);
    }

    #[OA\Put(
        path: '/transactions/{id}',
        summary: 'Update a specific transaction',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of the transaction',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'type',
                in: 'query',
                description: 'Type of transaction (income/expense)',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['income', 'expense'])
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['date', 'party_id', 'wallet_id', 'amount', 'group_id'],
                properties: [
                    new OA\Property(property: 'date', type: 'string', format: 'date'),
                    new OA\Property(property: 'party_id', type: 'integer'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'wallet_id', type: 'integer'),
                    new OA\Property(property: 'amount', type: 'number', format: 'float'),
                    new OA\Property(property: 'group_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transaction updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Transaction')
            ),
            new OA\Response(
                response: 404,
                description: 'Transaction not found'
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
        ]
    )]
    public function update(Request $request, $id): JsonResponse
    {
        $type = $request->query('type');

        if (! in_array($type, ['income', 'expense'])) {
            return $this->failure('Invalid transaction type', 400);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'party_id' => 'required|integer|exists:parties,id',
            'description' => 'nullable|string',
            'wallet_id' => 'required|integer|exists:wallets,id',
            'amount' => 'required|numeric',
            'group_id' => 'required|integer|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return $this->failure($validator->errors(), 400);
        }

        $data = $validator->validated();
        $transaction = ($type === 'income')
            ? Income::find($id)
            : Expense::find($id);

        if (! $transaction) {
            return $this->failure('Transaction not found', 404);
        }

        $transaction->update($data);

        return $this->success($transaction);
    }

    #[OA\Delete(
        path: '/transactions/{id}',
        summary: 'Delete a specific transaction',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of the transaction',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'type',
                in: 'query',
                description: 'Type of transaction (income/expense)',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['income', 'expense'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transaction deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Transaction not found'
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid transaction type'
            ),
        ]
    )]
    public function destroy($id, Request $request): JsonResponse
    {
        $type = $request->query('type');

        if (! in_array($type, ['income', 'expense'])) {
            return $this->failure('Invalid transaction type', 400);
        }

        $transaction = ($type === 'income')
            ? Income::find($id)
            : Expense::find($id);

        if (! $transaction) {
            return $this->failure('Transaction not found', 404);
        }

        $transaction->delete();

        return $this->success(['message' => 'Transaction deleted successfully']);
    }
}
