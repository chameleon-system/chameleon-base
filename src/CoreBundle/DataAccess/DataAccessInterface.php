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

/**
 * DataAccessInterface defines a generic service that reads data from a data source.
 * These assumptions were made while designing this interface:
 * 1. There are lots of reads on single items, so that bulk reading is more efficient than reading every item independently.
 * 2. There is a cache mechanism for the fetched data, to avoid reading rather large datasets from the backend.
 * 3. There are higher-level services that operate on the fetched data. The returned values should be quite raw, so more
 *    sophisticated usage requires additional work.
 *
 * @template T extends \TCMSRecord
 */
interface DataAccessInterface
{
    /**
     * Loads all entries of the underlying data model.
     *
     * @param string|null $languageId if null, the currently active language is used
     *
     * @return T[]
     */
    public function loadAll($languageId = null);

    /**
     * Returns a list of cache triggers that should be called to invalidate the cache entries for this data model.
     *
     * @return string[]
     */
    public function getCacheTriggers();
}
