<?php

declare(strict_types=1);

namespace Jascha030\Xerox\Tests\Config;

use Jascha030\Xerox\Config\WPConfigStore;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Jascha030\Xerox\Config\WPConfigStore
 */
class WPConfigStoreTest extends TestCase
{
    private const TEST_VALUES = [
        'DB_NAME'  => 'testdb',
        'DB_USER'  => 'user',
        'WP_HOME'  => 'https://example.test',
        'WP_DEBUG' => true,
    ];

    private const UNSET_KEY = 'nonExistentKeyName';

    public function testCreate(): WPConfigStore
    {
        $store = WPConfigStore::create(self::TEST_VALUES);

        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf(WPConfigStore::class, $store);

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
            $this->assertTrue($store::has($key));
            $this->assertTrue(WPConfigStore::has($key));
        }

        $this->assertFalse($store::has(self::UNSET_KEY));
    }

    /**
     * @depends testCreate
     */
    public function testAdd(WPConfigStore $store): void
    {
        $this->assertFalse(WPConfigStore::has('theAnswer'));
        WPConfigStore::add('theAnswer', 42);

        $this->assertTrue(WPConfigStore::has('theAnswer'));
        $this->assertEquals(42, WPConfigStore::get('theAnswer'));

        // Test immutability
        WPConfigStore::add('theAnswer', 41);
        $this->assertEquals(42, WPConfigStore::get('theAnswer'));
        $this->assertEquals(42, $store::get('theAnswer'));
    }

    /**
     * @depends testCreate
     */
    public function testGet(WPConfigStore $store): void
    {
        foreach (self::TEST_VALUES as $key => $value) {
            $this->assertEquals($value, $store::get($key));
            $this->assertEquals($value, WPConfigStore::get($key));

            $this->assertEquals(gettype($value), gettype($store::get($key)));
            $this->assertEquals(gettype($value), gettype(WPConfigStore::get($key)));
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
        $this->assertFalse(WPConfigStore::has('TEST_CONSTANT'));
        WPConfigStore::add('TEST_CONSTANT', 'test value');

        $this->assertTrue(WPConfigStore::has('TEST_CONSTANT'));
        $this->assertEquals('test value', WPConfigStore::get('TEST_CONSTANT'));

        $this->assertTrue(WPConfigStore::unset('TEST_CONSTANT'));
        $this->assertFalse(WPConfigStore::unset(self::UNSET_KEY));

        $this->assertFalse(WPConfigStore::has('TEST_CONSTANT'));
    }

    /**
     * @depends testCreate
     * @depends testHas
     * @depends testAdd
     * @depends testGet
     * @depends testUnset
     */
    public function testSave(): void
    {
        // Assert constants are not yet set.
        foreach (self::TEST_VALUES as $key => $value) {
            $this->assertFalse(defined($key));
        }

        // Assert missing required value.
        try {
            WPConfigStore::save();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(\RuntimeException::class, $exception);
            $this->assertEquals(
                'Can\'t initialize WP without required wp-config value: "DB_PASSWORD".',
                $exception->getMessage()
            );
        }

        WPConfigStore::add('DB_PASSWORD', 'password');

        // Assert that we can't set predefined constant.
        $this->assertFalse(defined('TEST_CONSTANT'));
        define('TEST_CONSTANT', 'test value');

        WPConfigStore::add('TEST_CONSTANT', 'test value 2');

        try {
            WPConfigStore::save();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(\RuntimeException::class, $exception);
            $this->assertEquals(
                'Trying to define already defined constant: "TEST_CONSTANT".',
                $exception->getMessage()
            );
        }

        // Unset test constant.
        WPConfigStore::unset('TEST_CONSTANT');

        // Assert save set's constants.
        WPConfigStore::save();

        foreach (self::TEST_VALUES as $key => $value) {
            $this->assertTrue(defined($key));
            $this->assertEquals($value, constant($key));
        }
    }
}
