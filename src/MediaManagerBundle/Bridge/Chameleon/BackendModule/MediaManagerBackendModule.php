<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\BackendModule;

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\TokenInjectionFailedException;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\MediaManager\AccessRightsModel;
use ChameleonSystem\MediaManager\DataModel\MediaTreeDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaTreeNodeDataModel;
use ChameleonSystem\MediaManager\Exception\AccessRightException;
use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\Interfaces\MediaManagerListRequestFactoryInterface;
use ChameleonSystem\MediaManager\Interfaces\MediaManagerListStateServiceInterface;
use ChameleonSystem\MediaManager\Interfaces\MediaTreeDataAccessInterface;
use ChameleonSystem\MediaManager\JavascriptPlugin\JavascriptPluginConfiguration;
use ChameleonSystem\MediaManager\JavascriptPlugin\JavascriptPluginConfigurationState;
use ChameleonSystem\MediaManager\JavascriptPlugin\JavascriptPluginConfigurationUrls;
use ChameleonSystem\MediaManager\JavascriptPlugin\JavascriptPluginMessage;
use ChameleonSystem\MediaManager\JavascriptPlugin\JavascriptPluginRenderedContent;
use ChameleonSystem\MediaManager\JavascriptPlugin\MediaTreeNodeJsonObject;
use ChameleonSystem\MediaManager\MediaItemChainUsageFinder;
use ChameleonSystem\MediaManager\MediaManagerExtensionCollection;
use ChameleonSystem\MediaManager\MediaManagerListState;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaManagerBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    public const PAGEDEF_NAME = 'mediaManager';

    public const PAGEDEF_NAME_PICK_IMAGE = 'mediaManagerPickImage';

    public const PAGEDEF_TYPE = '@ChameleonSystemMediaManagerBundle';

    public const MEDIA_ITEM_URL_NAME = 'mediaItemId';

    public const URL_TEMPLATE_PLACEHOLDER_ID = '--id--';

    public function __construct(
        private readonly MediaTreeDataAccessInterface $mediaTreeDataAccess,
        private readonly MediaItemDataAccessInterface $mediaItemDataAccess,
        private readonly UrlUtil $urlUtil,
        private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly MediaManagerListStateServiceInterface $mediaManagerListStateService,
        private readonly LanguageServiceInterface $languageService,
        private readonly MediaManagerListRequestFactoryInterface $mediaManagerListRequestService,
        private readonly TranslatorInterface $translator,
        private readonly MediaManagerExtensionCollection $mediaManagerExtensionCollection,
        private readonly ResponseVariableReplacerInterface $responseVariableReplacer,
        private readonly LoggerInterface $logger,
        readonly private BackendSessionInterface $backendSession,
        readonly private MediaItemChainUsageFinder $mediaItemChainUsageFinder
    ) {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        try {
            $oVisitor->SetMappedValue('mediaTree', $this->getMediaTree());
            $oVisitor->SetMappedValue('javascriptPluginConfiguration', $this->createJavascriptPluginConfiguration());
            $oVisitor->SetMappedValue('javascriptPluginState', $this->createJavascriptPluginStateConfiguration());
        } catch (AccessRightException $e) {
            $oVisitor->SetMappedValue('messageCode', 'media_manager.permission_denied');
        } catch (DataAccessException $e) {
            $oVisitor->SetMappedValue(
                'messageCode',
                'chameleon_system_media_manager.media_items.not_found_error_message'
            );
        }
    }

    /**
     * @return MediaTreeDataModel|null
     *
     * @throws AccessRightException
     * @throws DataAccessException
     */
    private function getMediaTree()
    {
        if (false === $this->createMediaTreeAccessRightsModel()->show) {
            throw new AccessRightException(sprintf('Current user does not have permission to view media tree.'));
        }

        return $this->mediaTreeDataAccess->getMediaTree($this->backendSession->getCurrentEditLanguageId());
    }

    private function createMediaTreeAccessRightsModel(): AccessRightsModel
    {
        return $this->createAccessRightsModel('cms_media_tree');
    }

    private function createAccessRightsModel(string $tableName): AccessRightsModel
    {
        $accessRightsModel = new AccessRightsModel();
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $accessRightsModel->new = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $tableName);
        $accessRightsModel->edit = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $tableName);
        $accessRightsModel->delete = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $tableName);
        $accessRightsModel->show = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $tableName);

        return $accessRightsModel;
    }

    /**
     * @return JavascriptPluginConfiguration
     */
    private function createJavascriptPluginConfiguration()
    {
        $configuration = new JavascriptPluginConfiguration();
        $configuration->urls = $this->createJavascriptPluginUrlsConfiguration();
        $configuration->accessRightsMedia = $this->createMediaAccessRightsModel();
        $configuration->accessRightsMediaTree = $this->createMediaTreeAccessRightsModel();
        $configuration->activeMediaItemId = $this->inputFilterUtil->getFilteredGetInput('id');

        return $configuration;
    }

    /**
     * @return JavascriptPluginConfigurationUrls
     */
    private function createJavascriptPluginUrlsConfiguration()
    {
        $configurationUrls = new JavascriptPluginConfigurationUrls();

        $configurationUrls->listUrl = $this->getUrlToModuleFunction('renderList');
        $parameters = [
            MediaManagerListState::STATE_PARAM_NAME_MEDIA_TREE_NODE_ID => self::URL_TEMPLATE_PLACEHOLDER_ID,
        ];
        $configurationUrls->mediaTreeNodeInfoUrlTemplate = $this->getUrlToModuleFunction(
            'provideMediaTreeNodeInfo',
            $parameters
        );
        $configurationUrls->editMediaTreePropertiesUrlTemplate = $this->getMediaTreeNodeEditUrl(
            self::URL_TEMPLATE_PLACEHOLDER_ID
        );
        $configurationUrls->mediaTreeNodeInsertUrl = $this->getUrlToModuleFunction('insertMediaTreeNode');
        $configurationUrls->mediaTreeNodeRenameUrl = $this->getUrlToModuleFunction('renameMediaTreeNode');
        $configurationUrls->mediaTreeNodeDeleteUrl = $this->getUrlToModuleFunction('deleteMediaTreeNode');
        $configurationUrls->mediaTreeNodeMoveUrl = $this->getUrlToModuleFunction('moveMediaTreeNode');
        $configurationUrls->mediaItemDeleteConfirmationUrl = $this->getUrlToModuleFunction('confirmDeleteMediaItem');
        $configurationUrls->mediaItemDeleteUrl = $this->getUrlToModuleFunction('deleteMediaItem');
        $configurationUrls->imagesMoveUrl = $this->getUrlToModuleFunction('moveImages');
        $configurationUrls->quickEditUrl = $this->getUrlToModuleFunction('quickEdit');
        $configurationUrls->mediaItemDetailsUrlTemplate = $this->getUrlToModuleFunction(
            'renderDetail',
            [self::MEDIA_ITEM_URL_NAME => self::URL_TEMPLATE_PLACEHOLDER_ID]
        );

        $universalUploaderBaseParameters = [
            'pagedef' => 'CMSUniversalUploader',
            'mode' => 'media',
        ];
        $parametersUploadMedia = array_merge(
            $universalUploaderBaseParameters,
            ['treeNodeID' => self::URL_TEMPLATE_PLACEHOLDER_ID, 'queueCompleteCallback' => 'queueCompleteCallback']
        );
        $parametersReplaceMediaItem = array_merge(
            $universalUploaderBaseParameters,
            [
                'callback' => 'reloadMediaItemDetail',
                'singleMode' => '1',
                'showMetaFields' => '0',
                'recordID' => self::URL_TEMPLATE_PLACEHOLDER_ID,
            ]
        );

        $configurationUrls->uploaderUrlTemplate = URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl(
            $parametersUploadMedia,
            '?',
            '&'
        );
        $configurationUrls->uploaderReplaceMediaItemUrl = URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl(
            $parametersReplaceMediaItem,
            '?',
            '&'
        );
        $configurationUrls->autoCompleteSearchUrl = $this->getUrlToModuleFunction('autoCompleteSearch');
        $configurationUrls->postSelectUrl = $this->getUrlToModuleFunction('postSelectHook');
        $configurationUrls->mediaItemFindUsagesUrl = $this->getUrlToModuleFunction('ajaxMediaItemFindUsages');


        return $configurationUrls;
    }

    /**
     * @param string $functionName
     *
     * @return string
     */
    private function getUrlToModuleFunction($functionName, array $additionalParameters = [])
    {
        $parameters = [
            'pagedef' => self::PAGEDEF_NAME,
            '_pagedefType' => self::PAGEDEF_TYPE,
            'module_fnc' => ['contentmodule' => $functionName],
        ];

        $parameters = array_merge($parameters, $additionalParameters);

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function getMediaTreeNodeEditUrl($id)
    {
        $parameters = [
            'pagedef' => 'tableeditorPopup',
            '_pagedefType' => 'Core',
            'tableid' => \TTools::GetCMSTableId('cms_media_tree'),
            'id' => $id,
        ];

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * @return AccessRightsModel
     */
    private function createMediaAccessRightsModel()
    {
        return $this->createAccessRightsModel('cms_media');
    }

    /**
     * @return JavascriptPluginConfigurationState
     */
    private function createJavascriptPluginStateConfiguration()
    {
        $listState = $this->getListState();

        $configurationState = new JavascriptPluginConfigurationState();

        $configurationState->mediaTreeNodeId = $listState->getMediaTreeNodeId();
        $configurationState->pageNumber = $listState->getPageNumber();
        $configurationState->pageSize = $listState->getPageSize();
        $configurationState->searchTerm = $listState->getSearchTerm();
        $configurationState->listView = $listState->getListView();
        $configurationState->showSubtree = $listState->isShowSubtree();
        $configurationState->deleteWithUsageSearch = $listState->isDeleteWithUsageSearch();
        $configurationState->sortColumn = $listState->getSortColumn();

        $configurationState->pickImageMode = $listState->isPickImageMode();
        if (true === $configurationState->pickImageMode) {
            $configurationState->pickImageCallback = $listState->getPickImageCallback();
            $configurationState->parentIFrame = $listState->getParentIFrame();
            $configurationState->pickImageWithCrop = $listState->isPickImageWithCrop();
        }

        return $configurationState;
    }

    /**
     * @return MediaManagerListState
     */
    private function getListState()
    {
        return $this->mediaManagerListStateService->getListState();
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = sprintf(
            '<link rel="stylesheet" href="%s">',
            \TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.17/themes/default/style.min.css')
        );
        $includes[] = sprintf(
            '<link rel="stylesheet" href="%s">',
            \TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css')
        );
        $includes[] = sprintf(
            '<link rel="stylesheet" href="%s">',
            \TGlobal::GetStaticURL('/bundles/chameleonsystemmediamanager/css/mediaManager.css')
        );

        // part to fix wysiwyg handling
        $ckEditorReference = $this->inputFilterUtil->getFilteredGetInput('CKEditorFuncNum');
        if (null !== $ckEditorReference) {
            $includes[] = '<script src="'.URL_CMS.'/javascript/wysiwygImage.js" type="text/javascript"></script>';
            $includes[] = sprintf(
                '<script type="text/javascript">setCKEditorFuncNum("%s");</script>',
                \TGlobal::OutJS($ckEditorReference)
            );
        }

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();
        $includes[] = '<script src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemcore/javascript/jsTree/3.3.17/jstree.min.js'
        ).'"></script>';
        $includes[] = '<script src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemmediamanager/lib/xselectable/xselectable.js'
        ).'"></script>';
        $includes[] = '<script src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemmediamanager/lib/Split.js/split.js'
        ).'?v=2"></script>';
        $includes[] = '<script src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemmediamanager/lib/Jeditable/jquery.jeditable.js'
        ).'"></script>';
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';
        $includes[] = '<script src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemmediamanager/js/mediaManager.js?v='.microtime(true)
        ).'"></script>';

        return $includes;
    }

    public function ajaxMediaItemFindUsages(){
        $mediaItemId = $this->inputFilterUtil->getFilteredInput(self::MEDIA_ITEM_URL_NAME);

        $mediaItem = $this->mediaItemDataAccess->getMediaItem($mediaItemId, null);

        $usagesObjectList = $this->mediaItemChainUsageFinder->findUsages($mediaItem);

        $viewRenderer = $this->createViewRendererInstance();
        $viewRenderer->AddSourceObject('usages', $usagesObjectList);

        $listReturn = new JavascriptPluginRenderedContent();
        $listReturn->contentHtml = $viewRenderer->Render('mediaManager/usages/ajax-usages.html.twig');

        $this->returnAsAjaxResponse($listReturn);
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'renderList';
        $this->methodCallAllowed[] = 'renderDetail';
        $this->methodCallAllowed[] = 'postSelectHook';
        $this->methodCallAllowed[] = 'provideMediaTreeNodeInfo';
        $this->methodCallAllowed[] = 'autoCompleteSearch';
        $this->methodCallAllowed[] = 'renameMediaTreeNode';
        $this->methodCallAllowed[] = 'moveImages';
        $this->methodCallAllowed[] = 'quickEdit';
        $this->methodCallAllowed[] = 'deleteMediaItem';
        $this->methodCallAllowed[] = 'confirmDeleteMediaItem';
        $this->methodCallAllowed[] = 'moveMediaTreeNode';
        $this->methodCallAllowed[] = 'insertMediaTreeNode';
        $this->methodCallAllowed[] = 'deleteMediaTreeNode';
        $this->methodCallAllowed[] = 'ajaxMediaItemFindUsages';
    }

    /**
     * renders list view based on list state.
     *
     * @return void
     *
     * @throws \LogicException
     * @throws \MapperException
     * @throws \TPkgSnippetRenderer_SnippetRenderingException
     */
    protected function renderList()
    {
        $accessRightsMedia = $this->createMediaAccessRightsModel();
        if (false === $accessRightsMedia->show) {
            $this->logAndReturnError('MediaManagerBackendModule: No show rights');
        }

        $listState = $this->getListState();

        $viewRenderer = $this->createViewRendererInstance();
        $viewRenderer->AddSourceObject('listState', $listState);

        $mediaTreeNode = $this->getMediaTreeNodeFromListState($listState);
        $viewRenderer->AddSourceObject('mediaTreeNode', $mediaTreeNode);
        if (null !== $mediaTreeNode) {
            $viewRenderer->AddSourceObject(
                'mediaTreeNodeEditUrl',
                $this->getMediaTreeNodeEditUrl($mediaTreeNode->getId())
            );
        }
        $viewRenderer->AddSourceObject('accessRightsMediaTree', $this->createMediaTreeAccessRightsModel());
        $viewRenderer->AddSourceObject('accessRightsMedia', $accessRightsMedia);
        $viewRenderer->AddSourceObject('language', \TdbCmsLanguage::GetNewInstance($this->backendSession->getCurrentEditLanguageId()));

        foreach ($this->getListMappers() as $mapperServiceId) {
            $viewRenderer->addMapperFromIdentifier($mapperServiceId);
        }

        if ('tableList' === $listState->getListView()) {
            $parameters = [
                'pagedef' => 'mediaManagerLegacyList',
                '_pagedefType' => self::PAGEDEF_TYPE,
                'cms_media_tree_id' => $listState->getMediaTreeNodeId(),
            ];

            $viewRenderer->AddSourceObject(
                'tableSrc',
                URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&')
            );
        }

        $listReturn = new JavascriptPluginRenderedContent();
        $listReturn->contentHtml = $viewRenderer->Render('mediaManager/list/list.html.twig');

        $this->returnAsAjaxResponse($listReturn);
    }

    /**
     * @return void
     */
    private function logAndReturnError(?string $logMessage = null, ?\Throwable $exception = null)
    {
        $this->logger->error($logMessage ?? 'A media error occured', ['exception' => $exception]);

        $return = new JavascriptPluginMessage();
        $return->message = $this->translator->trans(
            'chameleon_system_media_manager.general_error_message',
            [],
            TranslationConstants::DOMAIN_BACKEND
        );
        $this->returnAsAjaxError($return);
    }

    /**
     * @param mixed $object - must be JSON encodable
     *
     * @return never
     */
    private function returnAsAjaxError($object)
    {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode($object);
        exit;
    }

    private function createViewRendererInstance(): \ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    private function getMediaTreeNodeFromListState(MediaManagerListState $listState): ?MediaTreeNodeDataModel
    {
        $listRequest = $this->mediaManagerListRequestService->createListRequestFromListState(
            $listState,
            $this->backendSession->getCurrentEditLanguageId()
        );

        return $listRequest->getMediaTreeNode();
    }

    /**
     * An array of mapper service ids for detail view.
     *
     * @return array
     */
    protected function getListMappers()
    {
        return [
            'chameleon_system_media_manager.backend_module_mapper.list_result',
            'chameleon_system_media_manager.backend_module_mapper.list_sort',
            'chameleon_system_media_manager.backend_module_mapper.page_size',
            'chameleon_system_media_manager.backend_module_mapper.pick_images',
        ];
    }

    /**
     * @param mixed $object - Must be json serializable
     *
     * @return never
     */
    private function returnAsAjaxResponse($object)
    {
        header('HTTP/1.1 200 OK');
        echo json_encode($object);
        exit;
    }

    /**
     * renders detail view.
     *
     * @return void
     *
     * @throws \MapperException
     * @throws \TPkgSnippetRenderer_SnippetRenderingException
     * @throws \LogicException
     */
    protected function renderDetail()
    {
        $mediaItemId = $this->inputFilterUtil->getFilteredInput(self::MEDIA_ITEM_URL_NAME);
        try {
            $mediaItem = $this->mediaItemDataAccess->getMediaItem(
                $mediaItemId,
                $this->backendSession->getCurrentEditLanguageId()
            );
            if (null === $mediaItem) {
                $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t find media item '.$mediaItemId);
            }
        } catch (DataAccessException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t find media item '.$mediaItemId, $e);
        }

        $viewRenderer = $this->createViewRendererInstance();
        $viewRenderer->AddSourceObject('language', \TdbCmsLanguage::GetNewInstance($this->backendSession->getCurrentEditLanguageId()));
        $viewRenderer->AddSourceObject('listState', $this->getListState());
        $viewRenderer->AddSourceObject('mediaItem', $mediaItem);

        $parameters = [
            'pagedef' => 'tableeditorPopup',
            'tableid' => \TTools::GetCMSTableId('cms_media'),
            'id' => $mediaItem->getId(),
        ];
        $viewRenderer->AddSourceObject(
            'tableEditorIframeUrl',
            URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&')
        );

        $viewRenderer->AddSourceObject('accessRightsMedia', $this->createMediaAccessRightsModel());

        $extensions = $this->mediaManagerExtensionCollection->getExtensions();
        $additionalButtonTemplates = [];
        $additionalDetailViewTemplates = [];
        foreach ($extensions as $extension) {
            $additionalButtonTemplates = array_merge(
                $additionalButtonTemplates,
                $extension->registerAdditionalTemplatesForDetailViewButtons()
            );
            $additionalDetailViewTemplates = array_merge(
                $additionalDetailViewTemplates,
                $extension->registerAdditionalTemplatesForDetailView()
            );
        }
        $viewRenderer->AddSourceObject('additionalButtonTemplates', $additionalButtonTemplates);
        $viewRenderer->AddSourceObject('additionalDetailViewTemplates', $additionalDetailViewTemplates);

        foreach ($this->getDetailMappers() as $mapperServiceId) {
            $viewRenderer->addMapperFromIdentifier($mapperServiceId);
        }

        $detailReturn = new JavascriptPluginRenderedContent();

        $contentHtml = $viewRenderer->Render('mediaManager/detail/detail.html.twig');
        try {
            $contentHtml = $this->responseVariableReplacer->replaceVariables($contentHtml);
        } catch (TokenInjectionFailedException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t replace variables', $e);
        }
        $detailReturn->contentHtml = $contentHtml;
        $detailReturn->mediaItemName = $mediaItem->getName();

        $this->returnAsAjaxResponse($detailReturn);
    }

    /**
     * An array of mapper service ids for detail view.
     *
     * @return array
     */
    protected function getDetailMappers()
    {
        $detailMappers = [
            'chameleon_system_media_manager.backend_module_mapper.pick_images',
        ];

        $extensions = $this->mediaManagerExtensionCollection->getExtensions();
        foreach ($extensions as $extension) {
            $detailMappersFromExtension = $extension->registerDetailMappers();
            $detailMappers = array_merge($detailMappers, $detailMappersFromExtension);
        }

        return array_unique($detailMappers);
    }

    /**
     * @return void
     */
    protected function provideMediaTreeNodeInfo()
    {
        $mediaTreeNodeId = $this->inputFilterUtil->getFilteredGetInput(
            MediaManagerListState::STATE_PARAM_NAME_MEDIA_TREE_NODE_ID
        );
        if (null === $mediaTreeNodeId) {
            $this->logAndReturnError('MediaManagerBackendModule: Media tree node id is missing');
        }

        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode(
                $mediaTreeNodeId,
                $this->backendSession->getCurrentEditLanguageId()
            );
        } catch (DataAccessException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t access media tree node '.$mediaTreeNodeId);
        }
        if (null === $mediaTreeNode) {
            $this->logAndReturnError('MediaManagerBackendModule: media tree node not found '.$mediaTreeNodeId);
        }

        $info = new MediaTreeNodeJsonObject(
            $mediaTreeNode->getId(),
            $mediaTreeNode->getName(),
            $mediaTreeNode->getIconPath()
        );

        $this->returnAsAjaxResponse($info);
    }

    /**
     * @return void
     */
    protected function insertMediaTreeNode()
    {
        $mediaTreeNodeParentId = $this->inputFilterUtil->getFilteredPostInput('parentId');
        $name = $this->inputFilterUtil->getFilteredPostInput('name');
        if (null === $mediaTreeNodeParentId || null === $name) {
            $this->logAndReturnError('MediaManagerBackendModule: name or tree node parent id are missing');
        }

        $mediaTreeNode = null;
        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->insertMediaTreeNode(
                $mediaTreeNodeParentId,
                $name,
                $this->backendSession->getCurrentEditLanguageId()
            );
        } catch (DataAccessException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t insert media tree node parent '.$mediaTreeNodeParentId, $e);
        }

        $info = new MediaTreeNodeJsonObject(
            $mediaTreeNode->getId(),
            $mediaTreeNode->getName(),
            $mediaTreeNode->getIconPath()
        );

        $this->returnAsAjaxResponse($info);
    }

    /**
     * @return void
     */
    protected function renameMediaTreeNode()
    {
        $mediaTreeNodeId = $this->inputFilterUtil->getFilteredPostInput('id');
        $name = $this->inputFilterUtil->getFilteredPostInput('name');
        if (null === $mediaTreeNodeId || null === $name) {
            $this->logAndReturnError('MediaManagerBackendModule: name or media tree node id are missing');
        }

        $mediaTreeNode = null;
        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode($mediaTreeNodeId, $this->backendSession->getCurrentEditLanguageId());
            if (null === $mediaTreeNode) {
                $this->logAndReturnError('MediaManagerBackendModule: Media tree node not found '.$mediaTreeNodeId);
            }
            $this->mediaTreeDataAccess->renameMediaTreeNode($mediaTreeNode->getId(), $name, $this->backendSession->getCurrentEditLanguageId());
        } catch (DataAccessException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t rename media tree node '.$mediaTreeNodeId, $e);
        }

        $info = new MediaTreeNodeJsonObject(
            $mediaTreeNode->getId(),
            $mediaTreeNode->getName(),
            $mediaTreeNode->getIconPath()
        );

        $this->returnAsAjaxResponse($info);
    }

    /**
     * @return void
     */
    protected function deleteMediaTreeNode()
    {
        $mediaTreeNodeId = $this->inputFilterUtil->getFilteredPostInput('id');
        if (null === $mediaTreeNodeId) {
            $this->logAndReturnError('MediaManagerBackendModule: Media tree node id is missing');
        }
        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode(
                $mediaTreeNodeId,
                $this->backendSession->getCurrentEditLanguageId()
            );
            if (null === $mediaTreeNode) {
                $this->logAndReturnError('MediaManagerBackendModule: Media tree node not found '.$mediaTreeNodeId);
            }
            $this->mediaTreeDataAccess->deleteMediaTreeNode($mediaTreeNode->getId());
        } catch (DataAccessException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t delete media tree node '.$mediaTreeNodeId, $e);
        }

        $info = new MediaTreeNodeJsonObject($mediaTreeNodeId, '');
        $info->setMessage(
            $this->translator->trans(
                'chameleon_system_media_manager.delete.success_message',
                [],
                TranslationConstants::DOMAIN_BACKEND_JS
            )
        );

        $this->returnAsAjaxResponse($info);
    }

    /**
     * @return void
     */
    protected function moveMediaTreeNode()
    {
        $mediaTreeNodeId = $this->inputFilterUtil->getFilteredPostInput('id');
        $mediaTreeNodeParentId = $this->inputFilterUtil->getFilteredPostInput('parentId');
        $position = $this->inputFilterUtil->getFilteredPostInput('position');

        if (null === $mediaTreeNodeId || null === $mediaTreeNodeParentId || null === $position) {
            $this->logAndReturnError('MediaManagerBackendModule: position or media tree node id or media tree node parent id are missing');
        }

        $position = (int) $position;

        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode($mediaTreeNodeId, $this->backendSession->getCurrentEditLanguageId());
            if (null === $mediaTreeNode) {
                $this->logAndReturnError('MediaManagerBackendModule: Media tree node not found '.$mediaTreeNodeId);
            }
            $this->mediaTreeDataAccess->moveMediaTreeNode(
                $mediaTreeNodeId,
                $mediaTreeNodeParentId,
                $position,
                $this->backendSession->getCurrentEditLanguageId()
            );
        } catch (DataAccessException $e) {
            $this->logAndReturnError('Cannot move media tree node to '.$mediaTreeNodeId.' '.$mediaTreeNodeParentId, $e);
        }

        $info = new MediaTreeNodeJsonObject($mediaTreeNodeId, '');

        $this->returnAsAjaxResponse($info);
    }

    /**
     * @return void
     */
    protected function moveImages()
    {
        /** @var string|null $mediaTreeNodeId */
        $mediaTreeNodeId = $this->inputFilterUtil->getFilteredPostInput('treeId');

        /** @var string[]|null $imageIds */
        $imageIds = $this->inputFilterUtil->getFilteredPostInput('imageIds');

        if (null === $mediaTreeNodeId || false === is_array($imageIds)) {
            $this->logAndReturnError('MediaManagerBackendModule: images ids or media tree node id are missing');
        }

        try {
            $editLanguageId = $this->backendSession->getCurrentEditLanguageId();
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode($mediaTreeNodeId, $editLanguageId);
            if (null !== $mediaTreeNode) {
                foreach ($imageIds as $mediaItemId) {
                    $this->mediaItemDataAccess->setMediaTreeNodeOfMediaItem(
                        $mediaItemId,
                        $mediaTreeNodeId,
                        $editLanguageId
                    );
                }
            }
        } catch (DataAccessException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Cannot set media tree node of media item(s)', $e);
        }

        $return = new JavascriptPluginMessage();
        $return->message = $this->translator->trans(
            'chameleon_system_media_manager.messages.moved_successfully',
            [],
            TranslationConstants::DOMAIN_BACKEND
        );
        $this->returnAsAjaxResponse($return);
    }

    /**
     * @return void
     */
    protected function quickEdit()
    {
        $mediaItemId = $this->inputFilterUtil->getFilteredPostInput('mediaItemId');
        $value = $this->inputFilterUtil->getFilteredPostInput('value');
        $type = $this->inputFilterUtil->getFilteredPostInput('type');
        if (null === $mediaItemId || null === $value || null === $type) {
            $this->logAndReturnError('MediaManagerBackendModule: type or or value or media item id are missing');
        }

        try {
            $mediaItem = $this->mediaItemDataAccess->getMediaItem(
                $mediaItemId,
                $this->backendSession->getCurrentEditLanguageId()
            );
            if (null === $mediaItem) {
                $this->logAndReturnError('MediaManagerBackendModule: Media item not found '.$mediaItemId);
            }

            $languageId = $this->backendSession->getCurrentEditLanguageId();

            switch ($type) {
                case 'description':
                    $this->mediaItemDataAccess->updateDescription(
                        $mediaItemId,
                        $value,
                        $languageId
                    );
                    break;
                case 'tags':
                    $tagList = explode(',', $value);
                    array_walk(
                        $tagList,
                        function ($value) {
                            return trim($value);
                        }
                    );
                    $this->mediaItemDataAccess->updateTags(
                        $mediaItemId,
                        $tagList,
                        $languageId
                    );
                    break;
            }
        } catch (DataAccessException $e) {
            switch ($type) {
                case 'description':
                    $this->returnAsTextResponse($mediaItem->getName());
                    break;
                case 'tags':
                    $this->returnAsTextResponse(implode(', ', $mediaItem->getTags()));
                    break;
            }
        }

        $this->returnAsTextResponse($value);
    }

    /**
     * @param string|null $string
     *
     * @return never
     */
    private function returnAsTextResponse($string)
    {
        header('HTTP/1.1 200 OK');
        echo $string;
        exit;
    }

    /**
     * @return void
     */
    protected function confirmDeleteMediaItem()
    {
        $return = new JavascriptPluginRenderedContent();
        $mediaItemIds = $this->inputFilterUtil->getFilteredPostInput('id');
        $return->hasError = true;
        if (true === is_array($mediaItemIds)) {
            $return->hasError = false;
            $viewRenderer = $this->createViewRendererInstance();
            $viewRenderer->AddSourceObject('listState', $this->getListState());
            $viewRenderer->AddSourceObject('mediaItemIds', $mediaItemIds);
            $viewRenderer->AddSourceObject('language', \TdbCmsLanguage::GetNewInstance($this->backendSession->getCurrentEditLanguageId()));
            $viewRenderer->addMapperFromIdentifier(
                'chameleon_system_media_manager.backend_module_mapper.media_item_confirm_delete'
            );
            $return->contentHtml = $viewRenderer->Render('mediaManager/delete/confirmDelete.html.twig');
        }

        $this->returnAsAjaxResponse($return);
    }

    /**
     * @return void
     */
    protected function deleteMediaItem()
    {
        $return = new JavascriptPluginRenderedContent();
        $return->hasError = false;
        try {
            /** @var string[]|null $mediaItemIds */
            $mediaItemIds = $this->inputFilterUtil->getFilteredPostInput('id');

            if (true === is_array($mediaItemIds)) {
                foreach ($mediaItemIds as $mediaItemId) {
                    $this->mediaItemDataAccess->deleteMediaItem($mediaItemId);
                }
            }
        } catch (DataAccessException $e) {
            $this->logAndReturnError('MediaManagerBackendModule: Couldn\'t delete media item(s)', $e);
        }

        $this->returnAsAjaxResponse($return);
    }

    /**
     * @return void
     */
    protected function autoCompleteSearch()
    {
        $return = new JavascriptPluginRenderedContent();
        $viewRenderer = $this->createViewRendererInstance();
        $viewRenderer->AddSourceObject('searchTerm', $this->inputFilterUtil->getFilteredGetInput('term', ''));
        $viewRenderer->AddSourceObject('language', \TdbCmsLanguage::GetNewInstance($this->backendSession->getCurrentEditLanguageId()));
        $viewRenderer->addMapperFromIdentifier(
            'chameleon_system_media_manager.backend_module_mapper.search_auto_complete'
        );
        $return->contentHtml = $viewRenderer->Render('mediaManager/autocomplete/tagAutoCompleteResult.html.twig');

        $this->returnAsAjaxResponse($return);
    }

    /**
     * @return void
     */
    protected function postSelectHook()
    {
        $this->returnAsAjaxResponse(new \stdClass());
    }
}
