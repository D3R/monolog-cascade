<?php

declare(strict_types=1);

/**
 * This file is part of the Monolog Cascade package.
 *
 * (c) Raphael Antonmattei <rantonmattei@theorchard.com>
 * (c) The Orchard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cascade\Tests\Config\Loader\ClassLoader;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Monolog\Registry;
use Cascade\Config\Loader\ClassLoader\LoggerLoader;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class LoggerLoaderTest extends TestCase
{
    /**
     * Tear down function
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Registry::clear();
    }

    public function testConstructor(): void
    {
        new LoggerLoader('testLogger');

        $this->assertTrue(Registry::hasLogger('testLogger'));
    }

    public function testResolveHandlers(): void
    {
        $options = [
            'handlers' => ['test_handler_1', 'test_handler_2']
        ];
        $handlers = [
            'test_handler_1' => new TestHandler(),
            'test_handler_2' => new TestHandler()
        ];
        $loader = new LoggerLoader('testLogger', $options, $handlers);

        $this->assertEquals(
            array_values($handlers),
            $loader->resolveHandlers($options, $handlers)
        );
    }

    public function testResolveHandlersWithMismatch(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $options = [
            'handlers' => ['unexisting_handler', 'test_handler_2']
        ];
        $handlers = [
            'test_handler_1' => new TestHandler(),
            'test_handler_2' => new TestHandler()
        ];
        $loader = new LoggerLoader('testLogger', $options, $handlers);

        // This should throw an InvalidArgumentException
        $loader->resolveHandlers($options, $handlers);
    }

    public function testResolveProcessors(): void
    {
        $dummyClosure = function (): void {
            // Empty function
        };
        $options = [
            'processors' => ['test_processor_1', 'test_processor_2']
        ];
        $processors = [
            'test_processor_1' => $dummyClosure,
            'test_processor_2' => $dummyClosure
        ];

        $loader = new LoggerLoader('testLogger', $options, [], $processors);

        $this->assertEquals(
            array_values($processors),
            $loader->resolveProcessors($options, $processors)
        );
    }

    public function testResolveProcessorsWithMismatch(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $dummyClosure = function (): void {
            // Empty function
        };
        $options = [
            'processors' => ['unexisting_processor', 'test_processor_2']
        ];
        $processors = [
            'test_processor_1' => $dummyClosure,
            'test_processor_2' => $dummyClosure
        ];

        $loader = new LoggerLoader('testLogger', $options, [], $processors);

        // This should throw an InvalidArgumentException
        $loader->resolveProcessors($options, $processors);
    }

    public function testLoad(): void
    {
        $options = [
            'handlers' => ['test_handler_1', 'test_handler_2'],
            'processors' => ['test_processor_1', 'test_processor_2']
        ];
        $handlers = [
            'test_handler_1' => new TestHandler(),
            'test_handler_2' => new TestHandler()
        ];
        $dummyClosure = function (): void {
            // Empty function
        };
        $processors = [
            'test_processor_1' => $dummyClosure,
            'test_processor_2' => $dummyClosure
        ];

        $loader = new LoggerLoader('testLogger', $options, $handlers, $processors);
        $logger = $loader->load();

        $this->assertTrue($logger instanceof Logger);
        $this->assertEquals(array_values($handlers), $logger->getHandlers());
        $this->assertEquals(array_values($processors), $logger->getProcessors());
    }
}
