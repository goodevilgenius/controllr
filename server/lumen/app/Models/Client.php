<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A Client
 */
abstract class Client extends Model
{
    use SoftDeletes;

    protected $fillable = ['slug'];

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
        return $this->hasMany(Command::class);
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
