<?php

namespace Integration;
require_once __DIR__ . '/../BaseTest.php';


use App\Factory\FModel;
use App\Models\Facility;
use App\Models\PdfFiles;
use App\Plugins\Http\Response;
use Tests\BaseTest;
use Tests\Traits\THttpRequest;

class PdfControllerTest extends BaseTest
{

    use THttpRequest;

    public function setUp(): void
    {

        parent::setUp();
    }

    public function testAll()
    {
        // create api test user
        $apiToken = $this->createAdminUser()['apiToken'];


        $text = 'Parkeren
aanmeldenparkeren.amsterdam.nl
Meldcode 29223468
Pin 202301
==============
youtuublia@gmails.com';
        //
        //  Insert
        //
        $key = 'd#dd';
        $file = [
            'name' => 'test 1',
            'text' => $text,
            'key' => urlencode($key),
        ];

        $response = $this->sendRequest(
            'POST',
            '/api/pdf',
            $file,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken
        );

        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
        $this->assertEquals($file['name'], $response['body']['data']['name']);
      //  $this->assertEquals($key, $response['body']['data']['key']);

        //
        //  Show
        //
        $id = $response['body']['data']['id'];
        $response = $this->sendRequest(
            'GET',
            '/api/pdf/' . $id,
            $file,
            self::$CONTENT_TYPE_JSON,
            '&key=' . urlencode($key),
            $apiToken
        );
        $this->assertEquals($file['text'], urldecode($response['body']['data']['text']));

        //
        //  Show with wrong key
        //
        $id = $response['body']['data']['id'];
        $response = $this->sendRequest(
            'GET',
            '/api/pdf/' . $id,
            $file,
            self::$CONTENT_TYPE_JSON,
            '&key=wrong_' . $key,
            $apiToken
        );
        $this->assertNotEquals($file['text'], urldecode($response['body']['data']['text']));


        //
        //  Update
        //
        $key = 'ddd23ffer%^e';

        $text = 'alanat
1SalaS33dfana2!@#
S@1esfwewggPpuaT#hdfgghAs
PDF encrypt
tuulia, FGf121weegrg34#$#$


digiD
tewrg3dfg@gmail.com

TYty78&*

=============================================


oba Wifi tuulia@tantonius.com standard2 j
=============================================

sofi 2wgg906934


=============================================

NPIN 6254fh3f1
Visa GO 44fghr135
=============================================
APIN 04t677

=============================================

abn mastercard
icscreditcards.com/abnamro
login tewrg3dfg@gmail.com. 1Srghef4ana35

lost card 020 6 600 123
52rt6505 5024
12/28
code beind 457

=============================================

';
        $file = [
            'name' => 'test 12',
            'text' => $text,
            'key' => urlencode($key),
        ];

        $response = $this->sendRequest(
            'PUT',
            '/api/pdf/' . $id,
            $file,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken
        );

        $this->assertEquals(Response\Ok::STATUS_CODE, $response['status']);
        $this->assertEquals($file['name'], $response['body']['data']['name']);
        $this->assertEquals($file['text'], urldecode($response['body']['data']['text']));

        //
        //  Show
        //

        $response = $this->sendRequest(
            'GET',
            '/api/pdf/' . $id,
            $file,
            self::$CONTENT_TYPE_JSON,
            '&key=' . urlencode($key),
            $apiToken
        );
        $this->assertEquals($file['text'], urldecode($response['body']['data']['text']));

        $this->sendRequest(
            'DELETE',
            '/api/pdf/' . $id,
            $file,
            self::$CONTENT_TYPE_JSON,
            '',
            $apiToken
        );

        /** @var  PdfFiles $mPdfFiles */
        $mPdfFiles = FModel::build('PdfFiles');
        $pdf = $mPdfFiles->get($id);
        $this->assertEquals([], $pdf);
    }


}