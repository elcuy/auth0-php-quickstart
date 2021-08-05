<?php

namespace App\Http\Controllers\Auth;

use App;
use Auth;
use Redirect;
use App\Http\Controllers\Controller;

class Auth0IndexController extends Controller
{
    /**
     * Redirect to the Auth0 hosted login page
     *
     * @return mixed
     */
    public function login()
    {
        if (Auth::check()) {
            // Here we're using the redirect helper, and below we're calling
            // the static method on the Redirect class directly. Either is
            // fine, but it'd be great to have consistency across the whole
            // sample code
            return redirect()->intended('/');
        }

        return App::make('auth0')->login(
            null,
            null,
            ['scope' => 'openid name email email_verified'],
            'code'
        );
    }

    /**
     * Log out of our app
     *
     * @return mixed
     */
    public function logout()
    {
        Auth::logout();

        // This can be abstracted into the library or somewhere else
        // through a function. Seems like it's standard and takes from
        // the config values, so a function should be provided instead
        // of building the URL manually. As a developer I would
        // appreciate not having to do this in my code, but being able
        // to customize it if needed.
        $logoutUrl = sprintf(
            'https://%s/v2/logout?client_id=%s&returnTo=%s',
            config('laravel-auth0.domain'),
            config('laravel-auth0.client_id'),
            config('app.url')
        );

        return Redirect::intended($logoutUrl);
    }

    /**
     * Display the user's Auth0 data
     *
     * @return mixed
     */
    public function profile()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('profile')->with(
            'user',
            print_r(Auth::user()->getUserInfo(), true)
        );
    }
}
