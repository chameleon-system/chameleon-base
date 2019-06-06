<?php


namespace ChameleonSystem\CoreBundle\DataAccess;


use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;

interface DataAccessCmsMasterPagedefInterface
{
    public function getPagedefObject(string $pagedef): ?CmsMasterPagdef;
}