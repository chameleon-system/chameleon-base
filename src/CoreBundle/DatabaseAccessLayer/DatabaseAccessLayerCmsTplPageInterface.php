<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DatabaseAccessLayer;

use TdbCmsTplPage;

/**
 * @deprecated since 6.1.0 use methods in chameleon_system_core.page_service instead
 */
interface DatabaseAccessLayerCmsTplPageInterface
{
    /**
     * @param string $id
     *
     * @return TdbCmsTplPage|null
     *
     * @deprecated since 6.1.0 - use chameleon_system_core.page_service::getById() instead
     */
    public function loadFromId($id);

    /**
     * @param string $treeId
     * @param bool   $bPreventFilter - default false
     *
     * @deprecated since 6.1.0 - use chameleon_system_core.page_service::getByTreeId() instead
     *
     * @return mixed
     */
    public function loadForTreeId($treeId, $bPreventFilter = false);
}
