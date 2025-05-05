<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\Exception;

class DataAccessException extends \Exception
{
    /**
     * @param string $mediaItemId
     *
     * @return never
     *
     * @throws DataAccessException
     */
    public static function throwMediaItemNotFoundException($mediaItemId)
    {
        throw new self(sprintf("Media item with ID %s couldn't be found.", $mediaItemId));
    }
}
