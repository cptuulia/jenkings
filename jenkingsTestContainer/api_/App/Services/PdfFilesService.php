<?php

namespace App\Services;


use App\Services\Traits\TPdfEncrypt;

class PdfFilesService extends BaseService
{
    use TPdfEncrypt;

    protected $modelName = 'PdfFiles';

    public function __construct()
    {
        parent:: __construct();
    }

    public function index(): array
    {
        return $this->model->get();
    }

    public function store(array $columns): array
    {
        if (isset($columns['text'])) {
            $columns['text'] = $this->encrypt($columns['text'], $columns['key']);
        }
        $id = $this->model->insert($columns);
        return $this->show($id, $columns['key']);
    }

    public function update(array $columns, string $key): array
    {
        if (isset($columns['text'])) {
            $columns['text'] = $this->encrypt($columns['text'], $key);
        }
        $this->model->update($columns);
        return $this->show($columns['id'], $key);
    }

    public function show(int $id, string $key = ''): array
    {
        $file = $this->model->get($id)[0];
        $file['text'] = urlencode(
            $this->decrypt($file['text'], $key
            )
        );

        return $file;
    }

    public function delete(int $id): bool
    {
        $this->model->delete($id);
        return true;
    }

}