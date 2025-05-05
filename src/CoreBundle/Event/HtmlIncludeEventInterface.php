<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Event;

interface HtmlIncludeEventInterface
{
    /**
     * only unique entries will be kept. unique will be determine as follows:
     *   - if an entry in the array has a key, that will be used to ensure uniqueness.
     *   - if it does not, the md5 sum of the content will be used.
     *
     * @return void
     */
    public function addData(array $data);

    /**
     * @return array
     */
    public function getData();

    public function removeDataElement(string $key): bool;

    public function updateDataElement(string $key, string $value): bool;
}
