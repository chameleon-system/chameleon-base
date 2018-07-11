<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td>
            <div class="tabsContainer">
                <div class="btn-group">
                        <?php
                        $data['oMenuItems']->GoToStart();
                        /** @var $oMenuItem TCMSTableEditorMenuItem */
                        while ($oMenuItem = $data['oMenuItems']->Next()) {
                            echo $oMenuItem->GetMenuItemHTML();
                        }
                        ?>
                </div>
            </div>
        </td>
    </tr>
</table>