<?php

namespace App\Services;

use App\Enums\EAdminRoles;
use App\Factory\FModel;
use App\Helpres\HConfig;
use App\Helpres\HLogStatement;
use App\Models\LogStatements;
use App\Services\Traits\TEncrypt;
use App\Traits\TAuthenticate;
use mysql_xdevapi\Exception;


class AdminUsersService extends BaseService
{
    use TAuthenticate;
    use TEncrypt;

    protected $modelName = 'AdminUsers';

    public function __construct()
    {
        parent:: __construct();
    }

    public function get($value = null, array $options = []): array
    {
        return $this->model->get($value, $options);
    }


    public function store(array $columns, bool $filterColumns = true): array
    {
        if (isset($columns['password'])) {
            $columns['password'] = $this->crypt($columns['password'], self::adminSalt());
        }
        $id = $this->model->insert($columns, $filterColumns);
        return $this->show($id);
    }

    public function update(array $request): array
    {
        if (isset($request['new_password'])) {
            $request['password'] = $this->crypt($request['new_password'], self::adminSalt());;
        }
        $this->model->update($request);
        return $this->show($request['id']);
    }

    public function login(array $request): array
    {
        $user = $this->model->get(
            $request['email'],
            [
                'column' => 'email',
                'filter' => [
                    'needle' => $this->crypt($request['password'], self::adminSalt()),
                    'columns' => 'password'
                ]
            ]
        );
        return $this->setUserLoggedIn($user);
    }

    private function setUserLoggedIn(array $user): array
    {
        if (!empty($user)) {
            $user[0]['api_token'] = self::generateToken();
            $user[0]['api_token_expires'] = $this->getTokenExpires();
            $user[0]['email_login_hash'] = null;
            $user[0]['email_login_hash_expires'] = 0;
            $this->model->update($user[0]);
        }
        return $user;
    }


    public function loginByToken(string $emailToken): array
    {
        $user = $this->model->get(
            $emailToken,
            [
                'column' => 'email_login_hash',
                'filter' => [
                    'needle' => time(),
                    'columns' => 'email_login_hash_expires',
                    'operators' => ['email_login_hash_expires' => '>']
                ]
            ]
        );

        return $this->setUserLoggedIn($user);
    }


    public function generateEmailLoginToken(array $request): array
    {
        $user = $this->model->get(
            $request['email'],
            [
                'column' => 'email'
            ]
        );

        if (!empty($user)) {
            $user[0]['email_login_hash'] = self::generateToken();
            $user[0]['email_login_hash_expires'] = $this->getTokenExpires();
            $this->model->update($user[0]);

            $loginLink = HConfig::getConfig('frontEndHost')
                . '/admin/emailLogin?token=' . $user[0]['email_login_hash'];
            $message = 'Beste ' . $user[0]['name'] . '. <br> U kunt met de benedenstaande link binne 15 min' .
                ' eenmalig inloggen<br><a href="'.$loginLink.'">' . $loginLink . '</a>';


            $headers = 'From: ' . HConfig::getConfig('mailData.fromEmail') . "\r\n" .
                'Reply-To: ' . HConfig::getConfig('mailData.replyEmail') . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();


            $subject = 'Wactwoord herstellen';
                HLogStatement::set(
                    'SEND MAIL: ' . $user[0]['email']. "\n" .
                    'subject: ' .$subject. "\n" .
                    'message: ' . $message. "\n" .
                    'headers: ' . $headers
                );

            try {
                mail(
                    $user[0]['email'],
                    $subject,
                    $message,
                    $headers);
            } catch (Exception $e) {
                HLogStatement::set('MAIL ERROR' .  $e->getMessage() );
            }
            HLogStatement::set('MAIL SENT');
        } else {
            HLogStatement::set('Failed e-mail hash request by the e-mail ' .  $request['email']);
        }
        return $user;
    }

    public function logout(int $id)
    {
        $user = $this->model->get($id);

        if (!empty($user)) {
            $user[0]['api_token'] = null;
            $user[0]['api_token_expires'] = null;
            $this->model->update($user[0]);
        }
        return $user;
    }

    private function getTokenExpires(): int
    {
        $delay = HConfig::getConfig('admin.api_key_expires');
        $apiTokenExpires = time() + $delay;
        return $apiTokenExpires;
    }

    public function setApiTokenExpires(int $userId): void
    {
        $this->model->update([
            'id' => $userId,
            'api_token_expires' => $this->getTokenExpires(),
        ]);
    }

    public function checkAdminApiToken(string $apiToken): array
    {
        $user = $this->model->get(
            $apiToken,
            [
                'column' => 'api_token',
                'filter' => [
                    'needle' => time(),
                    'columns' => 'api_token_expires',
                    'operators' => ['api_token_expires' => '>']
                ]
            ]
        );
        if (!empty($user)) {
            $this->setApiTokenExpires($user[0]['id']);
        }
        return $user;
    }

    public function show(int $id)
    {
        return $this->model->get($id);
    }

    public function delete(int $id): bool
    {
        $this->model->delete($id);
        return true;
    }

    public function currentUser(): array
    {
        return self::getUserByApiToken();
    }

    public function checkShowAuthorization(int $id): bool
    {
        $user = $this->currentUser()[0];
        if ($user['admin_role_id'] == EAdminRoles::$ADMIN) {
            return true;
        }
        return $id == $user['id'];
    }
}