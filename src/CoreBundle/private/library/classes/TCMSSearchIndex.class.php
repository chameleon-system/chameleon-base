<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class TCMSSearchIndex
{
    /**
     * @var null|Connection
     */
    private $databaseConnection = null;

    /**
     * internal cache for url language mapping lookup.
     *
     * @var array
     */
    private $portalUrlLanguageMapping = array();

    private $timerStartTime = null;
    private $timerEndTime = null;

    /**
     * starts the index for given portal id.
     *
     * @param int $portalID
     */
    public function BuildIndex($portalID)
    {
        if ($this->createSearchIndexTempTable()) {
            $portal = new TdbCmsPortal();
            if ($portal->Load($portalID)) {
                $this->timerStart();

                $this->log(sprintf('Start indexing Portal "%s" (%s)', $portal->GetName(), $portal->id));

                $portalIndex = new TCMSSearchIndexPortal($this->getDatabaseConnection(), $portal);
                $portalIndex->startIndexing();

                $this->timerStop();
                $timerDiff = $this->timerGetDiffFormated();
                if (null != $timerDiff) {
                    $this->log(sprintf('Exection Time: %s for Portal "%s" (pages indexed: %s)', $timerDiff, $portal->GetName(), $portalIndex->getIndexedPagesCount()));
                } else {
                    $this->log('Exection Time: unavailable');
                }

                $this->CopyNewPortalIndex($portalID);
            } else {
                $this->log(sprintf('Portal with ID "%s" not found', $portal->id));
            }
        }
    }

    /**
     * starts internal timer.
     */
    private function timerStart()
    {
        $this->timerStartTime = time();
    }

    /**
     * stops internal timer.
     */
    private function timerStop()
    {
        $this->timerEndTime = time();
    }

    /**
     * returns formated (human readable) timer difference.
     *
     * @return null|string
     */
    private function timerGetDiffFormated()
    {
        $return = null;
        if (null != $this->timerStartTime && null != $this->timerEndTime) {
            $diffInSeconds = $this->timerEndTime - $this->timerStartTime;

            $return = gmdate('H:i:s', $diffInSeconds);
        }

        return $return;
    }

    /**
     * creates search index temp table.
     *
     * @return bool
     */
    protected function createSearchIndexTempTable()
    {
        $conn = $this->getDatabaseConnection();
        $stmt = $conn->executeQuery('SHOW CREATE TABLE `cms_search_index`');

        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            // drop temp table
            $conn->executeQuery('DROP TABLE `cms_search_index_tmp`');

            // $row[1] is the create statement
            $createTableQuery = $row[1];
            $createTableQuery = str_replace('cms_search_index', 'cms_search_index_tmp', $createTableQuery);

            // create temp table
            $conn->executeQuery($createTableQuery);

            // reset auto increment
            $conn->executeQuery('ALTER TABLE `cms_search_index_tmp` AUTO_INCREMENT = 1');

            $this->log('created temp search index table');

            return true;
        }

        $this->log('could NOT create temp search index table');

        return false;
    }

    protected function log($msg)
    {
        echo $msg.'<br />';
        flush();
    }

    /**
     * activates the new index for given portal id.
     *
     * @param int $portalID
     */
    public function CopyNewPortalIndex($portalID)
    {
        $conn = $this->getDatabaseConnection();

        // delete all entries for portal from "live" index
        $conn->executeQuery('DELETE FROM `cms_search_index` WHERE `cms_portal_id` = :cms_portal_id', array(
            'cms_portal_id' => $portalID,
        ));

        // reset the auto_increment value
        $conn->executeQuery('ALTER TABLE `cms_search_index` AUTO_INCREMENT = 1');

        // select all entries for portal from temp index
        $stmt = $conn->executeQuery('SELECT * FROM `cms_search_index_tmp` WHERE `cms_portal_id` = :cms_portal_id', array(
            'cms_portal_id' => $portalID,
        ));
        // insert entries from temp index to "live" index
        while ($row = $stmt->fetch(\PDO::FETCH_OBJ)) {
            $conn->executeQuery('INSERT INTO `cms_search_index`
								(`id`,`pagetitle`,`cms_portal_id`,`url`,`content`,`name`,`cms_language_id`)
						 VALUES
						 		(:id, :pagetitle, :cms_portal_id, :url, :content, :name, :cms_language_id)',
                            array(
                                'id' => $row->id,
                                'pagetitle' => $row->pagetitle,
                                'cms_portal_id' => $row->cms_portal_id,
                                'url' => $row->url,
                                'content' => $row->content,
                                'name' => $row->name,
                                'cms_language_id' => $row->cms_language_id,
                            )
            );
        }
    }

    /**
     * builds index for all portals.
     */
    public function BuildIndexForAllPortals()
    {
        $portalQuery = "SELECT * FROM `cms_portal` WHERE `index_search` = '1' ORDER BY `name`";
        $oPortalList = new TCMSPortalList();
        /** @var $oRecordList TCMSPortalList */
        $oPortalList->sTableName = 'cms_portal';
        $oPortalList->Load($portalQuery);

        while ($oPortal = $oPortalList->Next()) {
            /** @var $oPortal TCMSPortal */
            $this->BuildIndex($oPortal->id);
        }
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        if (null !== $this->databaseConnection) {
            return $this->databaseConnection;
        }

        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
