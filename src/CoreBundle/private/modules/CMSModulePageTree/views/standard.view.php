<?php

/**
 * @var \ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel $treeNodes

 * @var string $isInIframe
 * @var string $showAssignDialog
 * @var string $treeNodesAjaxUrl
 * @var string $openPageConnectionListUrl
 * @var string $openPageEditorUrl
 * @var string $openPageConfigEditorUrl
 * @var string $openTreeNodeEditorUrl
 * @var string $openTreeNodeEditorAddNewNodeUrl
 * @var string $deleteNodeUrl
 * @var string $assignPageUrl
 * @var string $moveNodeUrl
 * @var object $dataID
 */
$viewRenderer = new ViewRenderer();
$viewRenderer->AddSourceObject('isInIframe', $isInIframe);
$viewRenderer->AddSourceObject('showAssignDialog', $showAssignDialog);
$viewRenderer->AddSourceObject('treeNodesAjaxUrl', $treeNodesAjaxUrl);
$viewRenderer->AddSourceObject('openPageConnectionListUrl', $openPageConnectionListUrl);
$viewRenderer->AddSourceObject('openPageEditorUrl', $openPageEditorUrl);
$viewRenderer->AddSourceObject('openPageConfigEditorUrl', $openPageConfigEditorUrl);
$viewRenderer->AddSourceObject('openTreeNodeEditorUrl', $openTreeNodeEditorUrl);
$viewRenderer->AddSourceObject('openTreeNodeEditorAddNewNodeUrl', $openTreeNodeEditorAddNewNodeUrl);
$viewRenderer->AddSourceObject('deleteNodeUrl', $deleteNodeUrl);
$viewRenderer->AddSourceObject('assignPageUrl', $assignPageUrl);
$viewRenderer->AddSourceObject('moveNodeUrl', $moveNodeUrl);
$viewRenderer->AddSourceObject('pageId', $dataID);

echo $viewRenderer->Render('CMSModulePageTree/standard.html.twig');

