# Resolvers
Currently, there are five resolver types available.

Name       | Interface                         
-----------|-----------------------------------------------
Context    | `Altek\Accountant\Contracts\ContextResolver`
IP Address | `Altek\Accountant\Contracts\IpAddressResolver`
URL        | `Altek\Accountant\Contracts\UrlResolver`
User Agent | `Altek\Accountant\Contracts\UserAgentResolver`
User       | `Altek\Accountant\Contracts\UserResolver`

Each resolver has a **public static** `resolve()` method with the appropriate logic.

The package already includes concrete implementations that can be replaced, should there be a need for it.

## Context Resolver
The default `ContextResolver` implementation uses `App::runningUnitTests()` and `App::runningInConsole()` for its logic.

::: tip
You can generate a skeleton `ContextResolver` class with the `php artisan make:context-resolver` command.
:::

Here's an alternative implementation that does not depend on `Illuminate\Foundation\Application`:

```php
<?php

declare(strict_types=1);

namespace App\Resolvers;

use Altek\Accountant\Context;

class ContextResolver implements \Altek\Accountant\Contracts\ContextResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(): int
    {
        if (strpos($_SERVER['argv'][0] ?? '', 'phpunit') !== false) {
            return Context::TEST;
        }

        if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
            return Context::CLI;
        }

        return Context::WEB;
    }
}
```

Set the `accountant.resolvers.context` configuration value to the `FQCN` of the custom `ContextResolver` class:

```php
return [

    // ...

    'resolvers' = [
        'context' => App\Resolvers\ContextResolver::class,
        // ...
    ],

    // ...
];
```

## IP Address Resolver
The default `IpAddressResolver` implementation uses `Request::ip()` to get client IP addresses.

While that works for most applications, the ones running behind a proxy or a [load balancer](https://en.wikipedia.org/wiki/Load_balancing_(computing)) may need to get IP addresses differently.

Usually, the real IP address will be passed in a **X-Forwarded-For** HTTP header.

::: tip
You can generate a skeleton `IpAddressResolver` class with the `php artisan make:ip-address-resolver` command.
:::

Here's a resolver example for this use case.

```php
<?php

declare(strict_types=1);

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

Set the `accountant.resolvers.ip_address` configuration value to the `FQCN` of the custom `IpAddressResolver` class:

```php
return [

    // ...

    'resolvers' = [
        // ...
        'ip_address' => App\Resolvers\IpAddressResolver::class,
        // ...
    ],

    // ...
];
```

## URL Resolver
The default resolver uses the `Request::fullUrl()` method to get the current URL (including any query strings).

::: tip
You can generate a skeleton `UrlResolver` class with the `php artisan make:url-resolver` command.
:::

Here's a resolver example where query strings are not included.

```php
<?php

declare(strict_types=1);

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

Set the `accountant.resolvers.url` configuration value to the `FQCN` of the custom `UrlResolver` class:

```php
return [

    // ...

    'resolvers' = [
        // ...
        'url' => App\Resolvers\UrlResolver::class,
        // ...
    ],

    // ...
];
```

## User Agent Resolver
This resolver uses the `Request::header()` method without a default value, which returns `null` if a User Agent isn't available.

::: tip
You can generate a skeleton `UserAgentResolver` class with the `php artisan make:user-agent-resolver` command.
:::

The following example will return a default string when the `User-Agent` HTTP header is empty/unavailable.

```php
<?php

declare(strict_types=1);

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

Set the `accountant.resolvers.user_agent` configuration value to the `FQCN` of the custom `UserAgentResolver` class:

```php
return [

    // ...

    'resolvers' = [
        // ...
        'user_agent' => App\Resolvers\UserAgentResolver::class,
        // ...
    ],

    // ...
];
```

## User Resolver
The included `UserResolver` implementation uses the Laravel `Auth::guard()` method, by default.

The `resolve()` method must return an object implementing `Altek\Accountant\Contracts\Identifiable`, or `null` if the user cannot be resolved.

::: tip
You can generate a skeleton `UserResolver` class with the `php artisan make:user-resolver` command.
:::

### Identifiable implementation
Implementing the `Altek\Accountant\Contracts\Identifiable` interface on a `User` model:

```php
<?php

declare(strict_types=1);

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

declare(strict_types=1);

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

Set the `accountant.resolvers.user` configuration value to the `FQCN` of the custom `UserResolver` class:

```php
return [

    // ...

    'resolvers' = [
        // ...
        'user' => App\Resolvers\UserResolver::class,
    ],

    // ...
];
```

::: danger CAVEAT
Resolving a `User` in the CLI may not work.
:::
