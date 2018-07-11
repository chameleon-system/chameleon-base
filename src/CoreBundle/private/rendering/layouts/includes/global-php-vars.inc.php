<?php
    use ChameleonSystem\CoreBundle\ServiceLocator;

$authenticityTokenManager = ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');
?>

<script language="Javascript" type="text/javascript">
    /* global Variables coming from PHP */
    var _url_user_cms_public = '<?=URL_USER_CMS_PUBLIC; ?>';
    var _url_cms = '<?=URL_CMS; ?>';
    var _cmsurl = "<?=TGlobalBase::OutHTML(URL_CMS); ?>";
    var _cms_controler = "<?=TGlobalBase::OutHTML(PATH_CMS_CONTROLLER); ?>";
    var _cmsauthenticitytoken_parameter = '<?= $authenticityTokenManager->getTokenPlaceholderAsParameter(); ?>';
</script>
