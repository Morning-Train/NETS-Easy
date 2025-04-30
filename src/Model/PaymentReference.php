<?php

namespace Morningtrain\NETSEasy\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $payment_id
 * @property string $status
 * @property array $webhook_ids
 */
class PaymentReference extends Model
{
    protected $table = 'nets_easy_payment_references';

    protected $fillable = [
        'payment_id',
        'status',
        'webhook_ids',
    ];

    protected $casts = [
        'webhook_ids' => 'array',
    ];
}
