<?php

namespace App\Traits;

use App\Enums\EAdminRoles;
use App\Factory\FService;
use App\Services\AdminUsersService;
use App\Helpres\HConfig;

trait TAuthenticate
{
    public static function authenticate(): bool
    {
        if (self::loginRequired()) {
            return self::checkApiToken();
        }
        return true;
    }

    private static function loginRequired(): bool
    {
        return self::pageAllowed(HConfig::getConfig('guestPages'));
    }

    private static function pageAllowed($pages): bool
    {
        global $router;
        $method = $router->getRequestMethod();
        $allowed = true;
        foreach ($pages[$method] as $uriPattern) {
            $uri = $router->getCurrentUri();
            if (self::patternMatches($uriPattern, $uri)) {
                $allowed = false;
            }
        }
        return $allowed;
    }

    private static function checkApiToken(): bool
    {
        $user = self::getUserByApiToken();
        if (empty($user)) {
            return false;
        }
        if ($user[0]['admin_role_id'] == EAdminRoles::$APPLICATON_MANAGER) {
            return !(self::pageAllowed(HConfig::getConfig('applicationManagerPages')));
        }
        // admin can all
        return  true;
    }

    private static function getUserByApiToken(): array
    {
        global $router;
        if (!isset($router->getRequestHeaders()['Authorization'])) {
            return [];
        }

        $apiToken = str_replace('Bearer ', '', $router->getRequestHeaders()['Authorization']);
        /** @var AdminUsersService $adminUsersService */
        $adminUsersService = FService::build('AdminUsersService');
        return $adminUsersService->checkAdminApiToken($apiToken);
    }


    private static function patternMatches($pattern, $uri): bool
    {
        $matches = [];
        // Replace all curly braces matches {} into word patterns (like Laravel)
        $pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);

        // we may have a match!
        return boolval(
            preg_match_all(
                '#^' . $pattern . '$#',
                $uri, $matches,
                PREG_OFFSET_CAPTURE
            )
        );
    }
}