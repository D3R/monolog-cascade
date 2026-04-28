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
namespace Cascade\Tests;

class Fixtures
{
    /**
     * Return the fixture directory
     *
     * @return string Fixture directory
     */
    public static function fixtureDir()
    {
        return realpath(__DIR__ . '/Fixtures');
    }

    /**
     * Return a path to a non existing file
     *
     * @return string Wrong file path
     */
    public static function getInvalidFile(): string
    {
        return 'some/non/existing/file.txt';
    }

    /**
     * Return the fixture Yaml config file
     *
     * @return string Path to yaml config file
     */
    public static function getYamlConfigFile(): string
    {
        return self::fixtureDir() . '/fixture_config.yml';
    }

    /**
     * Return a config as Yaml
     *
     * @return string Yaml config
     */
    public static function getYamlConfig(): string|false
    {
        return file_get_contents(self::fixtureDir() . '/fixture_config.yml');
    }

    /**
     * Return the fixture sample Yaml file
     *
     * @return string Path to a sample yaml file
     */
    public static function getSampleYamlFile(): string
    {
        return self::fixtureDir() . '/fixture_sample.yml';
    }

    /**
     * Return the fixture sample Yaml string
     *
     * @return string Sample yaml string
     */
    public static function getSampleYamlString(): string
    {
        return trim(
            '---' . "\n" .
            'greeting: "hello"' . "\n" .
            'to: "you"' . "\n"
        );
    }

    /**
     * Return the fixture JSON config file
     *
     * @return string Path to JSON config file
     */
    public static function getJsonConfigFile(): string
    {
        return self::fixtureDir() . '/fixture_config.json';
    }

    /**
     * Return a config as JSON
     *
     * @return string JSON config
     */
    public static function getJsonConfig(): string|false
    {
        return file_get_contents(self::fixtureDir() . '/fixture_config.json');
    }

    /**
     * Return the fixture sample JSON file
     *
     * @return string Path to a sample JSON file
     */
    public static function getSampleJsonFile(): string
    {
        return self::fixtureDir() . '/fixture_sample.json';
    }

    /**
     * Return the fixture sample JSON string
     *
     * @return string Sample JSON string
     */
    public static function getSampleJsonString(): string
    {
        return trim(
            '{' . "\n" .
            '    "greeting": "hello",' . "\n" .
            '    "to": "you"' . "\n" .
            '}' . "\n"
        );
    }

    /**
     * Return a sample string
     *
     * @return string Sample string
     */
    public static function getSampleString(): string
    {
        return " some string with new \n\n lines and white spaces \n\n";
    }

    /**
     * Return the fixture PHP array config file
     *
     * @return string Path to PHP array config file
     */
    public static function getPhpArrayConfigFile(): string
    {
        return self::fixtureDir() . '/fixture_config.php';
    }

    /**
     * Return a config array
     *
     * @return array Config array
     */
    public static function getPhpArrayConfig()
    {
        return require self::fixtureDir() . '/fixture_config.php';
    }

    /**
     * Return a sample array
     *
     * @return array Sample array
     */
    public static function getSamplePhpArray(): array
    {
        return [
            'greeting' => 'hello',
            'to' => 'you'
        ];
    }
}
