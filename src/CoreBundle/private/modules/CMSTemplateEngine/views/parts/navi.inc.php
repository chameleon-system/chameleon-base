<div class="btn-group">
        <?php
        $data['oMenuItems']->GoToStart();
        /** @var $oMenuItem TCMSTableEditorMenuItem */
        while ($oMenuItem = $data['oMenuItems']->Next()) {
            echo $oMenuItem->GetMenuItemHTML();
        }
        ?>
</div>