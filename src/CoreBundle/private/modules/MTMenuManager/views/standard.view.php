<?php
/**
 * @deprecated since 6.3.0 - classic main menu will be removed in a future Chameleon release
 */
?>
<div class="row">

    <div class="col-sm">
        <?php
        $menuColName = 'oLeftMenu';
        require 'inc/menu-item.view.php';
        ?>
    </div>
    <div class="col-sm">
        <?php
        $menuColName = 'oMiddleMenu';
        require 'inc/menu-item.view.php';
        ?>
    </div>
    <div class="col-sm">
        <?php
        $menuColName = 'oRightMenu';
        require 'inc/menu-item.view.php';
        ?>
    </div>
</div>
