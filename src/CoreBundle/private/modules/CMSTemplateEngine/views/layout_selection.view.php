<!-- layout selection main view -->
<?php require __DIR__.'/parts/navi.inc.php'; ?>
<div>
    <?php
    require __DIR__.'/parts/header.inc.php';
    ?>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
        <tr>
            <td width="300" style="vertical-align: top">
                <iframe name="layoutliste" id="layoutliste" frameborder="0" width="300" style="min-height: 500px" height="100%"
                        src="<?=PATH_CMS_CONTROLLER; ?>?pagedef=templateengineplain&_mode=layoutlist&id=<?=$data['id']; ?>"></iframe>
            </td>
            <td width="*">
                <?php $src = isset($data['sActualMasterLayout']) ? " src=\"{$data['sActualMasterLayout']}\"" : ''; ?>
                <iframe name="layoutpreview" id="userwebpageiframe" frameborder="0" style="min-height: 500px" width="100%"
                        height="100%"<?= $src; ?>></iframe>
            </td>
        </tr>
    </table>
</div>
