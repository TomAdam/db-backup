<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Instantiate\DatabaseBackup\StorageAdapter;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class LocalStorageAdapter extends AbstractFlysystemStorageAdapter
{
    /**
     * @param array $config
     * @return Filesystem
     */
    protected function getFilesystem(array $config): Filesystem
    {
        $adapter = new Local($config['path']);

        return new Filesystem($adapter);
    }
}
