<?php

namespace Integration;
require_once __DIR__ . '/../BaseTest.php';

use App\Enums\EAdminRoles;
use App\Factory\FModel;
use App\Models\AdminUsers;
use App\Models\Facility;
use Tests\BaseTest;
use Tests\Traits\THttpRequest;
use App\Plugins\Http\Response;

class AdminUsersControllerTest extends BaseTest
{

    use THttpRequest;

    public function setUp(): void
    {

        parent::setUp();
    }

    public function testRegister()
    {

        //
        // Register
        //
        $params = [
            'name' => 'test',
            'email' => 'test@test.nl',
            'password' => '123df@eff34',
            'admin_role_id' => EAdminRoles::$ADMIN,
        ];
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users',
            $params, self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);

        //
        // Login
        //
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            $params, self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);

        //
        // Wrong Login
        //
        $params['password'] = 'wrong';
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            $params, self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Unauthorized::STATUS_CODE, $response['status']);
    }

    public function testAdminTestUser()
    {
        // create api test user
        $response = $this->createAdminUser(false);
        $userId = $response['body']['data'][0]['id'];

        //
        // Login
        //
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [
                'email' => self::$adminUserEmail,
                'password' => self::$adminUserPassword,
            ],
            self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
        $user = $response['body']['data'][0];
        $apiToken = $user['api_token'];

        //
        // Send request with api key
        //
        $response = $this->sendRequest(
            'GET',
            '/api/admin/users/',
            [],
            null,
            '',
            $apiToken
        );
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);

        //
        // Send request without api key
        //
        $response = $this->sendRequest(
            'GET',
            '/api/admin/users/'
        );
        $this->assertEquals(Response\Unauthorized::STATUS_CODE, $response['status']);

        //
        // Send request with  wrong api key
        //
        $response = $this->sendRequest(
            'GET',
            '/api/admin/users/',
            [],
            null,
            '',
            'WROON API KEY'
        );
        $this->assertEquals(Response\Unauthorized::STATUS_CODE, $response['status']);



        // Change password
        $newPassword = 'Xx23@#kdlff';
        $response = $this->sendRequest(
            'PUT',
            '/api/admin/users/' ,
            [
                'id' => $userId,
                'email' => self::$adminUserEmail,
                'name' => 'updated',
                'new_password' => $newPassword,
            ],
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);



        //
        // Logout
        //
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/logout/' . $user['id'],
            [
                'email' => self::$adminUserEmail,
                'password' => self::$adminUserPassword,
            ],
            self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);

        //
        // Send request with api key (now expired)
        //
        $response = $this->sendRequest(
            'GET',
            '/api/admin/users/',
            [],
            null,
            '',
            $apiToken
        );
        $this->assertEquals(Response\Unauthorized::STATUS_CODE, $response['status']);

        //
        // Login with new password
        //
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [
                'email' => self::$adminUserEmail,
                'password' => $newPassword,
            ],
            self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);




    }

    public function testEmailLogin(): void
    {
        // create api test user
        $response = $this->createAdminUser(false);
        $adminUser = $response['body']['data']['0'];

        // create email hash
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [
                'email' => self::$adminUserEmail,
                'generateEmailToken' => true,
            ],
            self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
        $user = $response['body']['data'][0];
        $emailLoginHash = $user['email_login_hash'];

        // try to log in with the hash
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [],
            null,
            '&emailToken=' . $emailLoginHash,
            self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
        $this->assertEquals(1, count($response['body']['data']));

        // try to login again with same hash
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [],
            null,
            '&emailToken=' . $emailLoginHash,
            self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Unauthorized::STATUS_CODE, $response['status']);

        //
        // Expired login hash
        //

        // create email hash
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [
                'email' => self::$adminUserEmail,
                'generateEmailToken' => true,
            ],
            self::$CONTENT_TYPE_JSON);
        // reset expires to the past
        $user = $response['body']['data'][0];
        $user['email_login_hash_expires'] = 1000;
        /** @var AdminUsers $mAdminUsers */
        $mAdminUsers = FModel::build('AdminUsers');
        $mAdminUsers->update($user);


        // try to log in with the hash
        $emailLoginHash = $user['email_login_hash'];
        $response = $this->sendRequest(
            'POST',
            '/api/admin/users/login',
            [],
            null,
            '&emailToken=' . $emailLoginHash,
            self::$CONTENT_TYPE_JSON);
        $this->assertEquals(Response\Unauthorized::STATUS_CODE, $response['status']);
    }


}