<?php

namespace Smtpd\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

abstract class Handler
{
    /**
     * Decode the credentials from the client.
     *
     * @param array $credentials
     *
     * @return array
     */
    public function decodeCredentials(array $credentials): array
    {
        return [
            'user'     => base64_decode(Arr::get($credentials, 'user')),
            'password' => base64_decode(Arr::get($credentials, 'password')),
        ];
    }

    /**
     * Attempt to authenticate a user when logging in via SMTP.
     *
     * @param array $credentials
     *
     * @return Authenticatable|null
     */
    abstract public function attempt(array $credentials): ?Authenticatable;
}
