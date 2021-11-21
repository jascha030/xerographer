<?php

namespace Jascha030\Xerox\Tests\Config;

use Jascha030\Xerox\Config\WPConfigStore;
use PHPUnit\Framework\TestCase;

class WPConfigStoreTest extends TestCase
{
    public function testCreate(): WPConfigStore
    {
        $store = WPConfigStore::create([
            'DB_NAME' => 'test_db',
            'DB_USER' => 'user',
            'DB_PASSWORD' => 'password',
            'WP_HOME' => 'https://example.test',
            'WP_DEBUG' => true,
        ]);

        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf(WPConfigStore::class, $store);

        return $store;
    }

    /**
     * @depends testCreate
     */
    public function testGet(WPConfigStore $store): void
    {
        self::assertEquals(true, $store::get('WP_DEBUG'));
    }

//    public function testSave(): void
//    {
//    }
//
//    public function testHas(): void
//    {
//    }
//
//    public function testAdd(): void
//    {
//    }
}
