<?php

namespace App\Models;

use App\Traits\ParseTraceId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class Transaction
 *
 * @package App\Models
 * @property int $trace_id
 * @property string $callback_url
 * @property string $type
 * @property string|null $subType
 * @property string|null $descriptionText
 * @property mixed|null $requestDate
 * @property string|null $requestingOrganisationTransactionReference
 * @property string|null $geoCode
 * @property array $debitParty
 * @property array $creditParty
 * @property array|null $senderKyc
 * @property mixed|null $recipientKyc
 * @property string|null $originalTransactionReference
 * @property string|null $servicingIdentity
 * @property string $transactionStatus
 * @property string|null $transactionReceipt
 * @property array $metadata
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereDescriptionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereGeoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereOriginalTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereRecipientKyc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereRequestDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereRequestingOrganisationTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereSenderKyc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereServicingIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereSubType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereTransactionReceipt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereTransactionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereMatadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereTransactionId($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use ParseTraceId;

    const STATUSES = ['Pending', 'Completed', 'Failed'];

    const TYPES = ['DEPOSIT', 'WITHDRAWAL', 'TRANSFER', 'PAYMENT', 'REFUND'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'trace_id';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trace_id',
        'transaction_id',
        'callback_url',
        'amount',
        'currency',
        'type',
        'subType',
        'descriptionText',
        'requestDate',
        'requestingOrganisationTransactionReference',
        'geoCode',
        'debitParty',
        'creditParty',
        'senderKyc',
        'recipientKyc',
        'originalTransactionReference',
        'servicingIdentity',
        'transactionStatus',
        'transactionReceipt',
        'metadata',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'debitParty' => 'array',
        'creditParty' => 'array',
        'senderKyc' => 'array',
        'recipientKyc' => 'array',
        'metadata' => 'array',
        'requestDate' => 'datetime:Y-m-dTH:i:s.vZ',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function setRequestDateAttribute(string $value)
    {
        $this->attributes['requestDate'] = new Carbon($value);
    }

    /**
     * Get current transaction
     *
     * @return Transaction|null
     */
    public static function getCurrent(): ?Transaction
    {
        $transaction = self::find(
            self::parseTraceId(resolve(Request::class)->header('traceparent'))
        );

        if (!$transaction) {
            throw new BadRequestHttpException();
        }

        return $transaction;
    }
}
