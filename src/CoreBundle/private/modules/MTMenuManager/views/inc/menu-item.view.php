<?php
while ($oMenu = $data[$menuColName]->Next()) {
    /** @var $oMenu TCMSContentBoxItem */
    $oMenu->loadMenuItems();
    if ($oMenu->oMenuItems->Length() > 0) {
        ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><?= TGlobal::OutHTML($oMenu->GetName()); ?></h5>
            </div>
            <div class="card-body p-0">
                <nav class="nav flex-column">
                    <?php
                    $count = 0;
        while ($oMenuItem = $oMenu->oMenuItems->Next()) {
            ++$count;

            $rowClass = 'bg-light';
            if ($count % 2) {
                $rowClass = '';
            }

            echo '<div class="pl-2 '.$rowClass.'">';
            /** @var $oMenuItem TCMSMenuItem */
            echo $oMenuItem->GetLink();
            echo '</div>';
        } ?>
                </nav>
            </div>
        </div>
        <?php
    }
}
