<div class="row button-element">
        <?php
        $data['oMenuItems']->GoToStart();
        /** @var $oMenuItem TCMSTableEditorMenuItem */
        while ($oMenuItem = $data['oMenuItems']->Next()) {
            echo '<div class="button-item col-12 col-sm-6 col-md-4 col-lg-auto">';
            echo $oMenuItem->GetMenuItemHTML();
            echo '</div>';
        }
        ?>
</div>