<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use esono\pkgCmsCache\CacheInterface;

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

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    public function __construct(
        DataAccessCmsMasterPagedefInterface $subject,
        InputFilterUtilInterface $inputFilterUtil,
        CacheInterface $cache,
        RequestInfoServiceInterface $requestInfoService
    ) {
        $this->subject = $subject;
        $this->cache = $cache;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->requestInfoService = $requestInfoService;
    }

    public function get(string $id): ?CmsMasterPagdef
    {
        $cacheKeyParameter = $this->getCacheKeyParameters($id);

        $cacheKey = $this->cache->getKey($cacheKeyParameter);
        $pagedefData = $this->cache->get($cacheKey);
        if (null !== $pagedefData) {
            return $pagedefData;
        }

        $pagedefData = $this->subject->get($id);
        if (null === $pagedefData) {
            return null;
        }
        $aTrigger = [
            ['table' => 'cms_tpl_page', 'id' => $id],
            ['table' => 'cms_tree', 'id' => null],
            ['table' => 'cms_tree_node', 'id' => null],
            ['table' => 'cms_master_pagedef', 'id' => null],
        ];
        $this->cache->set($cacheKey, $pagedefData, $aTrigger);

        return $pagedefData;
    }

    private function getCacheKeyParameters(string $pagedef): array
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $security = $securityHelper->getSecurity();

        $cacheKey = [
            'type' => 'pagedefdata',
            'pagedef' => $pagedef,
            'requestMasterPageDef' => $this->inputFilterUtil->getFilteredInput('__masterPageDef', false),
            'isTemplateEngineMode' => $this->requestInfoService->isCmsTemplateEngineEditMode(),
            'cmsuserdefined' => null !== $security->getToken() && $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER),
        ];

        if ($cacheKey['cmsuserdefined'] && $cacheKey['requestMasterPageDef']) {
            $cacheKey['get_id'] = $this->inputFilterUtil->getFilteredInput('id');
        }

        return $cacheKey;
    }
}
