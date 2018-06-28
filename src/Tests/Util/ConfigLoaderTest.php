<?php

namespace Instantiate\DatabaseBackup\Tests\Util;

use Instantiate\DatabaseBackup\Util\ConfigLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @covers \Instantiate\DatabaseBackup\Util\ConfigLoader
 */
class ConfigLoaderTest extends TestCase
{
    const RESOURCES_DIR = __DIR__ . '/Resources';

    public function testExceptionOnMissingConfig()
    {
        $this->expectException(\Exception::class);
        ConfigLoader::loadConfig('oops');
    }

    public function testLoadValidYamlFile()
    {
        $config = ConfigLoader::loadConfig(self::RESOURCES_DIR . '/good.yml');
        $this->assertTrue($config['testParam']);
    }

    public function testLoadInvalidYamlFile()
    {
        $this->expectException(ParseException::class);
        ConfigLoader::loadConfig(self::RESOURCES_DIR . '/bad.yml');
    }
}
