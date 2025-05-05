<?php

/**
 * @var TdbCmsTplModule $module
 * @var ?\TdbCmsTplModuleInstance $moduleInstance
 * @var string $createModuleMenu
 * @var string $activePortalId
 * @var bool $hasRightToEditModules
 * @var bool $hasRightToSwitchLayouts
 * @var ?string $pagedef
 * @var ?string $id
 * @var ?string $disableLinks
 * @var ?string $disableFrontendJs
 * @var ?string $previewMode
 * @var ?string $previewLanguageId
 * @var string $cmsMasterPageDefinitionSpotTableId
 * @var string $moduleEditStateColor
 * @var string $menuPrefix
 * @var array $viewMapping
 * @var string $moduleSpotName
 * @var array $functionRights
 * @var array $_oModules
 * @var array $relatedTables
 * @var ?\Titerator $moduleViews
 * @var ?\TdbCmsMasterPagedefSpot $cmsMasterPageDefSpot
 */
$viewRenderer = new ViewRenderer();
$viewRenderer->AddSourceObject('module', $module);
$viewRenderer->AddSourceObject('moduleInstance', $moduleInstance);
$viewRenderer->AddSourceObject('createModuleMenu', $createModuleMenu);
$viewRenderer->AddSourceObject('activePortalId', $activePortalId);
$viewRenderer->AddSourceObject('hasRightToEditModules', $hasRightToEditModules);
$viewRenderer->AddSourceObject('hasRightToSwitchLayouts', $hasRightToSwitchLayouts);
$viewRenderer->AddSourceObject('pagedef', $pagedef);
$viewRenderer->AddSourceObject('id', $id);
$viewRenderer->AddSourceObject('disableLinks', $disableLinks);
$viewRenderer->AddSourceObject('disableFrontendJs', $disableFrontendJs);
$viewRenderer->AddSourceObject('previewMode', $previewMode);
$viewRenderer->AddSourceObject('previewLanguageId', $previewLanguageId);
$viewRenderer->AddSourceObject('cmsMasterPageDefinitionSpotTableId', $cmsMasterPageDefinitionSpotTableId);
$viewRenderer->AddSourceObject('cmsMasterPageDefSpot', $cmsMasterPageDefSpot);
$viewRenderer->AddSourceObject('moduleEditStateColor', $moduleEditStateColor);
$viewRenderer->AddSourceObject('menuPrefix', $menuPrefix);
$viewRenderer->AddSourceObject('viewMapping', $viewMapping);
$viewRenderer->AddSourceObject('moduleSpotName', $moduleSpotName);
$viewRenderer->AddSourceObject('functionRights', $functionRights);
$viewRenderer->AddSourceObject('fullModuleList', $_oModules);
$viewRenderer->AddSourceObject('relatedTables', $relatedTables); // change var later
$viewRenderer->AddSourceObject('path_cms_controller_frontend', PATH_CMS_CONTROLLER_FRONTEND);
$viewRenderer->AddSourceObject('path_cms_controller', PATH_CMS_CONTROLLER);

echo $viewRenderer->Render('CMSModuleChooser/standard.html.twig');
