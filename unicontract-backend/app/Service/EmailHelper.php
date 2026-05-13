<?php

namespace App\Service;

use Illuminate\Support\Facades\Config;

class EmailHelper
{
    public static function hasAllowedDomain(?string $email): bool
    {
        if (!$email) {
            return false;
        }

        $email = strtolower(trim($email));
        $atPosition = strrpos($email, '@');

        if ($atPosition === false || $atPosition === strlen($email) - 1) {
            return false;
        }

        $domain = substr($email, $atPosition + 1);

        return in_array($domain, self::allowedDomains(), true);
    }

    public static function allowedDomains(): array
    {
        return array_values(array_filter(array_map(
            static fn ($domain) => strtolower(trim(ltrim($domain, '@'))),
            Config::get('unidem.allowed_email_domains', [])
        )));
    }
}
