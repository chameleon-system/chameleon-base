<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;

/**
 * encrypted password field.
 *
 * {@inheritdoc}
 */
class TCMSFieldPasswordEncrypted extends TCMSFieldPassword
{
    public const ENCRYPTED_PASSWORD_RAW_FIELD_PREFIX = 'password_encrypted_raw_';

    public function __construct()
    {
        $this->bEncryptedData = true;
    }

    /**
     * converts data to field based data.
     *
     * @return string|bool
     */
    public function ConvertDataToFieldBasedData($sData)
    {
        $returnVal = '';
        $sRawValue = false;
        $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->table)."` WHERE `id` = '".$this->recordId."' ";
        $res = MySqlLegacySupport::getInstance()->query($sQuery);
        if (MySqlLegacySupport::getInstance()->num_rows($res) > 0) {
            $aTableRow = MySqlLegacySupport::getInstance()->fetch_assoc($res);
            if ('' != trim($aTableRow[$this->oDefinition->sqlData['name']])) {
                $sRawValue = $aTableRow[$this->oDefinition->sqlData['name']];
            }
        }
        $passwordHashGenerator = $this->getPasswordHashGenerator();
        if ($sRawValue) {
            if (false === $sData) {
                $returnVal = $sRawValue;
            } elseif ('' === $sData) {
                $returnVal = '';
            } elseif ($sData != $sRawValue) {
                if (true === $passwordHashGenerator->verify($sData, $sRawValue)) {
                    $returnVal = $sRawValue;
                } else {
                    $returnVal = $passwordHashGenerator->hash($sData);
                }
            } else {
                $returnVal = $sData;
            }
        } else {
            if (!empty($sData)) {
                $returnVal = $passwordHashGenerator->hash($sData);
            }
        }

        return $returnVal;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return '?';
    }

    /**
     * @return PasswordHashGeneratorInterface
     */
    private function getPasswordHashGenerator()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.security.password.password_hash_generator');
    }
}
