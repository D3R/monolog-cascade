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

namespace Cascade\Tests\Config\Loader\ClassLoader\Resolver;

use Cascade\Util;
use Cascade\Config\Loader\ClassLoader\Resolver\ConstructorResolver;
use PHPUnit\Framework\TestCase;
use Symfony;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * Class ConstructorResolverTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ConstructorResolverTest extends TestCase
{
    /**
     * Reflection class for which you want to resolve extra options
     * @var \ReflectionClass
     */
    protected $reflected;

    /**
     * Constructor Resolver
     * @var ConstructorResolver
     */
    protected $resolver;

    protected $class;

    /**
     * Set up function
     */
    protected function setUp(): void
    {
        $this->class = \Cascade\Tests\Fixtures\SampleClass::class;
        $this->resolver = new ConstructorResolver(new \ReflectionClass($this->class));
        parent::setUp();
    }

    /**
     * Tear down function
     */
    protected function tearDown(): void
    {
        $this->resolver = null;
        $this->class = null;
        parent::tearDown();
    }

    /**
     * Return the contructor args of the reflected class
     *
     * @return \ReflectionParameter[] array of params
     */
    protected function getConstructorArgs()
    {
        return $this->resolver->getReflected()->getConstructor()->getParameters();
    }

    /**
     * Test the resolver contructor
     */
    public function testConstructor(): void
    {
        $this->assertEquals($this->class, $this->resolver->getReflected()->getName());
    }

    /**
     * Test that constructor args were pulled properly
     *
     * Note that we need to deuplicate the CamelCase conversion here for old
     * fashioned classes
     */
    public function testInitConstructorArgs(): void
    {
        $expectedConstructorArgs = [];

        foreach ($this->getConstructorArgs() as $param) {
            $expectedConstructorArgs[Util::snakeToCamelCase($param->getName())] = $param;
        }

        $this->assertEquals($expectedConstructorArgs, $this->resolver->getConstructorArgs());
    }

    /**
     * Test the hashToArgsArray function
     */
    public function testHashToArgsArray(): void
    {
        $this->assertEquals(
            ['someValue', 'hello', 'there', 'slither'],
            $this->resolver->hashToArgsArray(
                [ // Not properly ordered on purpose
                    'optionalB'     => 'there',
                    'optionalA'     => 'hello',
                    'optionalSnake' => 'slither',
                    'mandatory'     => 'someValue',
                ]
            )
        );
    }

    /**
     * Data provider for testResolve
     *
     * The order of the input options does not matter and is somewhat random. The resolution
     * should reconcile those options and match them up with the contructor param position
     *
     * @return array of arrays with expected resolved values and options used as input
     */
    public static function optionsProvider(): array
    {
        return [
            [
                ['someValue', 'hello', 'there', 'slither'], // Expected resolved options
                [ // Options (order should not matter, part of resolution)
                    'optionalB'      => 'there',
                    'optionalA'      => 'hello',
                    'mandatory'      => 'someValue',
                    'optionalSnake'  => 'slither',
                ]
            ],
            [
                ['someValue', 'hello', 'BBB', 'snake'],
                [
                    'mandatory' => 'someValue',
                    'optionalA' => 'hello',
                ]
            ],
            [
                ['someValue', 'AAA', 'BBB', 'snake'],
                ['mandatory' => 'someValue']
            ]
        ];
    }

    /**
     * Test resolving with valid options
     *
     * @param array $expectedResolvedOptions Array of expected resolved options
     * (i.e. parsed and validated)
     * @param  array $options Array of raw options
     * @dataProvider optionsProvider
     */
    public function testResolve(array $expectedResolvedOptions, array $options): void
    {
        $this->assertEquals($expectedResolvedOptions, $this->resolver->resolve($options));
    }

    /**
     * Data provider for testResolveWithInvalidOptions.
     *
     * The order of the input options does not matter and is somewhat random. The resolution
     * should reconcile those options and match them up with the contructor param position
     *
     * @return array of arrays with expected resolved values and options used as input
     */
    public static function missingOptionsProvider(): array
    {
        return [
            [
                [ // No values
                ],
                [ // Missing a mandatory value
                    'optionalB' => 'BBB'
                ],
                [ // Still missing a mandatory value
                    'optionalB' => 'there',
                    'optionalA' => 'hello'
                ]
            ]
        ];
    }

    /**
     * Test resolving with missing/incomplete options. It should throw an exception.
     *
     * @param  array $incompleteOptions Array of invalid options
     * @dataProvider missingOptionsProvider
     */
    public function testResolveWithMissingOptions(array $incompleteOptions): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->resolver->resolve($incompleteOptions);
    }

    /**
     * Data provider for testResolveWithInvalidOptions
     *
     * The order of the input options does not matter and is somewhat random. The resolution
     * should reconcile those options and match them up with the contructor param position
     *
     * @return array of arrays with expected resolved values and options used as input
     */
    public static function invalidOptionsProvider(): array
    {
        return [
            [
                ['ABC'],
                [ // All invalid
                    'someInvalidOptionA' => 'abc',
                    'someInvalidOptionB' => 'def'
                ],
                [ // Some invalid
                    'optionalB' => 'there',
                    'optionalA' => 'hello',
                    'mandatory' => 'dsadsa',
                    'additionalInvalid' => 'some unknow param'
                ]
            ]
        ];
    }

    /**
     * Test resolving with invalid options. It should throw an exception.
     *
     * @param  array $invalidOptions Array of invalid options
     * @dataProvider invalidOptionsProvider
     */
    public function testResolveWithInvalidOptions(array $invalidOptions): void
    {
        $this->expectException(UndefinedOptionsException::class);

        $this->resolver->resolve($invalidOptions);
    }
}
