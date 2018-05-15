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

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;

abstract class AbstractFlysystemStorageAdapter implements StorageAdapterInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param array           $config
     * @param LoggerInterface $logger
     */
    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->filesystem = $this->getFilesystem($config);
        $this->logger = $logger;
    }

    /**
     * @param array $fileList
     */
    public function store(array $fileList)
    {
        foreach ($fileList as $file) {
            $this->logger->notice('Storing '.$file);
            try {
                $stream = fopen($file, 'r');
                $this->filesystem->writeStream(
                    basename($file),
                    $stream,
                    ['visibility' => AdapterInterface::VISIBILITY_PRIVATE]
                );
            } catch (\Exception $e) {
                $this->logger->error('Exception while storing '.$file.': '.$e->getMessage());
            }

            if (isset($stream) && is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /**
     * @return array
     */
    public function getListing()
    {
        return $this->filesystem->listFiles();
    }

    /**
     * @param array $fileList
     */
    public function delete(array $fileList)
    {
        foreach ($fileList as $file) {
            $this->logger->notice('Deleting '.$file);

            try {
                $this->filesystem->delete($file);
            } catch (\Exception $e) {
                $this->logger->error('Exception while deleting '.$file.': '.$e->getMessage());
            }
        }
    }

    /**
     * @return array
     */
    protected function getFilesystemConfig(): array
    {
        return [];
    }

    /**
     * @param array $config
     * @return Filesystem
     */
    abstract protected function getFilesystem(array $config): Filesystem;
}
