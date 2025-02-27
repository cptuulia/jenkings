<?php
/**
 *  Arguments
 *   env=test   : use this if you want to use test database, as default the dev is used
 */

namespace Commands;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../App/Lib/php8.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Plugins;
use App\Plugins\Db\Db;
use Commands\Traits\TDatabase;


abstract class BaseCommand
{
    use TDatabase;


    /**
     * @var Db
     */
    protected $db;

    /** @var array */
    protected $arg;

    abstract protected function run(): void;

    /**
     * @param Db $db
     */
    public function __construct(array $argv)
    {
        $this->getArguments($argv);
        $env = isset($this->arg['env']) ?? '';
        $this->setDatabaseConnection($env);
        $this->run();
    }

    private function getArguments(array $arg): void
    {
        if (empty($arg)) {
            return;
        }
        foreach ($arg as $item) {
            $parts = explode('=', $item);
            if (count($parts) == 2) {
                $this->arg[current($parts)] = end($parts);
            }
        }
    }



}