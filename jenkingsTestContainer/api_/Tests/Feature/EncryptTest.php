<?php
/**
 * SearchTest
 *
 */

namespace Feature;

require_once __DIR__ . '/../BaseTest.php';


use App\Factory\FService;
use App\Services\PdfFilesService;
use Tests\BaseTest;

class EncryptTestTest extends BaseTest
{
    public function testEncrypt(): void
    {
        /** @var PdfFilesService $pdfFilesService */
        $pdfFilesService = FService::build('PdfFilesService');
        $key = 'ddd';
        $message = 'Test';
        $encryptedMessage = $pdfFilesService->encrypt($message, $key);
        $decryptedMessage = $pdfFilesService->decrypt($encryptedMessage, $key);
        $this->assertEquals($message, $decryptedMessage);

        $wrongKey = 'eee';
        $decryptedMessage = $pdfFilesService->decrypt($encryptedMessage, $wrongKey);
        $this->assertNotEquals($message, $decryptedMessage);
    }

    public function testInsertAndUpdate(): void
    {

        /** @var PdfFilesService $pdfFilesService */
        $pdfFilesService = FService::build('PdfFilesService');
        $key = 'd#dd';
        $file = [
            'name' => 'test 1',
            'text' => 'Parkeren
aanmeldenparkeren.amsterdam.nl
Meldcode 29468
Pin 2001
=============================================

yuowwa@gmail.com
ee@#ddd$3gfg()dsfdS',
            'key' => $key,
        ];

        $file['id'] = $pdfFilesService->store($file)['id'];

        $storedFile = $pdfFilesService->show($file['id'], $key);
        $this->assertEquals(
            $file['text'],
            urldecode($storedFile['text'])
        );

        $wrongKey = 'eee';
        $storedFile = $pdfFilesService->show($file['id'], $wrongKey);
        $this->assertNotEquals($file['text'],$storedFile['text']);

        $file['name'] = 'test 2';
        $key = 'eefwefwef';
        $pdfFilesService->update($file, $key);
        $storedFile = $pdfFilesService->show($file['id'], $key);
        $this->assertEquals(
            $file['text'],
            urldecode($storedFile['text'])
        );
    }

}