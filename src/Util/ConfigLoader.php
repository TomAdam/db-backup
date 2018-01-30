<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\Util;

use Symfony\Component\Yaml\Yaml;

class ConfigLoader
{
    /**
     * @param string $path
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function loadConfig($path)
    {
        if (!is_file($path)) {
            throw new \Exception('Could not load config file '.$path);
        }

        return Yaml::parse(file_get_contents($path));
    }
}
