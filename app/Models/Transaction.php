<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Transaction',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', description: 'ID of the transaction'),
        new OA\Property(property: 'type', type: 'string', enum: ['income', 'expense'], description: 'Type of the transaction (income or expense)'),
        new OA\Property(property: 'amount', type: 'number', format: 'float', description: 'Amount of the transaction'),
        new OA\Property(property: 'description', type: 'string', description: 'Description of the transaction'),
        new OA\Property(property: 'date', type: 'string', format: 'date', description: 'Date of the transaction'),
        new OA\Property(property: 'category_id', type: 'integer', description: 'ID of the category to which the transaction belongs'),
    ],
    required: ['id', 'type', 'amount', 'date']
)]
class Transaction extends Model
{
    use HasFactory;
}
