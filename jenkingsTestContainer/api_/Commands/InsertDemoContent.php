<?php
/**
 * Usage  php Commands/InsertDemoContent.php
 *
 * env=test if you want to use tests
 */
namespace Commands;


use App\Factory\FService;

require_once __DIR__ . '/BaseCommand.php';

class InsertDemoContent extends BaseCommand
{
    protected function run(): void
    {
        /** @var DemoContentService $demoContentService */
        $demoContentService = FService::build('DemoContentService');
        $demoContentService->get();
    }
}

new InsertDemoContent($argv);
