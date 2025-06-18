<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Module;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\DashboardModulesProvider;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\ImageCrop\Interfaces\CropImageServiceInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

class Dashboard extends \MTPkgViewRendererAbstractModuleMapper
{
    private const DEFAULT_BG_IMAGE = '/bundles/chameleonsystemcmsdashboard/images/dashboard-bg.png';

    public function __construct(
        private readonly DashboardModulesProvider $provider,
        private readonly SecurityHelperAccess $securityHelperAccess,
        private readonly RequestStack $requestStack,
        private readonly Connection $databaseConnection,
        private readonly CropImageServiceInterface $cropImageService,
        private readonly LanguageServiceInterface $languageService)
    {
        parent::__construct();
    }

    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $reload = $this->requestStack->getCurrentRequest()?->query->get('reload', false);
        if ('true' === $reload) {
            $reload = true;
        }

        $oVisitor->SetMappedValue('forceReload', $reload);
        $oVisitor->SetMappedValue('loggedInUserName', $this->getLoggedInUserName());
        $oVisitor->SetMappedValue('cmsOwner', $this->getOwnerName());
        $oVisitor->SetMappedValue('headerBackgroundImage', $this->getDashboardHeaderImage());
        $oVisitor->SetMappedValue('widgetCollections', $this->provider->getWidgetCollections());
        $oVisitor->SetMappedValue('availableCollections', $this->provider->getAvailableCollectionsForUser());
    }

    #[ExposeAsApi(description: 'Call this method dynamically via API:/cms/api/dashboard/widget/{widgetServiceId}/getWidgetHtmlAsJson')]
    public function saveWidgetLayout(array $widgetLayout): void
    {
        $user = $this->securityHelperAccess->getUser();
        if (null === $user) {
            return;
        }

        $query = 'UPDATE `cms_user` SET `dashboard_widget_config` = :layout WHERE `id` = :userId';
        $this->databaseConnection->executeQuery($query, ['layout' => json_encode($widgetLayout), 'userId' => $user->getId()]);
    }

    public function GetHtmlFooterIncludes(): array
    {
        $includes = [];
        $includes[] = '<script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemcmsdashboard/js/dashboard.js').'" type="text/javascript"></script>';

        $widgetCollections = $this->provider->getWidgetCollections();
        foreach ($widgetCollections as $widgetCollection) {
            foreach ($widgetCollection as $widgetData) {
                $footerIncludesFromWidget = $widgetData['widget']->getFooterIncludes();
                if (count($footerIncludesFromWidget) > 0) {
                    $includes = array_merge($includes, $footerIncludesFromWidget);
                }
            }
        }

        return $includes;
    }

    protected function getOwnerName(): string
    {
        return \TdbCmsConfig::GetNewInstance('1')->fieldName;
    }

    private function getLoggedInUserName(): string
    {
        $user = $this->securityHelperAccess->getUser();

        if (null === $user) {
            return '';
        }

        $name = $user->getFirstname();
        if ('' !== $name) {
            $name .= ' ';
        }

        $name .= $user->getLastname();

        return $name;
    }

    private function getDashboardHeaderImage(): string
    {
        $config = \TdbCmsConfig::GetNewInstance('1');

        if ('' === $config->fieldDashboardBg) {
            return self::DEFAULT_BG_IMAGE;
        }

        $imageDataModel = $this->cropImageService->getCroppedImageForCmsMediaIdAndCropId(
            $config->fieldDashboardBg,
            $config->fieldDashboardBgImageCropId,
            $this->languageService->getActiveLanguageId()
        );

        if (null === $imageDataModel) {
            return self::DEFAULT_BG_IMAGE;
        }

        return $imageDataModel->getImageUrl();
    }
}
