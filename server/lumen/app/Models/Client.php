<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable; // @todo replace. These are no good for me
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A Client
 */
class Client extends Model implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes, Authenticatable, Authorizable;

    protected $fillable = ['slug', 'kind'];
    protected $hidden = ['secret'];

    /**
     * {@inheritdoc}
     */
    public function save(array $options = []): bool
    {
        if (empty($this->secret)) {
            $this->secret = str_random(32);
        }

        return parent::save($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthIdentifierName()
    {
        return 'slug';
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthPassword()
    {
        return $this->secret;
    }

    /**
     * Scope to get specify kind.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeKind(Builder $query, string $kind): Builder
    {
        return $query->where('kind', $kind);
    }

    /**
     * Scope to get senders.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeSenders(Builder $query): Builder
    {
        return $this->scopeKind($query, 'sender');
    }

    /**
     * Scope to get receivers.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeReceivers(Builder $query): Builder
    {
        return $this->scopeKind($query, 'receiver');
    }

    /**
     * Sets slug.
     *
     * @param string $value
     */
    public function setSlugAttribute(string $value): void
    {
        $this->attributes['slug'] = str_slug($value);
    }

    /**
     * @return HasMany
     */
    public function commands(): HasMany
    {
        return $this->hasMany(Command::class, $this->kind . '_id', 'id');
    }

    /**
     * Regenerate the client secret.
     *
     * @return string
     */
    public function newSecret(): string
    {
        $secret = str_random(32);
        $this->secret = $secret;
        $this->save();

        return $secret;
    }
}
