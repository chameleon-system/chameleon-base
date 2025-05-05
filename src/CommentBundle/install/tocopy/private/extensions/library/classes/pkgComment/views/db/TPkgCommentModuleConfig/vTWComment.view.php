<?php
$oUser = TdbDataExtranetUser::GetInstance();
?>
<div class="TPkgCommentModuleconfig">
    <div class="standard">
        <?php if ($oCommentList->Length() > 0) {
            ?>
        <?php
                $oCommentList->GoToStart();
            while ($oComment = $oCommentList->Next()) { /* @var $oComment TdbPkgComment */
                echo $oComment->Render('vTWComment');
            } ?>
        <?php
        } ?>
    </div>
</div>
