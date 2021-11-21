<?php

namespace Jascha030\Xerox\Tests\Config;

use Jascha030\Xerox\Config\WPConfigStore;
use PHPUnit\Framework\TestCase;

class WPConfigStoreTest extends TestCase
{
    private const TEST_VALUES = [
        'DB_NAME' => 'testdb',
        'DB_USER' => 'user',
        'WP_HOME' => 'https://example.test',
        'WP_DEBUG' => true,
    ];

    private const UNSET_KEY = 'nonExistentKeyName';

    public function testCreate(): WPConfigStore
    {
        $store = WPConfigStore::create(self::TEST_VALUES);

        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf(WPConfigStore::class, $store);

        return $store;
    }

    /**
     * @depends testCreate
     */
    public function testExceptionOnReInitialiseAttempt(): void
    {
        $class = WPConfigStore::class;
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Static \"{$class}\" has already been initialised.");

        WPConfigStore::create([]);
    }



    /**
     * @depends testCreate
     */
    public function testHas(WPConfigStore $store): void
    {
        foreach (self::TEST_VALUES as $key => $value) {
            self::assertTrue($store::has($key));
            self::assertTrue(WPConfigStore::has($key));
        }

        self::assertFalse($store::has(self::UNSET_KEY));
    }

    /**
     * @depends testCreate
     */
    public function testAdd(WPConfigStore $store): void
    {
        self::assertFalse(WPConfigStore::has('theAnswer'));
        WPConfigStore::add('theAnswer', 42);

        self::assertTrue(WPConfigStore::has('theAnswer'));
        self::assertEquals(42, WPConfigStore::get('theAnswer'));

        // Test immutability
        WPConfigStore::add('theAnswer', 41);
        self::assertEquals(42, WPConfigStore::get('theAnswer'));
        self::assertEquals(42, $store::get('theAnswer'));
    }

    /**
     * @depends testCreate
     */
    public function testGet(WPConfigStore $store): void
    {
        foreach (self::TEST_VALUES as $key => $value) {
            self::assertEquals($value, $store::get($key));
            self::assertEquals($value, WPConfigStore::get($key));

            self::assertEquals(gettype($value), gettype($store::get($key)));
            self::assertEquals(gettype($value), gettype(WPConfigStore::get($key)));
        }
    }

    /**
     * @depends testCreate
     * @depends testGet
     */
    public function testGetUndefinedKeyThrowsException(WPConfigStore $store): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No configuration was found for key: "' . self::UNSET_KEY . '".');

        $store::get(self::UNSET_KEY);
    }

    /**
     * @depends testCreate
     * @depends testHas
     * @depends testAdd
     */
    public function testUnset(): void
    {
        self::assertFalse(WPConfigStore::has('TEST_CONSTANT'));
        WPConfigStore::add('TEST_CONSTANT', 'test value');

        self::assertTrue(WPConfigStore::has('TEST_CONSTANT'));
        self::assertEquals('test value', WPConfigStore::get('TEST_CONSTANT'));

        self::assertTrue(WPConfigStore::unset('TEST_CONSTANT'));
        self::assertFalse(WPConfigStore::unset(self::UNSET_KEY));

        self::assertFalse(WPConfigStore::has('TEST_CONSTANT'));
    }

    public function testSave(): void
    {
        // Assert constants are not yet set.
        foreach (self::TEST_VALUES as $key => $value) {
            self::assertFalse(defined($key));
        }

        // Assert missing required value.
        try {
            WPConfigStore::save();
        } catch (\Exception $exception) {
            self::assertInstanceOf(\RuntimeException::class, $exception);
            self::assertEquals(
                'Can\'t initialize WP without required wp-config value: "DB_PASSWORD".',
                $exception->getMessage()
            );
        }

        WPConfigStore::add('DB_PASSWORD', 'password');

        // Assert that we can't set predefined constant.
        self::assertFalse(defined('TEST_CONSTANT'));
        define('TEST_CONSTANT', 'test value');

        WPConfigStore::add('TEST_CONSTANT', 'test value 2');

        try {
            WPConfigStore::save();
        } catch (\Exception $exception) {
            self::assertInstanceOf(\RuntimeException::class, $exception);
            self::assertEquals(
                'Trying to define already defined constant: "TEST_CONSTANT".',
                $exception->getMessage()
            );
        }

        // Unset test constant.
        WPConfigStore::unset('TEST_CONSTANT');

        // Assert save set's constants.
        WPConfigStore::save();

        foreach (self::TEST_VALUES as $key => $value) {
            self::assertTrue(defined($key));
            self::assertEquals($value, constant($key));
        }
    }
}
