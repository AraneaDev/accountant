<?php

namespace Altek\Accountant\Resolvers;

use Altek\Accountant\Contracts\Identifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UserResolver implements \Altek\Accountant\Contracts\UserResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(): ?Identifiable
    {
        $guards = Config::get('accountant.user.guards', [
            'web',
            'api',
        ]);

        foreach ($guards as $guard) {
            if ($user = Auth::guard($guard)->user()) {
                return $user;
            }
        }

        return null;
    }
}
