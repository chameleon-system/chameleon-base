<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Login</h3>
                </div>
                <div class="panel-body">
                    <form name="cmsform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
                        <input type="hidden" name="pagedef" value="login" />
                        <input type="hidden" name="module_fnc[contentmodule]" value="Login" />
                        <input type="hidden" name="redirectParams" value="<?=$redirectParams; ?>" />
                        <input type="hidden" name="login" value="<?=$login; ?>" />
                        <fieldset>
                            <?php
                            if (array_key_exists('errmsg', $data)) {
                                ?>
                                <div class="alert alert-warning">
                                    <?=$data['errmsg']; ?>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input class="form-control" placeholder="<?php echo TGlobal::Translate('chameleon_system_core.cms_module_login.form_user_name'); ?>" name="username" type="text" value="<?php if (array_key_exists('username', $data)) {
                                echo $data['username'];
                            }?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                    <input class="form-control" placeholder="<?php echo TGlobal::Translate('chameleon_system_core.cms_module_login.form_password'); ?>" name="password" type="password" value="">
                                </div>
                            </div>
                            <input class="btn btn-lg btn-success btn-block" type="submit" value="<?php echo TGlobal::Translate('chameleon_system_core.cms_module_login.action_login'); ?>">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        if (parent.frames.length > 0) {
            parent.location.href = self.document.location
        }

        document.cmsform.username.focus();
    });
</script>