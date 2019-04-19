<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:51
 */

namespace Smtpd\Contracts;


use Illuminate\Contracts\Auth\Authenticatable;

interface AuthorizesRecipients
{
    /**
     * Authorize a user to add a given recipient.
     *
     * @param Authenticatable|null $user
     * @param string               $recipient
     *
     * @return bool
     */
    public function authorize(?Authenticatable $user, string $recipient): bool;
}
