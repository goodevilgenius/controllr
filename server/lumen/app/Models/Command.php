<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A Receiver
 */
class Command extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'command',
        'arguments',
        'data',
        'status',
        'eta',
        'return_code',
        'output',
    ];

    /**
     * The Receiver.
     *
     * @return HasOne
     */
    public function receiver(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'receiver_id');
    }

    /**
     * The Sender.
     *
     * @return HasOne
     */
    public function sender(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'sender_id');
    }
}
