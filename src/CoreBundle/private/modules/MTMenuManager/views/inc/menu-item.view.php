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
            <div class="card-body p-2">
                <nav class="nav flex-column">
                    <?php
                    while ($oMenuItem = $oMenu->oMenuItems->Next()) {
                        /** @var $oMenuItem TCMSMenuItem */
                        echo $oMenuItem->GetLink();
                    } ?>
                </nav>
            </div>
        </div>
        <?php
    }
}
