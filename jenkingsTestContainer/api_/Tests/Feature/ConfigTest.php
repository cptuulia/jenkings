<?php
/**
 * SearchTest
 *
 */

namespace Feature;

require_once __DIR__ . '/../BaseTest.php';


use App\Factory\FService;
use App\Helpres\HConfig;
use App\Services\PdfFilesService;
use Tests\BaseTest;

class oonfigTest extends BaseTest
{
    public function testEncrypt(): void
    {

        // Set a new config parameter
        $path = 'db.test2.test3';
        $newValue = 'asd';

        HConfig::setConfig($path, $newValue);
        $this->assertEquals(HConfig::getConfig($path) , $newValue);

        // Update existing config parameter
        $updatedValue = 'asd22#';
        HConfig::setConfig($path, $updatedValue);
        $this->assertEquals(HConfig::getConfig($path) , $updatedValue);

    }

}