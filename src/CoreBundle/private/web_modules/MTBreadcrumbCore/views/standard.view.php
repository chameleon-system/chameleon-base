<?php
if (!is_null($data['oBreadcrumb'])) {
    $currentNode = 1;
    $numberOfNodes = $data['oBreadcrumb']->Length(); ?>
<div class="ModuleBreadcrumb">
    <ul>
        <?php
        $data['oBreadcrumb']->GoToStart();
    while ($oNode = $data['oBreadcrumb']->Next()) {
        /** @var $oNode TCMSTreeNode */
        $class = '';
        if (1 == $currentNode) {
            $class = 'class="firstNode"';
        } elseif ($currentNode == $numberOfNodes) {
            $class = 'class="lastNode"';
        } ?>
            <li <?=$class; ?>><a href="<?=$oNode->getLink(); ?>" target="<?=$oNode->GetTarget(); ?>"
                               title="<?=TGlobal::OutHTML($oNode->GetName()); ?>"><?=TGlobal::OutHTML($oNode->GetName()); ?></a>
            </li>
            <?php
            ++$currentNode;
    } ?>
    </ul>
</div>
<?php
} ?>