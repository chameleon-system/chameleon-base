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
            <li <?php echo $class; ?>><a href="<?php echo $oNode->getLink(); ?>" target="<?php echo $oNode->GetTarget(); ?>"
                               title="<?php echo TGlobal::OutHTML($oNode->GetName()); ?>"><?php echo TGlobal::OutHTML($oNode->GetName()); ?></a>
            </li>
            <?php
            ++$currentNode;
    } ?>
    </ul>
</div>
<?php
} ?>