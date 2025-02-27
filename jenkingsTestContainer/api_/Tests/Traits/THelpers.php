<?php

namespace Tests\Traits;

use App\Enums\EAdminRoles;
use App\Factory\FModel;
use App\Models\MatchesForm;
use App\Models\MatchesUsers;
use App\Services\MatchesProfileService;

trait THelpers
{

    public static string $adminUserEmail = 'adminTest@test.nl';
    public static string $adminUserPassword = '1232ddf3Sw';



    public function createAdminUser(bool $apiTokenOnly = true): array
    {
        // create
        $params = [
            'name' => 'adminTest',
            'email' => self::$adminUserEmail,
            'password' => self::$adminUserPassword,
            'admin_role_id' => EAdminRoles::$ADMIN,
        ];
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users',
            $params, self::$CONTENT_TYPE_JSON);

        //login
        $response  = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [
                'email' => self::$adminUserEmail,
                'password' => self::$adminUserPassword,
            ],
            self::$CONTENT_TYPE_JSON);

        if ($apiTokenOnly) {
            return ['apiToken' => $response['body']['data'][0]['api_token']];
        } else {
            return $response;
        }
    }

}