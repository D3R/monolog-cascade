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

namespace Cascade\Tests\Config\Loader\ClassLoader;

use Monolog\Formatter\LineFormatter;
use Cascade\Config\Loader\ClassLoader\HandlerLoader;
use PHPUnit\Framework\TestCase;

/**
 * Class HandlerLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class HandlerLoaderTest extends TestCase
{
    public function testHandlerLoader(): void
    {
        $dummyClosure = function (): void {
            // Empty function
        };
        $original = [
            'class' => \Monolog\Handler\TestHandler::class,
            'level' => 'DEBUG',
            'formatter' => 'test_formatter',
            'processors' => ['test_processor_1', 'test_processor_2']
        ];
        $options = [
            'class' => \Monolog\Handler\TestHandler::class,
            'level' => 'DEBUG',
            'formatter' => 'test_formatter',
            'processors' => ['test_processor_1', 'test_processor_2']
        ];
        $formatters = ['test_formatter' => new LineFormatter()];
        $processors = [
            'test_processor_1' => $dummyClosure,
            'test_processor_2' => $dummyClosure
        ];
        new HandlerLoader($options, $formatters, $processors);

        $this->assertNotEquals($original, $options);
        $this->assertSame($formatters['test_formatter'], $options['formatter']);
        $this->assertSame($processors['test_processor_1'], $options['processors'][0]);
        $this->assertSame($processors['test_processor_2'], $options['processors'][1]);
    }

    public function testHandlerLoaderWithNoOptions(): void
    {
        $original = [];
        $options = [];
        new HandlerLoader($options);
        $this->assertEquals($original, $options);
    }

    public function testHandlerLoaderWithInvalidFormatter(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $options = [
            'formatter' => 'test_formatter'
        ];

        $formatters = ['test_formatterXYZ' => new LineFormatter()];
        new HandlerLoader($options, $formatters);
    }

    public function testHandlerLoaderWithInvalidProcessor(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $dummyClosure = function (): void {
            // Empty function
        };
        $options = [
            'processors' => ['test_processor_1']
        ];

        $formatters = [];
        $processors = ['test_processorXYZ' => $dummyClosure];
        new HandlerLoader($options, $formatters, $processors);
    }

    public function testHandlerLoaderWithInvalidHandler(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $dummyClosure = function (): void {
            // Empty function
        };
        $options = [
            'handler' => 'test_handler'
        ];

        $formatters = [];
        $processors = [];
        $handlers = ['test_handlerXYZ' => $dummyClosure];
        new HandlerLoader($options, $formatters, $processors, $handlers);
    }

    public function testHandlerLoaderWithInvalidHandlers(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $dummyClosure = function (): void {
            // Empty function
        };
        $options = [
            'handlers' => ['test_handler_1', 'test_handler_2']
        ];

        $formatters = [];
        $processors = [];
        $handlers = [
            'test_handler_1' => $dummyClosure,
            'test_handlerXYZ' => $dummyClosure
        ];
        new HandlerLoader($options, $formatters, $processors, $handlers);
    }

    /**
     * Check if the handler exists for a given class and option
     * Also checks that it a callable and return it
     *
     * @param  string $class Class name the handler applies to
     * @param  string $optionName Option name
     * @return \Closure Closure
     * @throws \Exception
     */
    private function getHandler(string $class, string $optionName)
    {
        if (isset(HandlerLoader::$extraOptionHandlers[$class][$optionName])) {
            // Get the closure
            $closure = HandlerLoader::$extraOptionHandlers[$class][$optionName];

            $this->assertTrue(is_callable($closure));

            return $closure;
        } else {
            throw new \Exception(
                sprintf(
                    'Custom handler %s is not defined for class %s',
                    $optionName,
                    $class
                )
            );
        }
    }

    /**
     * Tests that calling the given Closure will trigger a method call with the given param
     * in the given class
     *
     * @param  string $class Class name
     * @param  string $methodName Method name
     * @param  mixed $methodArg Parameter passed to the closure
     * @param  \Closure $closure Closure to call
     */
    private function doTestMethodCalledInHandler(string $class, string $methodName, array|\Monolog\Formatter\LineFormatter $methodArg, \Closure $closure): void
    {
        // Setup mock and expectations
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->onlyMethods([$methodName])
            ->getMock();

        $mock->expects($this->once())
            ->method($methodName)
            ->with($methodArg);

        // Calling the handler
        $closure($mock, $methodArg);
    }


    /**
     * Test that handlers exist
     */
    public function testHandlersExist(): void
    {
        $options = [];
        new HandlerLoader($options);
        $this->assertTrue(count(HandlerLoader::$extraOptionHandlers) > 0);
    }

    /**
     * Data provider for testHandlers
     * /!\ Important note:
     * Just add values to this array if you need to test a newly added handler
     *
     * If one of your handlers calls more than one method you can add more than one entries
     *
     * @return array of array of args for testHandlers
     */
    public static function handlerParamsProvider(): array
    {
        return [
            [
                '*',                    // Class name
                'formatter',            // Option name
                new LineFormatter(),    // Option test value
                'setFormatter'          // Name of the method called by your handler
            ],
            [
                \Monolog\Handler\LogglyHandler::class,    // Class name
                'tags',                             // Option name
                ['some_tag'],                  // Option test value
                'setTag'                            // Name of the method called by your handler
            ]
        ];
    }

    /**
     * Test the extra option handlers
     *
     * @param  string $class Class name
     * @param  string $optionName Option name
     * @param  mixed $optionValue Option value
     * @param  string $calledMethodName Expected called method name
     * @dataProvider handlerParamsProvider
     */
    public function testHandlers(string $class, string $optionName, \Monolog\Formatter\LineFormatter|array $optionValue, string $calledMethodName): void
    {
        $options = [];
        new HandlerLoader($options);
        // Test if handler exists and return it
        $closure = $this->getHandler($class, $optionName);

        if ($class === '*') {
            $class = \Monolog\Handler\TestHandler::class;
        }

        $this->doTestMethodCalledInHandler($class, $calledMethodName, $optionValue, $closure);
    }

    /**
     * Test extra option processor handler
     */
    public function testHandlerForProcessor(): void
    {
        $options = [];

        $mockProcessor1 = function (array $record) {
            $record['extra']['dummy'] = 'Hello world 1!';
            return $record;
        };
        $mockProcessor2 = function (array $record) {
            $record['extra']['dummy'] = 'Hello world 1!';
            return $record;
        };
        $processorsArray = [$mockProcessor1, $mockProcessor2];

        // Setup mock and expectations
        $mockHandler = $this->getMockBuilder(\Monolog\Handler\TestHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['pushProcessor'])
            ->getMock();

        $mockHandler->expects($this->exactly(count($processorsArray)))
            ->method('pushProcessor');

        new HandlerLoader($options);
        $closure = $this->getHandler('*', 'processors');
        $closure($mockHandler, $processorsArray);
    }

    public function testReplacesHandlerNamesInOptionsArrayWithLoadedCallable(): void
    {
        $options = [
            'handlers' => [
                'foo',
                'bar',
            ],
            'handler' => 'baz'
        ];

        $formatters = [];
        $processors = [];
        $handlers = [
            'foo' => fn(): string => 'foo',
            'bar' => fn(): string => 'bar',
            'baz' => fn(): string => 'baz',
        ];

        new HandlerLoader($options, $formatters, $processors, $handlers);

        $this->assertSame($handlers['foo'], $options['handlers'][0]);
        $this->assertSame($handlers['bar'], $options['handlers'][1]);
        $this->assertSame($handlers['baz'], $options['handler']);
    }
}
