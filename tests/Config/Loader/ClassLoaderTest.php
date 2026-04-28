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

namespace Cascade\Tests\Config\Loader;

use Cascade\Config\Loader\ClassLoader;
use Cascade\Tests\Fixtures\DependentClass;
use Cascade\Tests\Fixtures\SampleClass;
use PHPUnit\Framework\TestCase;

/**
 * Class ClassLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 * @author Dom Morgan <dom@d3r.com>
 */
class ClassLoaderTest extends TestCase
{
    /**
     * Set up function
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear down function
     */
    protected function tearDown(): void
    {
        ClassLoader::$extraOptionHandlers = [];
        parent::tearDown();
    }

    /**
     * Provides options with and without a class param
     * @return array of args
     */
    public static function dataFortestSetClass(): array
    {
        return [
            [
                [
                    'class' => \Cascade\Tests\Fixtures\SampleClass::class,
                    'some_param' => 'abc'
                ],
                \Cascade\Tests\Fixtures\SampleClass::class
            ],
            [
                [
                    'some_param' => 'abc'
                ],
                '\stdClass'
            ]
        ];
    }

    /**
     * Testing the setClass method
     *
     * @param  array $options Array of options
     * @param  string $expectedClass Expected classname of the instantiated object
     * @dataProvider dataFortestSetClass
     */
    public function testSetClass(array $options, string $expectedClass): void
    {
        $loader = new ClassLoader($options);

        $this->assertEquals($expectedClass, $loader->class);
    }

    public function testOptionsToCamelCase(): void
    {
        $array = ['hello_there' => 'Hello', 'bye_bye' => 'Bye'];

        $this->assertEquals(
            ['helloThere' => 'Hello', 'byeBye' => 'Bye'],
            ClassLoader::optionsToCamelCase($array)
        );
    }

    public function testGetExtraOptionsHandler(): void
    {
        ClassLoader::$extraOptionHandlers = [
            '*' => [
                'hello' => function ($instance, $value): void {
                    $instance->setHello(strtoupper($value));
                }
            ],
            \Cascade\Tests\Fixtures\SampleClass::class => [
                'there' => function ($instance, $value): void {
                    $instance->setThere(strtoupper($value) . '!!!');
                }
            ]
        ];

        $loader = new ClassLoader([]);
        $existingHandler = $loader->getExtraOptionsHandler('hello');
        $this->assertNotNull($existingHandler);
        $this->assertTrue(is_callable($existingHandler));

        $this->assertNull($loader->getExtraOptionsHandler('nohandler'));
    }

    public function testLoad(): void
    {
        $options = [
            'class' => \Cascade\Tests\Fixtures\SampleClass::class,
            'mandatory' => 'someValue',
            'optional_X' => 'testing some stuff',
            'optional_Y' => 'testing other stuff',
            'hello' => 'hello',
            'there' => 'there',
        ];

        ClassLoader::$extraOptionHandlers = [
            '*' => [
                'hello' => function ($instance, $value): void {
                    $instance->setHello(strtoupper($value));
                }
            ],
            \Cascade\Tests\Fixtures\SampleClass::class => [
                'there' => function ($instance, $value): void {
                    $instance->setThere(strtoupper($value) . '!!!');
                }
            ]
        ];

        $loader = new ClassLoader($options);
        $instance = $loader->load();

        $expectedInstance = new SampleClass('someValue');
        $expectedInstance->optionalX('testing some stuff');
        $expectedInstance->optionalY = 'testing other stuff';
        $expectedInstance->setHello('HELLO');
        $expectedInstance->setThere('THERE!!!');

        $this->assertEquals($expectedInstance, $instance);
    }

    /**
     * Test a nested class to load
     */
    public function testLoadDependency(): void
    {
        $options = [
            'class' => \Cascade\Tests\Fixtures\DependentClass::class,
            'dependency' => [
                'class' => \Cascade\Tests\Fixtures\SampleClass::class,
                'mandatory' => 'someValue',
            ]
        ];

        $loader = new ClassLoader($options);
        $instance = $loader->load();

        $expectedInstance = new DependentClass(
            new SampleClass('someValue')
        );

        $this->assertEquals($expectedInstance, $instance);
    }
}
