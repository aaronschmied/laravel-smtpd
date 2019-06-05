<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:14
 */

namespace Smtpd\Auth;


use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class SmtpGuard implements Guard
{
    use GuardHelpers;

    /**
     * The users credentials
     *
     * @var array
     */
    private $credentials;

    /**
     * The username field
     * @var string
     */
    private $usernameField;

    /**
     * SmtpGuard constructor.
     *
     * @param $provider      UserProvider
     * @param $usernameField string
     */
    public function __construct(UserProvider $provider, ?string $usernameField)
    {
        $this->setProvider($provider);
        $this->usernameField = $usernameField ?? 'username';
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $credentials[$this->usernameField] = Arr::pull($credentials, 'user');

        $user = $this
            ->getProvider()
            ->retrieveByCredentials($credentials);

        if (!is_null($user) and Hash::check($credentials['password'], $user->getAuthPassword())) {
            $this->setUser($user);
        }

        return $this->check();
    }
}
