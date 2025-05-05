<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

class MltFieldUtil
{
    /*
    * If the MLT table name ends with numeric characters, check if it's the real target table name or
    * if the characters came from multiple MLT connection fields.
    *
    * @param string $mltTableName
    * @return string
    */
    /**
     * @param string $mltTableName
     *
     * @return string
     */
    public function cutMultiMltFieldNumber($mltTableName)
    {
        if (1 === preg_match('#^(.*?)\d+$#', $mltTableName, $matches)) {
            if (false === \TGlobal::TableExists($mltTableName)) {
                return $matches[1];
            }
        }

        return $mltTableName;
    }

    /**
     * @param string $mltTableName
     *
     * @return string
     */
    public function cutMltExtension($mltTableName)
    {
        if ('_mlt' === mb_substr($mltTableName, -4)) {
            return substr($mltTableName, 0, -4);
        }

        return $mltTableName;
    }

    /**
     * @param string $mltTableName
     *
     * @return string
     */
    public function getRealTableName($mltTableName)
    {
        $realTableName = $this->cutMltExtension($mltTableName);
        $realTableName = $this->cutMultiMltFieldNumber($realTableName);

        return $realTableName;
    }
}
