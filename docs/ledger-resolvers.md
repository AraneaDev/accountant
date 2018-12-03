# Ledger Resolvers
A resolver is a class implementing one of the following contracts:
- `Altek\Accountant\Contracts\IpAddressResolver`
- `Altek\Accountant\Contracts\UrlResolver`
- `Altek\Accountant\Contracts\UserAgentResolver`
- `Altek\Accountant\Contracts\UserResolver`

Each resolver must have a **public static** `resolve()` method with the appropriate logic.

## IP Address Resolver
The default `IpAddressResolver` implementation uses `Request::ip()` to get client IP addresses.

While that works for most applications, the ones running behind a proxy or a [load balancer](https://en.wikipedia.org/wiki/Load_balancing_(computing)) should get IP addresses differently.

Usually, the real IP address will be passed in a **X-Forwarded-For** HTTP header.

Here's a resolver example for this use case.

```php
<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\Request;

class IpAddressResolver implements \Altek\Accountant\Contracts\IpAddressResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(): string
    {
        return Request::header('HTTP_X_FORWARDED_FOR', '0.0.0.0');
    }
}
```

Set the custom _IP Address_ resolver in the `config/accountant.php` configuration file:

```php
return [

    'ledger' => [
        // ...

        'resolvers' = [
            // ...
            'ip_address' => App\Resolvers\IpAddressResolver::class,
            // ...
        ],
    ],

    // ...
];
```

## URL Resolver
The default resolver uses the `Request::fullUrl()` method to get the current URL (including any query strings).

Here's a resolver example where query strings are not included.

```php
<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class UrlResolver implements \Altek\Accountant\Contracts\UrlResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(): string
    {
        if (App::runningInConsole()) {
            return 'Command Line Interface';
        }

        // Just the URL without query strings
        return Request::url();
    }
}
```

Set the custom _URL_ resolver in the `config/accountant.php` configuration file:

```php
return [

    'ledger' => [
        // ...

        'resolvers' = [
            // ...
            'url' => App\Resolvers\UrlResolver::class,
        ],
    ],

    // ...
];
```

## User Agent Resolver
This resolver uses the `Request::header()` method without a default value, which returns `null` if a User Agent isn't available.

The following example will return a default string when the `User-Agent` HTTP header is empty/unavailable.

```php
<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\Request;

class UserAgentResolver implements \Altek\Accountant\Contracts\UserAgentResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(): ?string
    {
        // Default to "N/A" if the User Agent isn't available
        return Request::header('User-Agent', 'N/A');
    }
}
```

Set the custom _User Agent_ resolver in the `config/accountant.php` configuration file:

```php
return [

    'ledger' => [
        // ...

        'resolvers' = [
            // ...
            'user_agent' => App\Resolvers\UserAgentResolver::class,
            // ...
        ],
    ],

    // ...
];
```

## User Resolver
The included `UserResolver` implementation uses the Laravel `Auth::guard()` method, by default.

The `resolve()` method must either return an `Altek\Accountant\Contracts\Identifiable` instance, or `null` when the user can't be resolved.

### Identifiable interface implementation
Implementing the `Altek\Accountant\Contracts\Identifiable` interface on a `User` model:

```php
<?php

namespace App\Models;

use Altek\Accountant\Contracts\Identifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Identifiable
{
    // ...

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getKey();
    }

    // ...
}
```

### Other authentication systems
When using different auth mechanisms like [Sentinel](https://github.com/cartalyst/sentinel), make sure to implement a corresponding resolver.

```php
<?php

namespace App\Resolvers;

use Altek\Accountant\Contracts\Identifiable;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class UserResolver implements \Altek\Accountant\Contracts\UserResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(): ?Identifiable
    {
        return Sentinel::check() ? Sentinel::getUser() : null;
    }
}
```

Set the custom _User_ resolver in the `config/accountant.php` configuration file:

```php
return [

    'ledger' => [
        // ...

        'resolvers' = [
            'user' => App\Resolvers\UserResolver::class,
            // ...
        ],
    ],

    // ...
];
```
