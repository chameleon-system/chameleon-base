<?php $rowStyle = array('oddrow', 'evenrow'); ?>
<div id="cmsmainmenu">
    <div id="cmsleftmenu">
        <div class="columnpadding">
            <?php
            while ($oMenu = $data['oLeftMenu']->Next()) {
                /** @var $oMenu TCMSContentBoxItem */
                $oMenu->loadMenuItems();
                if ($oMenu->oMenuItems->Length() > 0) {
                    ?>
                    <div class="cmsmainmenubox cmsBoxBorder">
                    <?php
                    $rowCount = 0;
                    $oMenu->DrawBoxHeader();

                    while ($oMenuItem = $oMenu->oMenuItems->Next()) {
                        /** @var $oMenuItem TCMSMenuItem */
                        ++$rowCount;
                        $style = $rowStyle[$rowCount % 2];
                        echo "<div class=\"{$style}\">".$oMenuItem->GetLink()."</div>\n";
                    }

                    $oMenu->DrawBoxFooter(); ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <div id="cmsmiddlemenu">
        <div class="columnpadding">
            <?php
            while ($oMenu = $data['oMiddleMenu']->Next()) {
                /** @var $oMenu TCMSContentBoxItem */
                $oMenu->loadMenuItems();
                if ($oMenu->oMenuItems->Length() > 0) {
                    ?>
                    <div class="cmsmainmenubox cmsBoxBorder">
                    <?php
                    $rowCount = 0;
                    $oMenu->DrawBoxHeader();
                    while ($oMenuItem = $oMenu->oMenuItems->Next()) {
                        /** @var $oMenuItem TCMSMenuItem */
                        ++$rowCount;
                        $style = $rowStyle[$rowCount % 2];
                        echo "<div class=\"{$style}\">".$oMenuItem->GetLink()."</div>\n";
                    }
                    $oMenu->DrawBoxFooter();
                    echo "<br />\n"; ?>
                    </div>
                    <?php
                }
            }
            ?>

        </div>
    </div>
    <div id="cmsrightmenu">
        <div class="columnpadding">
            <?php
            while ($oMenu = $data['oRightMenu']->Next()) {
                /** @var $oMenu TCMSContentBoxItem */
                $oMenu->loadMenuItems();
                if ($oMenu->oMenuItems->Length() > 0) {
                    ?>
                    <div class="cmsmainmenubox cmsBoxBorder">
                    <?php
                    $rowCount = 0;
                    $oMenu->DrawBoxHeader();
                    while ($oMenuItem = $oMenu->oMenuItems->Next()) {
                        /** @var $oMenuItem TCMSMenuItem */
                        ++$rowCount;
                        $style = $rowStyle[$rowCount % 2];
                        echo "<div class=\"{$style}\">".$oMenuItem->GetLink()."</div>\n";
                    }
                    $oMenu->DrawBoxFooter();
                    echo "<br />\n"; ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>
