<?php

namespace App\Controllers;


use App\Services\AdminUsersService;
use App\Plugins\Http\Response as Status;

;

class AdminUsersController extends BaseController
{

    private AdminUsersService $adminUsersService;


    public function __construct(array $request = [])
    {
        parent::__construct($request);
        $this->adminUsersService = new AdminUsersService();
    }

    public function index(): array
    {

        $users = $this->adminUsersService->get();

        return $this->response(
            [
                'count' => count($users),
                'data' => $users
            ]
        );
    }

    public function show(): array
    {
        $id = $this->requestUriParam;
        if (!$this->adminUsersService->checkShowAuthorization($id)) {
            return $this->paginationResponse(['errors' => ['Not authorized']], self::$Unauthorized);
        }

        $status = $this->adminUsersService->show($id);
        return $this->response(
            [
                'data' => $status
            ]
        );
    }

    public function store(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            return $this->response(['errors' => $errors], self::$BadRequest);
        }

        $response = $this->adminUsersService->store($this->request);
        return $this->response([
                'message' => 'User register successfully.',
                'data' => $response]
        );
    }

    public function update(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            return $this->response(['errors' => $errors], self::$BadRequest);
        }
        $user = $this->adminUsersService->update($this->request);
        return $this->response([
                'message' => 'update successfull.',
                'data' => $user]
        );
    }


    public function login(): array
    {
        if (isset($this->queryParams['emailToken'])) {
            $user = $this->adminUsersService->loginByToken($this->queryParams['emailToken']);
            if (!empty($user)) {
                return $this->response([
                    'message' => 'Email login successfully.',
                    'data' => $user,

                ]);
            } else {
                return $this->response(['errors' => ['Login incorrect']], self::$Unauthorized);
            }
        }

        if (isset($this->request['generateEmailToken'])) {
            $user = $this->adminUsersService->generateEmailLoginToken($this->request);
            if (!empty($user)) {
                return $this->response([
                    'message' => 'Email login hash created.',
                    'data' => $user,
                ]);
            } else {
                return $this->response(['errors' => ['Incorrect email']], self::$Unauthorized);
            }
        }
        $user = $this->adminUsersService->login($this->request);
        if (!empty($user)) {
            return $this->response([
                'message' => 'User login successfully.',
                'data' => $user,

            ]);
        }
        $this->response(['errors' => ['Login incorrect']], self::$Unauthorized);
        return [];
    }

    public function logout(): array
    {
        $user = $this->adminUsersService->logout($this->requestUriParam);
        if (!empty($user)) {
            return $this->response([
                'message' => 'User logged out successfully.'

            ]);
        }
        $this->response(['errors' => ['Logout incorrect']], self::$Unauthorized);
        return [];
    }

    public function destroy()
    {
        $this->adminUsersService->delete($this->requestUriParam);
        return $this->response(['message' => 'User with id ' . $this->requestUriParam . ' is deleted.',]);
    }

    protected function getValidationRules(): array
    {
        $rules = [
            'name' => [
                'label' => 'naam',
                'type' => self::$VALIDATE_STRING,
                'required' => true,
            ],
            'email' => [
                'label' => 'e-mail',
                'type' => self::$VALIDATE_EMAIL,
                'unique' => [
                    'model' => 'AdminUsers',
                    'column' => 'email'
                ],
                'required' => true,
            ],
        ];

        $rules['new_password'] = [
            'label' => 'nieuwe wachtwoord',
            'type' => self::$VALIDATE_PASSWORD,
            'required' => false,

        ];


        if ($_SERVER["REQUEST_METHOD"] == 'POST') {

            $rules['admin_role_id'] = [
                'label' => 'role',
                'type' => self::$VALIDATE_INTEGER,
                'required' => true,
            ];
            $rules['password'] = [
                'label' => 'wachtwoord',
                'type' => self::$VALIDATE_PASSWORD,
                'unique' => [
                    'model' => 'AdminUsers',
                    'column' => 'email'
                ],
                'required' => true,
            ];
        }
        return $rules;
    }
}