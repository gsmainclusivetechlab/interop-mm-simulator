<?php

namespace App\Models;

use App\Traits\ParseTraceId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class Transaction
 *
 * @package App\Models
 * @property int $trace_id
 * @property string $callback_url
 * @property string $type
 * @property array $debitParty
 * @property array $creditParty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCallbackUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCreditParty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereDebitParty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereTraceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use ParseTraceId;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'trace_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trace_id',
        'callback_url',
        'type',
        'debitParty',
        'creditParty',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'debitParty' => 'array',
        'creditParty' => 'array',
    ];

    /**
     * Get current transaction
     *
     * @return Transaction|null
     */
    public static function getCurrent(): ?Transaction
    {
        return self::find(self::parseTraceId(resolve(Request::class)->header('traceparent')));
    }
}
