<?php
if (false === $isInIFrame) {
    ?>
<div class="card">
    <div class="card-header">
        <h3><?=TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_header.action_help')); ?></h3>
    </div>
    <div class="card-body">
<?php
echo $data['sHtml']; ?>
    </div>
</div>
<?php
} else {
        echo $data['sHtml'];
    }
