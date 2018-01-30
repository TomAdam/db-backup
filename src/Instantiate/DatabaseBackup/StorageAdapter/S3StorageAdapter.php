<?php
/**
 * Instantiate
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Instantiate EULA that is bundled with
 * this package in the file LICENSE.
 *
 * If you did not receive a copy of the license please send an email
 * to info@instantiate.co.uk so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to info@instantiate.co.uk for more
 * information
 *
 * @author     Instantiate
 * @copyright  Copyright (c) 2015 Instantiate
 *
 * @see       http://www.instantiate.co.uk/
 *
 * @license    For the full copyright and license information, please view the
 *             LICENSE file that was distributed with this source code.
 */

namespace Instantiate\DatabaseBackup\StorageAdapter;

use Aws\S3\S3Client;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;

class S3StorageAdapter implements StorageAdapterInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param array           $config
     * @param LoggerInterface $logger
     */
    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->filesystem = $this->getFilesystem(
            $config['region'],
            $config['key'],
            $config['secret'],
            $config['bucket'],
            $config['prefix']
        );

        $this->logger = $logger;
    }

    /**
     * @param array $fileList
     */
    public function store(array $fileList)
    {
        foreach ($fileList as $file) {
            $this->logger->notice('Storing '.$file.' in s3 bucket '.$this->filesystem->getAdapter()->getBucket());
            try {
                $stream = fopen($file, 'r');
                $this->filesystem->writeStream(
                    $this->getStoredFilename($file),
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
            $this->logger->notice('Deleting '.$file.' in s3 bucket '.$this->filesystem->getAdapter()->getBucket());

            try {
                $this->filesystem->delete($file);
            } catch (\Exception $e) {
                $this->logger->error('Exception while deleting '.$file.': '.$e->getMessage());
            }
        }
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function getStoredFilename($file)
    {
        return basename($file);
    }

    /**
     * @param string $region
     * @param string $key
     * @param string $secret
     * @param string $bucket
     * @param string $prefix
     *
     * @return Filesystem
     */
    private function getFilesystem($region, $key, $secret, $bucket, $prefix)
    {
        $client = new S3Client([
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
            'region' => $region,
            'version' => 'latest',
        ]);

        $adapter = new AwsS3Adapter($client, $bucket, $prefix);

        return new Filesystem($adapter);
    }
}
