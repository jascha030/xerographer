<?php

/**
 * PHPUnit bootstrap file.
 * Uses current dir as working dir during test execution.
 */

declare(strict_types=1);

/**
 * Load the composer autoloader.
 */
include dirname(__FILE__, 4) . '/includes/loader.php';

require_once loader();

/**
 * Change working directory to current dir.
 * This is used during execution of `InitCommandTest::testExecute()`,
 * which uses the Symfony CommandTester to execute the init command in this directory.
 * When the test is executed successfully a .env file will temporarily be created in ./public.
 * This file will thereafter be removed by the `InitCommandTest::tearDown()` method.
 */
chdir(__DIR__);
