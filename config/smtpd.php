<?php

return [

    /**
     * The address the smtp server should listen on.
     */
    'interface' => env('SMTPD_INTERFACE', '0.0.0.0'),

    /**
     * Define the port for the smtp server.
     */
    'port' => env('SMTPD_PORT', 25),

    /**
     * Define the servers hostname when responding to HELO / EHLO
     */
    'hostname' => env('SMTPD_HOSTNAME', 'localhost'),

    /**
     * Configuration for the authentication.
     */
    'auth' => [

        /**
         * The auth handler for the server.
         *
         * If empty, a guard handler will be used with the guard defined below.
         *
         * @see \Smtpd\Auth\Handler
         */
        'handler' => null,

        /**
         * Define the auth guard used to authenticate users over SMTP.
         *
         * Remember to configure the guard in your auth config.
         *
         * in auth.php:
         * 'guards' => [
         *     ...
         *     'smtp' => [
         *         'driver' => 'smtp',
         *         'username_field' => 'email',
         *         'provider' => 'users',
         *     ],
         * ],
         */
        'guard' => 'smtp',

        /**
         * Define the handler to authorize recipients.
         *
         * @see \Smtpd\Contracts\AuthorizesRecipients
         */
        'authorize_recipients' => \Smtpd\Auth\AuthorizeAllRecipients::class,
    ],

    /**
     * Provide additional context options for the smtp server.
     */
    'context_options' => [

        /**
         * Allow the server to run with a self signed certificate when using STARTTLS
         */
        'ssl' => [
            'verify_peer' => false,
            'allow_self_signed' => false,
            'local_cert' => env('SMTPD_CERT_PATH', null),
        ],
    ]
];
