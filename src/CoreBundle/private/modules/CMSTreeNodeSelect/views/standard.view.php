<?php
/**
 * @var \ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel $treeNodes
 * @var string $nodeId
 * @var string $fieldName
 * @var string $treePathHTML
 */
$viewRenderer = new ViewRenderer();
$viewRenderer->AddSourceObject('treeNodes', $treeNodes);
$viewRenderer->AddSourceObject('activeId', $nodeId);
$viewRenderer->AddSourceObject('fieldName', $fieldName);
$viewRenderer->AddSourceObject('level', 0);
echo $viewRenderer->Render('CMSTreeNodeSelect/standard.html.twig');

echo $treePathHTML;

