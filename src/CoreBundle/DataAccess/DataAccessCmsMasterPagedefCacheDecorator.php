<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use esono\pkgCmsCache\CacheInterface;
use TGlobal;

class DataAccessCmsMasterPagedefCacheDecorator implements DataAccessCmsMasterPagedefInterface
{
    /**
     * @var DataAccessCmsMasterPagedefInterface
     */
    private $subject;
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    public function __construct(
        DataAccessCmsMasterPagedefInterface $subject,
        InputFilterUtilInterface $inputFilterUtil,
        CacheInterface $cache
    ) {
        $this->subject = $subject;
        $this->cache = $cache;
        $this->inputFilterUtil = $inputFilterUtil;
    }

    public function getPagedefObject(string $pagedef): ?CmsMasterPagdef
    {
        $cacheKey = $this->getCacheKeyParameters($pagedef);

        $key = $this->cache->getKey($cacheKey);
        $pagedefData = $this->cache->get($key);
        if (null !== $pagedefData) {
            return $pagedefData;
        }

        $pagedefData = $this->subject->getPagedefObject($pagedef);
        if (null === $pagedefData) {
            return null;
        }
        $aTrigger = array(
            array('table' => 'cms_tpl_page', 'id' => $pagedef),
            array('table' => 'cms_tree', 'id' => null),
            array('table' => 'cms_tree_node', 'id' => null),
            array('table' => 'cms_master_pagedef', 'id' => null),
        );
        $this->cache->set($key, $pagedefData, $aTrigger);

        return $pagedefData;
    }

    private function getCacheKeyParameters(string $pagedef): array
    {
        $cacheKey = array(
            'type'                 => 'pagedefdata',
            'pagedef'              => $pagedef,
            'requestMasterPageDef' => $this->inputFilterUtil->getFilteredInput('__masterPageDef', false),
            'isTemplateEngineMode' => TGlobal::IsCMSTemplateEngineEditMode(),
            'cmsuserdefined'       => TGlobal::CMSUserDefined(),
        );

        if ($cacheKey['cmsuserdefined'] && $cacheKey['requestMasterPageDef']) {
            $cacheKey['get_id'] = $this->inputFilterUtil->getFilteredInput('id');
        }

        return $cacheKey;
    }

}