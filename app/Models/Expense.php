<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Expense',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', description: 'ID of the expense'),
        new OA\Property(property: 'amount', type: 'number', format: 'float', description: 'Amount of the expense'),
        new OA\Property(property: 'description', type: 'string', description: 'Description of the expense'),
        new OA\Property(property: 'date', type: 'string', format: 'date', description: 'Date of the expense'),
        new OA\Property(property: 'category_id', type: 'integer', description: 'ID of the category to which the expense belongs')
    ]
)]
class Expense extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'date',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function group(): MorphOne
    {
        return $this->MorphOne(Group::class, 'groupable');
    }
}
