<?php

namespace App\Controllers;


use App\Services\PdfFilesService;

;

class PdfController extends BaseController
{

    private PdfFilesService $pdfFilesServicee;


    public function __construct(array $request = [])
    {
        parent::__construct($request);
        $this->pdfFilesServicee = new PdfFilesService();
    }

    public function index(): array
    {

        $files = $this->pdfFilesServicee->index();

        return $this->response(
            [
                'count' => count($files),
                'data' => $files
            ]
        );
    }

    public function show(): array
    {
        $id = $this->requestUriParam;
        $key = urldecode($this->queryParams['key']) ;
        $file = $this->pdfFilesServicee->show($id, $key);

        return $this->response(
            [
                'data' => $file
            ]
        );
    }

    public function store(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            return $this->response(['errors' => $errors], self::$BadRequest);
        }

        $this->request['key']  = urldecode($this->request['key'] ) ;
        $response = $this->pdfFilesServicee->store($this->request);
        return $this->response([
                'message' => 'Pdf added successfully.',
                'data' => $response]
        );
    }

    public function update(): array
    {
        $errors = $this->validate();
        if (!empty($errors)) {
            return $this->response(['errors' => $errors], self::$BadRequest);
        }
        $columns = array_merge(
            [
                'id' => $this->requestUriParam
            ],
            $this->request
        );
        $user = $this->pdfFilesServicee->update(
            $columns,
            urldecode($this->request['key'])
        );
        return $this->response([
                'message' => 'update successfull.',
                'data' => $user]
        );
    }


    public function destroy()
    {
        $this->pdfFilesServicee->delete($this->requestUriParam);
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
            'text' => [
                'label' => 'Tekst',
                'type' => self::$VALIDATE_STRING,
                'required' => true,
            ],
            'key' => [
                'label' => 'Sleutel',
                'type' => self::$VALIDATE_STRING,
                'required' => true,
            ],
        ];
        return $rules;
    }
}