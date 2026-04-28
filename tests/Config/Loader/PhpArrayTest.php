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

use Cascade\Config\Loader\PhpArray as ArrayLoader;
use Cascade\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpArrayTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class PhpArrayTest extends TestCase
{
    /**
     * Array loader object
     * @var ArrayLoader
     */
    protected $arrayLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->arrayLoader = new ArrayLoader();
    }

    protected function tearDown(): void
    {
        $this->arrayLoader = null;
        parent::tearDown();
    }

    /**
     * Test loading a Php array
     */
    public function testLoad(): void
    {
        $array = Fixtures::getSamplePhpArray();
        $this->assertSame($array, $this->arrayLoader->load($array));
    }

    /**
     * Data provider for testSupportsWithInvalidResource
     *
     * @return array array of non-array values
     */
    public static function notStringDataProvider(): array
    {
        return [
            ['Some string'],
            [true],
            [123],
            [123.456],
            [null],
            [new \stdClass()],
            [function (): void {
            }]
        ];
    }

    /**
     * Test loading resources supported by the YamlLoader
     *
     * @param mixed $invalidResource Invalid resource value
     * @dataProvider notStringDataProvider
     */
    public function testSupportsWithInvalidResource(string|bool|int|float|\stdClass|\Closure|null $invalidResource): void
    {
        $this->assertFalse($this->arrayLoader->supports($invalidResource));
    }

    /**
     * Test supports with a valid array
     */
    public function testSupports(): void
    {
        $array = Fixtures::getSamplePhpArray();
        $this->assertTrue($this->arrayLoader->supports($array));
    }
}
