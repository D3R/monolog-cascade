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

namespace Cascade\Tests\Config\Loader\FileLoader;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use org\bovigo\vfs\vfsStream;
use Cascade\Tests\Fixtures;

/**
 * Class FileLoaderAbstractTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class FileLoaderAbstractTest extends TestCase
{
    /**
     * Mock of extending Cascade\Config\Loader\FileLoader\FileLoaderAbstract
     */
    protected ?\PHPUnit\Framework\MockObject\MockObject $mock = null;

    protected function setUp(): void
    {
        parent::setUp();

        $fileLocatorMock = $this->getMockBuilder(\Symfony\Component\Config\FileLocatorInterface::class)
                                ->getMock();

        $this->mock = $this->getMockForAbstractClass(
            \Cascade\Config\Loader\FileLoader\FileLoaderAbstract::class,
            [$fileLocatorMock]
        );

        // Setting valid extensions for tests
        $this->mock::$validExtensions = ['test', 'php'];
    }

    protected function tearDown(): void
    {
        $this->mock = null;
        parent::tearDown();
    }

    /**
     * Test loading config from a valid file
     */
    public function testReadFrom(): void
    {
        $this->assertEquals(
            Fixtures::getSampleYamlString(),
            $this->mock->readFrom(Fixtures::getSampleYamlFile())
        );
    }

    /**
     * Test loading config from a valid file
     */
    public function testLoadFileFromString(): void
    {
        $this->assertEquals(
            trim(Fixtures::getSampleString()),
            $this->mock->readFrom(Fixtures::getSampleString())
        );
    }

    /**
     * Data provider for testGetSectionOf
     *
     * @return array array with original value, section and expected value
     */
    public static function extensionsDataProvider(): array
    {
        return [
            [true, 'hello/world.test'],
            [true, 'hello/world.php'],
            [false, 'hello/world.jpeg'],
            [false, 'hello/world'],
            [false, '']
        ];
    }

    /**
     * Test validating the extension
     *
     * @param boolean $expected Expected boolean value
     * @param string $filepath Filepath to validate
     * @dataProvider extensionsDataProvider
     */
    public function testValidateExtension(bool $expected, string $filepath): void
    {
        if ($expected) {
            $this->assertTrue($this->mock->validateExtension($filepath));
        } else {
            $this->assertFalse($this->mock->validateExtension($filepath));
        }
    }

    /**
     * Data provider for testGetSectionOf
     *
     * @return array array wit original value, section and expected value
     */
    public static function arrayDataProvider(): array
    {
        return [
            [
                [
                    'a' => ['aa' => 'AA', 'ab' => 'AB'],
                    'b' => ['ba' => 'BA', 'bb' => 'BB']
                ],
                'b',
                ['ba' => 'BA', 'bb' => 'BB']
            ],
            [
                ['a' => 'A', 'b' => 'B'],
                'c',
                ['a' => 'A', 'b' => 'B'],
            ],
            [
                ['a' => 'A', 'b' => 'B'],
                '',
                ['a' => 'A', 'b' => 'B'],
            ]
        ];
    }

    /**
     * Test the getSectionOf function
     *
     * @param array $array Array of options
     * @param string $section Section key
     * @param array $expected Expected array for the given section
     * @dataProvider arrayDataProvider
     */
    public function testGetSectionOf(array $array, string $section, array $expected): void
    {
        $this->assertSame($expected, $this->mock->getSectionOf($array, $section));
    }

    /**
     * Test loading an invalid file
     */
    public function testloadFileFromInvalidFile(): void
    {
        $this->expectException(\RuntimeException::class);

        // mocking the file system from a 'config_dir' base dir
        $root = vfsStream::setup('config_dir');

        // Adding an unreadable file (chmod 0000)
        vfsStream::newFile('config.yml', 0000)
            ->withContent(
                "---\n" .
                "hidden_config: true"
            )->at($root);

        // This will throw an exception because the file is not readable
        $this->mock->readFrom(vfsStream::url('config_dir/config.yml'));

        stream_wrapper_unregister(vfsStream::SCHEME);
    }
}
