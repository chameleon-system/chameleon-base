<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-4 lg-offset-4 mt-5">
            <div class="card bg-light">
                <div class="card-header">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <form name="cmsform" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
                        <input type="hidden" name="pagedef" value="login" />
                        <input type="hidden" name="module_fnc[contentmodule]" value="Login" />
                        <input type="hidden" name="redirectParams" value="<?php echo $redirectParams; ?>" />
                        <fieldset>
                            <?php
                            if (array_key_exists('errmsg', $data)) {
                                ?>
                                <div class="alert alert-warning">
                                    <?php echo $data['errmsg']; ?>
                                </div>
                            <?php
                            }
                    ?>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                    <input class="form-control" placeholder="<?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_login.form_user_name'); ?>" name="username" type="text" value="<?php if (array_key_exists('username', $data)) {
                                        echo $data['username'];
                                    }?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-unlock-alt"></i></span></div>
                                    <input class="form-control" placeholder="<?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_login.form_password'); ?>" name="password" type="password" value="">
                                </div>
                            </div>
                            <input class="btn btn-lg btn-success btn-block" type="submit" value="<?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_login.action_login'); ?>">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        if (parent.frames.length > 0) {
            parent.location.href = self.document.location
        }

        document.cmsform.username.focus();
    });
</script>
