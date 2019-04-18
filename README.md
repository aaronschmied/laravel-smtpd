# Laravel SMTPD <small>`aaronschmied/laravel-smtpd`</small>

A basic SMTP server for recieving emails using [Laravel](https://laravel.com/).

**THIS PACKAGE IS STILL IN DEVELOPMENT, USE AT YOUR OWN RISK**

## Notice

This package includes modified source code from [Christian Mayer](https://fox21.at/). Specifically the packages [thefox/network](https://github.com/TheFox/network) and [thefox/smtpd](https://github.com/TheFox/smtpd). Both are released under the GNU General Public License version 3.


## Usage

Install the package using composer with the following command:

```
composer require aaronschmied/laravel-smtpd
```

If you're running Laravel 5.5 or later, the service provider is automatically discovered. Otherwise add the following provider to your apps providers in `app.php`:

```php
  'providers' => [
    ...
    
    \Smtpd\Providers\SmtpdServiceProvider::class,
  ],
```

Publish the config file using the command:

```
php artisan vendor:publish --provider="Smtpd\Providers\SmtpdServiceProvider"
```

This creates the `smtpd.php` file in your config directory.

### Configuration

#### `interface` & `port`
By default the server listens on any interface (`0.0.0.0`) on port `25`.

#### `hostname`

Define the hostname for the server. In production mode this should be the FQDN of your server.

#### `auth`

Contains the configuration options for the authentication.

##### `handler`

The handler class checks if the username / password combination submitted from the client is valid.

You can provide your own handler here by extending the base handler class `Smtpd\Auth\Handler`.

If left blank, a guard handler is created using the configured guard:


##### `guard`

*The guard is only used if the handler is left blank.*

The package comes with an integrated smtp guard driver. To use this driver, edit your `auth.php` config file and add the guard config as follows:

```php
'guards' => [
    ...
    'smtp' => [
        'driver' => 'smtp',
        'username_field' => 'email',
        'provider' => 'users',
    ],
],
```

The provider can be any user provider defined in your config. In this example, the default eloquent user provider is configured, with the email field as the smtp username.

##### `authorize_recipients`

This handler checks if a user can send a message to a given recipient.

By default, all recipients are allowed using the `Smtpd\Auth\AuthorizeAllRecipients::class` class.

If you only want authenticated users to be able to send messages, create a new handler implementing the `Smtpd\Contracts\AuthorizesRecipients` interface.


### `context_options`

Here you can define additional options for the socket.

By default the socket is configured with the option to provide a self signed certificate for connections using tls encryption with STARTTLS.

You can find more information about the options [here](https://php.net/manual/de/function.stream-context-create.php).

### Recieving a message

To recieve a message and handle it from there, create an event listener for the `Smtpd\Events\MessageRecieved` event.

The event contains the user (if authenticated) as well as the `Smtpd\Message` object.

### Starting the listener

To start listening for incoming connections, run the following command:

```
php artisan smtpd:listen
```

From here you can save the message to your database, send it to another server using the laravel mailer or just ignore it... ¯\\\_(ツ)\_/¯

## License

GPL-3.0

Copyright &copy; 2019 Aaron Schmied [schmied.dev](https://schmied.dev)

See [LICENSE](LICENSE)

**Contains sources of the following packages:**

[thefox/network](https://github.com/TheFox/network#license)
&rarr;
Copyright &copy; 2017 Christian Mayer [fox21.at](https://fox21.at/)

[thefox/smtpd](https://github.com/TheFox/smtpd#license)
&rarr;
Copyright &copy; 2014 Christian Mayer [fox21.at](https://fox21.at/)



