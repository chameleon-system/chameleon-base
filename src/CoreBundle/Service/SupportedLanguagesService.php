<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;

class SupportedLanguagesService implements SupportedLanguagesServiceInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * {@inheritdoc}
     * This implementation queries the database directly instead of using the TdbCmsConfig class, because it is
     * used during compile time where autoclasses might not be available.
     */
    public function getSupportedLanguages()
    {
        $locales = [];
        $locales[] = $this->getTranslationBaseLanguageId();
        $locales = array_merge($locales, $this->getTranslationLanguageIdList());

        return array_unique($locales);
    }

    /**
     * @return string
     *
     * @psalm-suppress FalsableReturnStatement
     */
    private function getTranslationBaseLanguageId()
    {
        $query = 'SELECT l.`iso_6391`
                  FROM `cms_config` AS c
                  JOIN `cms_language` AS l
                  ON c.`translation_base_language_id` = l.`id`
        ';

        return $this->databaseConnection->fetchOne($query);
    }

    /**
     * @return string[]
     */
    private function getTranslationLanguageIdList()
    {
        $query = 'SELECT l.`iso_6391` AS code
                  FROM `cms_config_cms_language_mlt` AS mlt
                  JOIN `cms_language` AS l
                  ON mlt.`target_id` = l.`id`
        ';
        $result = $this->databaseConnection->fetchAllAssociative($query);
        $localeList = [];
        foreach ($result as $row) {
            $localeList[] = $row['code'];
        }

        return $localeList;
    }
}
