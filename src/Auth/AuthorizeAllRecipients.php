<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:54
 */

namespace Smtpd\Auth;


use Illuminate\Contracts\Auth\Authenticatable;
use Smtpd\Contracts\AuthorizesRecipients;

class AuthorizeAllRecipients implements AuthorizesRecipients
{
    /**
     * Authorize all recipients.
     *
     * @param Authenticatable|null $user
     * @param string               $recipient
     *
     * @return bool
     */
    public function authorize(?Authenticatable $user, string $recipient): bool
    {
        return true;
    }
}
