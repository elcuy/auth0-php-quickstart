<?php
namespace App\Repositories;

use App\User;

use Auth0\Login\Auth0User;
use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Repository\Auth0UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomUserRepository extends Auth0UserRepository
{
    /**
     * Get an existing user or create a new one
     *
     * @param array $profile - Auth0 profile
     *
     * @return User
     */
    protected function upsertUser($profile)
    {
        // Not a big deal, but we can suggest using destructuring (PHP7+).
        // This makes it more scalable in the long run, if we want to
        // add more properties to $profile
        ["sub" => $sub, "email" => $email, "name" => $name] = $profile;

        return User::firstOrCreate(
            ['sub' => $sub],
            [
                'email' => $email ?? '',
                'name' => $name ?? '',
            ]
        );
    }

    /**
     * Authenticate a user with a decoded ID Token
     *
     * @param object $jwt
     *
     * @return Auth0JWTUser
     */
    public function getUserByDecodedJWT(array $decodedJwt): Authenticatable
    {
        // No error handling here, we might want to handle exceptions
        // properly in case this operation fails
        $user = $this->upsertUser($decodedJwt);
        return new Auth0JWTUser($user->getAttributes());
    }

    /**
     * Get a User from the database using Auth0 profile information
     *
     * @param array $userinfo
     *
     * @return Auth0User
     */
    public function getUserByUserInfo(array $userinfo): Authenticatable
    {
        // No error handling here, we might want to handle exceptions
        // properly in case this operation fails
        $user = $this->upsertUser($userinfo['profile']);
        return new Auth0User($user->getAttributes(), $userinfo['accessToken']);
    }
}
