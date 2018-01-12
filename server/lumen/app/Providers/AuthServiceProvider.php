<?php namespace App\Providers;

use App\Models\Client;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->hasHeader('Authorization')) {
                $auth = $request->header('Authorization');
                $regex = '/^Bearer /';
                if (preg_match($regex, $auth)) {
                    $bearer = preg_replace($regex, '', $auth);
                    $parts = explode('|', base64_decode($bearer), 3);
                    if (count($parts) < 2) return null;

                    $body = $request->getContent();

                    if ($body && count($parts) < 3) return null;
                    if ($body && $parts[2] != $body) return null;

                    return Client::where([
                        'id' => $parts[0],
                        'secret' => $parts[1],
                    ])->first();
                }
            }

            return null;
        });
    }
}
