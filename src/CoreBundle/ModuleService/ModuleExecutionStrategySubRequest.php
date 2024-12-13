<?php

namespace ChameleonSystem\CoreBundle\ModuleService;

use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ModuleExecutionStrategySubRequest implements ModuleExecutionStrategyInterface
{
    private CacheInterface $cache;
    private KernelInterface $kernel;

    public function __construct(CacheInterface $cache, KernelInterface $kernel)
    {
        $this->cache = $cache;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Request $request, \TModelBase $module, $spotName, $isLegacyModule)
    {
        $esiPath = $this->getModuleESIPath($request, $module, $spotName);
        $moduleRequest = $request->duplicate();
        $moduleRequest->server->set('REQUEST_URI', $esiPath);
        $moduleRequest->attributes->set('_controller', $module);
        $moduleRequest->attributes->set('isLegacyModule', $isLegacyModule);

        return $this->kernel->handle($moduleRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * @param string $spotName
     *
     * @return string
     */
    private function getModuleESIPath(Request $request, \TModelBase $module, $spotName)
    {
        $sBaseUrl = $request->getPathInfo();

        if ('/' === substr($sBaseUrl, -1)) {
            $sBaseUrl = substr($sBaseUrl, 0, -1);
        }

        $aParts = [
            $sBaseUrl,
            \TModuleLoader::ESIMODULE_DIVIDER,
            $spotName,
        ];
        if (isset($module->aModuleConfig['instanceID']) && null !== $module->aModuleConfig['instanceID'] && '' !== $module->aModuleConfig['instanceID']) {
            $aParts[] = $module->aModuleConfig['instanceID'];
        }
        // add hash on parameters since they are always relevant
        $param = $request->query->all();
        $post = $request->request->all();
        if (is_array($post) && count($post) > 0) {
            $param = array_merge_recursive($param, $post);
        }
        if (count($param) > 0) {
            $aParts[] = $this->cache->getKey($param);
        }

        return implode('/', $aParts);
    }
}
