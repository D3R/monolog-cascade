<?php

/**
 * This file is part of the Monolog Cascade package.
 *
 * (c) Raphael Antonmattei <rantonmattei@theorchard.com>
 * (c) The Orchard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cascade\Tests;

use Monolog\Logger;
use Monolog\Registry;
use Cascade\Cascade;
use PHPUnit\Framework\TestCase;

/**
 * Class CascadeTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class CascadeTest extends TestCase
{
    protected function teardown(): void
    {
        Registry::clear();
        parent::teardown();
    }

    public function testCreateLogger(): void
    {
        $logger = Cascade::createLogger('test');

        $this->assertTrue($logger instanceof Logger);
        $this->assertEquals('test', $logger->getName());
        $this->assertTrue(Registry::hasLogger('test'));
    }

    public function testRegistry(): void
    {
        // Creates the logger and push it to the registry
        $logger = Cascade::logger('test');

        // We should get the logger from the registry this time
        $logger2 = Cascade::logger('test');
        $this->assertSame($logger, $logger2);
    }

    public function testRegistryWithInvalidName(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Cascade::getLogger(null);
    }

    public function testFileConfig(): void
    {
        $filePath = Fixtures::getPhpArrayConfigFile();
        Cascade::fileConfig($filePath);
        $this->assertInstanceOf(\Cascade\Config::class, Cascade::getConfig());
    }

    public function testLoadConfigFromArray(): void
    {
        $options = Fixtures::getPhpArrayConfig();
        Cascade::loadConfigFromArray($options);
        $this->assertInstanceOf(\Cascade\Config::class, Cascade::getConfig());
    }

    public function testLoadConfigFromStringWithJson(): void
    {
        $jsonConfig = Fixtures::getJsonConfig();
        Cascade::loadConfigFromString($jsonConfig);
        $this->assertInstanceOf(\Cascade\Config::class, Cascade::getConfig());
    }

    public function testLoadConfigFromStringWithYaml(): void
    {
        $yamlConfig = Fixtures::getYamlConfig();
        Cascade::loadConfigFromString($yamlConfig);
        $this->assertInstanceOf(\Cascade\Config::class, Cascade::getConfig());
    }

    public function testHasLogger(): void
    {
        // implicitly create logger "existing"
        Cascade::logger('existing');
        $this->assertFalse(Cascade::hasLogger('not_existing'));
        $this->assertTrue(Cascade::hasLogger('existing'));
    }
}
