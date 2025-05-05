<?php
if (false === $isInIFrame) {
    ?>
<div class="card">
    <div class="card-header">
        <h3><?php echo TGlobal::OutHTML($title); ?></h3>
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
