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
use ChameleonSystem\MediaManager\MediaManagerExtensionCollection;
use ChameleonSystem\MediaManager\MediaManagerListState;
use IMapperCacheTriggerRestricted;
use IMapperVisitorRestricted;
use LogicException;
use MapperException;
use MTPkgViewRendererAbstractModuleMapper;
use Symfony\Contracts\Translation\TranslatorInterface;
use TdbCmsUser;
use TGlobal;
use TPkgSnippetRenderer_SnippetRenderingException;
use ViewRenderer;

class MediaManagerBackendModule extends MTPkgViewRendererAbstractModuleMapper
{
    const PAGEDEF_NAME = 'mediaManager';

    const PAGEDEF_NAME_PICK_IMAGE = 'mediaManagerPickImage';

    const PAGEDEF_TYPE = '@ChameleonSystemMediaManagerBundle';

    const MEDIA_ITEM_URL_NAME = 'mediaItemId';

    const URL_TEMPLATE_PLACEHOLDER_ID = '--id--';

    /**
     * @var MediaTreeDataAccessInterface
     */
    private $mediaTreeDataAccess;

    /**
     * @var MediaItemDataAccessInterface
     */
    private $mediaItemDataAccess;

    /**
     * @var UrlUtil
     */
    private $urlUtil;

    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    /**
     * @var MediaManagerListStateServiceInterface
     */
    private $mediaManagerListStateService;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @var MediaManagerListRequestFactoryInterface
     */
    private $mediaManagerListRequestService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var MediaManagerExtensionCollection
     */
    private $mediaManagerExtensionCollection;

    /**
     * @var ResponseVariableReplacerInterface
     */
    private $responseVariableReplacer;

    public function __construct(
        MediaTreeDataAccessInterface $mediaTreeDataAccess,
        MediaItemDataAccessInterface $mediaItemDataAccess,
        UrlUtil $urlUtil,
        InputFilterUtilInterface $inputFilterUtil,
        MediaManagerListStateServiceInterface $mediaManagerListStateService,
        LanguageServiceInterface $languageService,
        MediaManagerListRequestFactoryInterface $mediaManagerListRequestService,
        TranslatorInterface $translator,
        MediaManagerExtensionCollection $mediaManagerExtensionCollection,
        ResponseVariableReplacerInterface $responseVariableReplacer
    ) {
        parent::__construct();
        $this->mediaTreeDataAccess = $mediaTreeDataAccess;
        $this->mediaItemDataAccess = $mediaItemDataAccess;
        $this->urlUtil = $urlUtil;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->mediaManagerListStateService = $mediaManagerListStateService;
        $this->languageService = $languageService;
        $this->mediaManagerListRequestService = $mediaManagerListRequestService;
        $this->translator = $translator;
        $this->mediaManagerExtensionCollection = $mediaManagerExtensionCollection;
        $this->responseVariableReplacer = $responseVariableReplacer;
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
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

        $editLanguage = $this->languageService->getActiveEditLanguage();

        return $this->mediaTreeDataAccess->getMediaTree(null !== $editLanguage ? $editLanguage->id : null);
    }

    /**
     * @return AccessRightsModel
     */
    private function createMediaTreeAccessRightsModel()
    {
        return $this->createAccessRightsModel('cms_media_tree');
    }

    /**
     * @param string $tableName
     *
     * @return AccessRightsModel
     */
    private function createAccessRightsModel($tableName)
    {
        $accessRightsModel = new AccessRightsModel();
        $backendUser = TdbCmsUser::GetActiveUser();
        if (null === $backendUser) {
            return $accessRightsModel;
        }

        $accessManager = $backendUser->oAccessManager;
        $accessRightsModel->new = $accessManager->HasNewPermission($tableName);
        $accessRightsModel->edit = $accessManager->HasEditPermission($tableName);
        $accessRightsModel->delete = $accessManager->HasDeletePermission($tableName);
        $accessRightsModel->show = $accessManager->HasShowAllPermission(
                $tableName
            ) || $accessManager->HasShowAllReadOnlyPermission($tableName);

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
        $parameters = array(
            MediaManagerListState::STATE_PARAM_NAME_MEDIA_TREE_NODE_ID => self::URL_TEMPLATE_PLACEHOLDER_ID,
        );
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
            array(self::MEDIA_ITEM_URL_NAME => self::URL_TEMPLATE_PLACEHOLDER_ID)
        );

        $universalUploaderBaseParameters = array(
            'pagedef' => 'CMSUniversalUploader',
            'mode' => 'media',
        );
        $parametersUploadMedia = array_merge(
            $universalUploaderBaseParameters,
            array('treeNodeID' => self::URL_TEMPLATE_PLACEHOLDER_ID, 'queueCompleteCallback' => 'queueCompleteCallback')
        );
        $parametersReplaceMediaItem = array_merge(
            $universalUploaderBaseParameters,
            array(
                'callback' => 'reloadMediaItemDetail',
                'singleMode' => '1',
                'showMetaFields' => '0',
                'recordID' => self::URL_TEMPLATE_PLACEHOLDER_ID,
            )
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

        return $configurationUrls;
    }

    /**
     * @param string $functionName
     * @param array  $additionalParameters
     *
     * @return string
     */
    private function getUrlToModuleFunction($functionName, array $additionalParameters = array())
    {
        $parameters = array(
            'pagedef' => self::PAGEDEF_NAME,
            '_pagedefType' => self::PAGEDEF_TYPE,
            'module_fnc' => array('contentmodule' => $functionName),
        );

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
        $parameters = array(
            'pagedef' => 'tableeditorPopup',
            '_pagedefType' => 'Core',
            'tableid' => \TTools::GetCMSTableId('cms_media_tree'),
            'id' => $id,
        );

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
            TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/themes/default/style.css')
        );
        $includes[] = sprintf(
            '<link rel="stylesheet" href="%s">',
            TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css')
        );
        $includes[] = sprintf(
            '<link rel="stylesheet" href="%s">',
            TGlobal::GetStaticURL('/bundles/chameleonsystemmediamanager/css/mediaManager.css')
        );

        //part to fix wysiwyg handling
        $ckEditorReference = $this->inputFilterUtil->getFilteredGetInput('CKEditorFuncNum');
        if (null !== $ckEditorReference) {
            $includes[] = '<script src="'.URL_CMS.'/javascript/wysiwygImage.js" type="text/javascript"></script>';
            $includes[] = sprintf(
                '<script type="text/javascript">setCKEditorFuncNum("%s");</script>',
                TGlobal::OutJS($ckEditorReference)
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
        $includes[] = '<script src="'.TGlobal::GetStaticURL(
                '/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/jstree.js'
            ).'"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURL(
                '/bundles/chameleonsystemmediamanager/lib/xselectable/xselectable.js'
            ).'"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURL(
                '/bundles/chameleonsystemmediamanager/lib/Split.js/split.js'
            ).'"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURL(
                '/bundles/chameleonsystemmediamanager/lib/Jeditable/jquery.jeditable.js'
            ).'"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURL(
                '/bundles/chameleonsystemmediamanager/js/mediaManager.js'
            ).'"></script>';

        return $includes;
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
    }

    /**
     * renders list view based on list state.
     *
     * @throws LogicException
     * @throws MapperException
     * @throws TPkgSnippetRenderer_SnippetRenderingException
     *
     * @return void
     */
    protected function renderList()
    {
        $accessRightsMedia = $this->createMediaAccessRightsModel();
        if (false === $accessRightsMedia->show) {
            $this->returnGeneralErrorMessageForAjax();
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
        $viewRenderer->AddSourceObject('language', $this->languageService->getActiveEditLanguage());

        foreach ($this->getListMappers() as $mapperServiceId) {
            $viewRenderer->addMapperFromIdentifier($mapperServiceId);
        }

        if ('tableList' === $listState->getListView()) {
            $parameters = array(
                'pagedef' => 'mediaManagerLegacyList',
                '_pagedefType' => self::PAGEDEF_TYPE,
                'cms_media_tree_id' => $listState->getMediaTreeNodeId(),
            );

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
    private function returnGeneralErrorMessageForAjax()
    {
        $return = new JavascriptPluginMessage();
        $return->message = $this->translator->trans(
            'chameleon_system_media_manager.general_error_message',
            array(),
            TranslationConstants::DOMAIN_BACKEND
        );
        $this->returnAsAjaxError($return);
    }

    /**
     * @param mixed $object - must be JSON encodable.
     *
     * @return never
     */
    private function returnAsAjaxError($object)
    {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode($object);
        exit();
    }

    /**
     * @return ViewRenderer
     */
    private function createViewRendererInstance()
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    /**
     * @param MediaManagerListState $listState
     *
     * @return MediaTreeNodeDataModel|null
     */
    private function getMediaTreeNodeFromListState(MediaManagerListState $listState)
    {
        $listRequest = $this->mediaManagerListRequestService->createListRequestFromListState(
            $listState,
            $this->languageService->getActiveEditLanguage()->id
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
        return array(
            'chameleon_system_media_manager.backend_module_mapper.list_result',
            'chameleon_system_media_manager.backend_module_mapper.list_sort',
            'chameleon_system_media_manager.backend_module_mapper.page_size',
            'chameleon_system_media_manager.backend_module_mapper.pick_images',
        );
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
        exit();
    }

    /**
     * renders detail view.
     *
     * @throws MapperException
     * @throws TPkgSnippetRenderer_SnippetRenderingException
     * @throws LogicException
     *
     * @return void
     */
    protected function renderDetail()
    {
        $mediaItemId = $this->inputFilterUtil->getFilteredInput(self::MEDIA_ITEM_URL_NAME);
        try {
            $mediaItem = $this->mediaItemDataAccess->getMediaItem(
                $mediaItemId,
                $this->languageService->getActiveEditLanguage()->id
            );
            if (null === $mediaItem) {
                $this->returnGeneralErrorMessageForAjax();
            }
        } catch (DataAccessException $e) {
            $return = [];
            $return['hasError'] = true;
            $return['errorMessage'] = $this->translator->trans(
                'chameleon_system_media_manager.general_error_message',
                array(),
                TranslationConstants::DOMAIN_BACKEND
            );
            $this->returnAsAjaxError($return);
        }

        $viewRenderer = $this->createViewRendererInstance();
        $viewRenderer->AddSourceObject('language', $this->languageService->getActiveEditLanguage());
        $viewRenderer->AddSourceObject('listState', $this->getListState());
        $viewRenderer->AddSourceObject('mediaItem', $mediaItem);

        $parameters = array(
            'pagedef' => 'tableeditorPopup',
            'tableid' => \TTools::GetCMSTableId('cms_media'),
            'id' => $mediaItem->getId(),
        );
        $viewRenderer->AddSourceObject(
            'tableEditorIframeUrl',
            URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&')
        );

        $viewRenderer->AddSourceObject('accessRightsMedia', $this->createMediaAccessRightsModel());

        $extensions = $this->mediaManagerExtensionCollection->getExtensions();
        $additionalButtonTemplates = array();
        $additionalDetailViewTemplates = array();
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
            $this->returnGeneralErrorMessageForAjax();
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
        $detailMappers = array(
            'chameleon_system_media_manager.backend_module_mapper.media_item_usages',
            'chameleon_system_media_manager.backend_module_mapper.pick_images',
        );

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
            $this->returnGeneralErrorMessageForAjax();
        }

        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode(
                $mediaTreeNodeId,
                $this->languageService->getActiveEditLanguage()->id
            );
        } catch (DataAccessException $e) {
            $this->returnGeneralErrorMessageForAjax();
        }
        if (null === $mediaTreeNode) {
            $this->returnGeneralErrorMessageForAjax();
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
            $this->returnGeneralErrorMessageForAjax();
        }

        $mediaTreeNode = null;
        $editLanguage = $this->languageService->getActiveEditLanguage();
        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->insertMediaTreeNode(
                $mediaTreeNodeParentId,
                $name,
                null === $editLanguage ? null : $editLanguage->id
            );
        } catch (DataAccessException $e) {
            $this->returnGeneralErrorMessageForAjax();
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
            $this->returnGeneralErrorMessageForAjax();
        }

        $mediaTreeNode = null;
        try {
            $editLanguage = $this->languageService->getActiveEditLanguage();
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode($mediaTreeNodeId, $editLanguage->id);
            if (null === $mediaTreeNode) {
                $this->returnGeneralErrorMessageForAjax();
            }
            $this->mediaTreeDataAccess->renameMediaTreeNode($mediaTreeNode->getId(), $name, $editLanguage->id);
        } catch (DataAccessException $e) {
            $this->returnGeneralErrorMessageForAjax();
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
            $this->returnGeneralErrorMessageForAjax();
        }
        try {
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode(
                $mediaTreeNodeId,
                $this->languageService->getActiveEditLanguage()->id
            );
            if (null === $mediaTreeNode) {
                $this->returnGeneralErrorMessageForAjax();
            }
            $this->mediaTreeDataAccess->deleteMediaTreeNode($mediaTreeNode->getId());
        } catch (DataAccessException $e) {
            $this->returnGeneralErrorMessageForAjax();
        }

        $info = new MediaTreeNodeJsonObject($mediaTreeNodeId, '');
        $info->setMessage(
            $this->translator->trans(
                'chameleon_system_media_manager.delete.success_message',
                array(),
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
        $position = (int) $this->inputFilterUtil->getFilteredPostInput('position');

        if (null === $mediaTreeNodeId || null === $mediaTreeNodeParentId || null === $position) {
            $this->returnGeneralErrorMessageForAjax();
        }

        try {
            $editLanguage = $this->languageService->getActiveEditLanguage();
            if (null === $editLanguage) {
                $this->returnGeneralErrorMessageForAjax();
            }
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode($mediaTreeNodeId, $editLanguage->id);
            if (null === $mediaTreeNode) {
                $this->returnGeneralErrorMessageForAjax();
            }
            $this->mediaTreeDataAccess->moveMediaTreeNode(
                $mediaTreeNodeId,
                $mediaTreeNodeParentId,
                $position,
                $editLanguage->id
            );
        } catch (DataAccessException $e) {
            $this->returnGeneralErrorMessageForAjax();
        }

        $info = new MediaTreeNodeJsonObject($mediaTreeNodeId, '');

        $this->returnAsAjaxResponse($info);
    }

    /**
     * @return void
     */
    protected function moveImages()
    {
        $mediaTreeNodeId = $this->inputFilterUtil->getFilteredPostInput('treeId');
        $imageIds = $this->inputFilterUtil->getFilteredPostInput('imageIds');
        if (null === $mediaTreeNodeId || false === is_array($imageIds)) {
            $this->returnGeneralErrorMessageForAjax();
        }

        try {
            $editLanguageId = $this->languageService->getActiveEditLanguage()->id;
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode($mediaTreeNodeId, $editLanguageId);
            if (null !== $mediaTreeNode) {
                /** @var $imageIds array* */
                foreach ($imageIds as $mediaItemId) {
                    $this->mediaItemDataAccess->setMediaTreeNodeOfMediaItem(
                        $mediaItemId,
                        $mediaTreeNodeId,
                        $editLanguageId
                    );
                }
            }
        } catch (DataAccessException $e) {
            $this->returnGeneralErrorMessageForAjax();
        }

        $return = new JavascriptPluginMessage();
        $return->message = $this->translator->trans(
            'chameleon_system_media_manager.messages.moved_successfully',
            array(),
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
            $this->returnGeneralErrorMessageForAjax();
        }

        try {
            $mediaItem = $this->mediaItemDataAccess->getMediaItem(
                $mediaItemId,
                $this->languageService->getActiveEditLanguage()->id
            );
            if (null === $mediaItem) {
                $this->returnGeneralErrorMessageForAjax();
            }

            $languageId = $this->languageService->getActiveEditLanguage()->id;

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
     * @param null|string $string
     *
     * @return never
     */
    private function returnAsTextResponse($string)
    {
        header('HTTP/1.1 200 OK');
        echo $string;
        exit();
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
            $viewRenderer->AddSourceObject('language', $this->languageService->getActiveEditLanguage());
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
            $mediaItemIds = $this->inputFilterUtil->getFilteredPostInput('id');
            /** @var $mediaItemIds array* */
            if (true === is_array($mediaItemIds)) {
                foreach ($mediaItemIds as $mediaItemId) {
                    $this->mediaItemDataAccess->deleteMediaItem($mediaItemId);
                }
            }
        } catch (DataAccessException $e) {
            $return->hasError = true;
            $return->errorMessage = $this->translator->trans(
                'chameleon_system_media_manager.general_error_message',
                array(),
                TranslationConstants::DOMAIN_BACKEND
            );
            $this->returnAsAjaxError($return);
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
        $viewRenderer->AddSourceObject('language', $this->languageService->getActiveEditLanguage());
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
