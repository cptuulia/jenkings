<?php

namespace Tests;

/**
 * A base test for all tests.
 * This does some common functions for all tests
 *
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPUnit\Framework\TestCase;


class BaseTest extends TestCase
{


    /**
     * Set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }


}