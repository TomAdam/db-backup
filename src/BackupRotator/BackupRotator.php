<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\BackupRotator;

use Instantiate\DatabaseBackup\StorageAdapter\StorageAdapterInterface;

class BackupRotator
{
    private $dailyCount;
    private $weeklyDay;
    private $weeklyCount;
    private $monthlyDay;
    private $monthlyCount;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->dailyCount = $config['daily_backups'];
        $this->weeklyCount = $config['weekly_backups'];
        $this->weeklyDay = $config['weekly_backup_day'];
        $this->monthlyCount = $config['monthly_backups'];
        $this->monthlyDay = $config['monthly_backup_day'];
    }

    /**
     * @param StorageAdapterInterface $storageAdapter
     */
    public function rotate(StorageAdapterInterface $storageAdapter)
    {
        $fileList = $storageAdapter->getListing();
        $deleteList = $this->getDeleteList($fileList);
        $storageAdapter->delete($deleteList);
    }

    /**
     * @param array $fileList
     *
     * @return array
     */
    private function getDeleteList(array $fileList)
    {
        $deleteList = [];
        $splitFileList = array_map(
            function ($file) {
                return array_merge(explode('_', $file, 3), [$file]);
            },
            $fileList
        );

        $collatedFileList = [];
        foreach ($splitFileList as $splitFile) {
            if (4 != count($splitFile)) {
                continue;
            }

            $date = \DateTime::createFromFormat('YmdHis', $splitFile[0]);
            $driver = $splitFile[1];
            $dbName = rtrim($splitFile[2], '.sql');
            $filename = $splitFile[4];

            if (!$date) {
                continue;
            }

            $collatedFileList[$driver][$dbName][$filename] = $date;
        }

        foreach ($collatedFileList as $driver => $dbs) {
            foreach ($dbs as $db => $files) {
                $dayCount = 0;
                $weekCount = 0;
                $monthCount = 0;
                krsort($files);
                foreach ($files as $filename => $date) {
                    if ($dayCount <= $this->dailyCount) {
                        ++$dayCount;
                        continue;
                    }

                    if ($date->format('w') == $this->weeklyDay && $weekCount <= $this->weeklyCount) {
                        ++$weekCount;
                        continue;
                    }

                    if ($date->format('j') == $this->monthlyDay && $monthCount <= $this->monthlyCount) {
                        ++$monthCount;
                        continue;
                    }

                    $deleteList[] = $filename;
                }
            }
        }

        return $deleteList;
    }
}
