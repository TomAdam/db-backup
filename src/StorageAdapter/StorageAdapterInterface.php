<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\StorageAdapter;

interface StorageAdapterInterface
{
    public function store(array $fileList);

    public function getListing();

    public function delete(array $fileList);
}
