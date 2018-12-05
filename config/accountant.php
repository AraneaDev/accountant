<?php

return [

    'ledger' => [

        /*
        |--------------------------------------------------------------------------
        | Ledger Implementation
        |--------------------------------------------------------------------------
        |
        | Define the Ledger implementation.
        |
        */

        'implementation' => Altek\Accountant\Models\Ledger::class,

        /*
        |--------------------------------------------------------------------------
        | CLI Recording
        |--------------------------------------------------------------------------
        |
        | Whether a Ledger should be created from Command Line Interface events.
        |
        */

        'cli' => false,

        /*
        |--------------------------------------------------------------------------
        | Ledger Threshold
        |--------------------------------------------------------------------------
        |
        | Specify a cutoff for the number of Ledger records a model can have.
        | Zero means unlimited.
        |
        */

        'threshold' => 0,

        /*
        |--------------------------------------------------------------------------
        | Ledger Driver
        |--------------------------------------------------------------------------
        |
        | The default driver used to store Ledger records.
        |
        */

        'driver' => 'database',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resolver Implementations
    |--------------------------------------------------------------------------
    |
    | Define the User, IP Address, User Agent and URL resolver implementations.
    |
    */

    'resolvers' => [
        'user'       => Altek\Accountant\Resolvers\UserResolver::class,
        'ip_address' => Altek\Accountant\Resolvers\IpAddressResolver::class,
        'user_agent' => Altek\Accountant\Resolvers\UserAgentResolver::class,
        'url'        => Altek\Accountant\Resolvers\UrlResolver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notary Implementation
    |--------------------------------------------------------------------------
    |
    | The default Notary implementation.
    |
    */

    'notary' => Altek\Accountant\Notary::class,

    /*
    |--------------------------------------------------------------------------
    | Recordable Events
    |--------------------------------------------------------------------------
    |
    | The events that trigger a new Ledger record.
    |
    */

    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],

    /*
    |--------------------------------------------------------------------------
    | User MorphTo relation prefix & default Guards
    |--------------------------------------------------------------------------
    |
    | Define the morph prefix and which authentication guards the User resolver
    | should use.
    |
    */

    'user' => [
        'prefix' => 'user',
        'guards' => [
            'web',
            'api',
        ],
    ],

];
