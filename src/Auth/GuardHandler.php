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
        if ($this->guard->validate($credentials)) {
            return $this->guard->user();
        }
        return null;
    }
}
