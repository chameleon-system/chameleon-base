<?php

namespace ChameleonSystem\CmsChangeLogBundle\DataAccess;

use ChameleonSystem\CmsChangeLog\Exception\CmsChangeLogDataAccessFailedException;
use ChameleonSystem\CmsChangeLog\Interfaces\CmsChangeLogDataAccessInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class CmsChangeLogDataAccess implements CmsChangeLogDataAccessInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteOlderThan(int $days): int
    {
        if ($days <= 0) {
            return 0;
        }

        $minimumDate = new \DateTime();
        $minimumDate->setTime(0, 0);
        $minimumDate->sub(new \DateInterval(sprintf('P%sD', $days)));

        try {
            $result = $this->connection->executeQuery('DELETE `pkg_cms_changelog_set`, `pkg_cms_changelog_item` 
                                                   FROM `pkg_cms_changelog_set`
                                              LEFT JOIN `pkg_cms_changelog_item` ON `pkg_cms_changelog_set_id` = `pkg_cms_changelog_set`.`id`
                                                  WHERE `modify_date` < :minimumDate',
                ['minimumDate' => $minimumDate->format('Y-m-d')]
            );

            return $result->rowCount();
        } catch (Exception $exception) {
            throw new CmsChangeLogDataAccessFailedException('Deletion of changelog entries failed: '.$exception->getMessage(), 0, $exception);
        }
    }
}
