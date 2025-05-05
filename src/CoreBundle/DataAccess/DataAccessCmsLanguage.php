<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataAccess;

use Doctrine\DBAL\Connection;

/**
 * DataAccessCmsLanguage provides an implementation of DataAccessCmsLanguageInterface for the default database backend.
 * Some methods load data manually instead of using existing TCMSRecord loading methods, because there are cases where
 * this would lead to endless recursion (loading the language is a special case as the language relies on the language
 * which implies an intrinsic potential for deadlocks).
 */
class DataAccessCmsLanguage implements DataAccessCmsLanguageInterface
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
     */
    public function getLanguage($id, $targetLanguageId)
    {
        $language = \TdbCmsLanguage::GetNewInstance();
        $languageRaw = $this->getLanguageRaw($id);
        if (null === $languageRaw) {
            return null;
        }
        $language->SetLanguage($targetLanguageId);
        $language->LoadFromRow($languageRaw);

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageRaw($id)
    {
        $language = \TdbCmsLanguage::GetNewInstance();
        $language->DisablePostLoadHook(true);
        if (false === $language->Load($id)) {
            return null;
        }

        return $language->sqlData;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageFromIsoCode($isoCode, $targetLanguageId)
    {
        $query = 'SELECT * FROM `cms_language` WHERE `iso_6391` = :isoCode';
        $row = $this->databaseConnection->fetchAssociative($query, [
            'isoCode' => $isoCode,
        ]);
        if (false === $row) {
            return null;
        }
        $language = \TdbCmsLanguage::GetNewInstance();
        $language->SetLanguage($targetLanguageId);
        $language->LoadFromRow($row);

        return $language;
    }
}
