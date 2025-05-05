<?php use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

require dirname(__FILE__).'/parts/navi.inc.php'; ?>

<div class="card card-accent-primary mb-2" id="templateengine">
    <div class="card-header p-1">
    <?php
    require_once dirname(__FILE__).'/../../MTTableEditor/views/includes/editorheader.inc.php';
?>
    </div>
    <div class="card-body p-0">
    <?php
/** @var BackendSessionInterface $backendSession */
$backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

?>
    <iframe name="userwebpage" id="userwebpageiframe" frameborder="0"
            src="<?php echo PATH_CMS_CONTROLLER_FRONTEND; ?>?pagedef=<?php echo TGlobal::OutHTML(urlencode($data['oPage']->id)); ?>&amp;__modulechooser=true&amp;id=<?php echo TGlobal::OutHTML(urlencode($data['oPage']->id)); ?>&amp;entropy=<?php echo md5(rand()); ?>&amp;esdisablelinks=true&amp;esdisablefrontendjs=true&amp;__previewmode=true&amp;previewLanguageId=<?php echo $backendSession->getCurrentEditLanguageId(); ?>"
            width="100%" height="600"></iframe>
    </div>
</div>
