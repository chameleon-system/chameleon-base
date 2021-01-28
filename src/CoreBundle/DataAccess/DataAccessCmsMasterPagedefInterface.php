<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;

interface DataAccessCmsMasterPagedefInterface
{
    public function get(string $id, ?string $type = null): ?CmsMasterPagdef;
}
