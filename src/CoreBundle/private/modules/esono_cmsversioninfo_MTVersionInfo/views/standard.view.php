<?php
/**
 * @deprecated since 6.3.0
 *
 * @var $updates array
 * @var $deploys array
 * @var $logEntryList array
 * @var $logEntry \esono\cmsversioninfo\Commit
 */
?>

<form method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8" style="float:left;">
    <input type="hidden" name="pagedef" value="<?=TGlobal::OutHTML($data['pagedef']); ?>"/>
    <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value="refresh"/>
    <input type="submit" value="Refresh" />
</form>
<form method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8" style="float:right;">
    <input type="hidden" name="pagedef" value="<?=TGlobal::OutHTML($data['pagedef']); ?>"/>
    <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value="stepBack"/>
    <input type="submit" value="Step back in history" title="(undo last mark as read - history stays the same)" />
</form>

<div style="clear: both"></div>
    <h1>Updates since last refresh</h1>
    <div class="">
        <div class="contentcontainer">
            <?php
             foreach ($updates as $name => $logEntryList) {
                 echo "<div class=\"header\">$name</div>";
                 echo '<div class="content">';
                 $c = 0;

                 foreach ($logEntryList as $logEntry) {
                     $class = 0 === $c++ % 2 ? 'evenrow' : 'oddrow';
                     echo '<div class="'.$class.'">';
                     echo '<img src="/chameleon/blackbox//images/icons/folder_edit.png" />&nbsp;&nbsp;&nbsp;';
                     echo \esono\cmsversioninfo\MTVersionInfo::getHTMLVersion($logEntry); ?>

                     <?php
                     echo '</div>';
                 }
                 echo '</div>';
             }

            ?>
        </div>
    </div>
</form>