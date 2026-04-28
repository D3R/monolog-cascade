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
namespace Cascade\Tests\Config;

use Cascade\Config\ConfigLoader;
use Cascade\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigLoaderTest
 *
 * @author Raphael Antonmattei <rantonmattei@theorchard.com>
 */
class ConfigLoaderTest extends TestCase
{
    /**
     * Loader to test against
     * @var ConfigLoader
     */
    protected $loader;

    protected function setUp(): void
    {
        parent::setup();
        $this->loader = new ConfigLoader();
    }

    protected function tearDown(): void
    {
        $this->loader = null;
        parent::tearDown();
    }

    public function testLoader(): void
    {
        $this->assertInstanceOf(
            \Symfony\Component\Config\Loader\DelegatingLoader::class,
            $this->loader
        );

        $this->assertInstanceOf(
            \Symfony\Component\Config\Loader\LoaderResolver::class,
            $this->loader->getResolver()
        );

        $configLoaders = $this->loader->getResolver()->getLoaders();
        $this->assertCount(4, $configLoaders);

        // Checking the order of thr loaders
        $this->assertInstanceOf(
            \Cascade\Config\Loader\PhpArray::class,
            $configLoaders[0]
        );
        $this->assertInstanceOf(
            \Cascade\Config\Loader\FileLoader\PhpArray::class,
            $configLoaders[1]
        );
        $this->assertInstanceOf(
            \Cascade\Config\Loader\FileLoader\Json::class,
            $configLoaders[2]
        );
        $this->assertInstanceOf(
            \Cascade\Config\Loader\FileLoader\Yaml::class,
            $configLoaders[3]
        );
    }

    public function testLoad(): void
    {
        $json = Fixtures::getSampleJsonString();
        $this->assertEquals(json_decode($json, true), $this->loader->load($json));
    }
}
