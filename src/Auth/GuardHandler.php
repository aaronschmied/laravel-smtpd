<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:06
 */

namespace Smtpd\Auth;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class GuardHandler extends Handler
{
    /**
     * The guard used to authenticate the user.
     *
     * @var Guard
     */
    protected $guard;

    /**
     * EloquentUserHandler constructor.
     *
     * @param $guard string|null
     */
    public function __construct(?string $guard)
    {
        $this->guard = Auth::guard($guard);
    }

    /**
     * Attempt to authenticate a user when logging in via SMTP.
     *
     * @param array $credentials
     *
     * @return Authenticatable|null
     */
    public function attempt(array $credentials): ?Authenticatable
    {
        /**
         * Clone the guard so the authenticated user is not carried on to the next connection.
         * It's a bit of a hacky solution but required since the guards are designed for a single request lifetime.
         */
        $guard = clone $this->guard;

        if ($guard->validate($credentials)) {
            return $guard->user();
        }
        return null;
    }
}
