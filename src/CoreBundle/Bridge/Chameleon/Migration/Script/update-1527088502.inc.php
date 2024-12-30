<h1>pkgnewsletter - Build #1527088502</h1>
<div class="changelog">
    - Adjust snippet chain paths to new Chameleon bundle structure.
</div>
<?php

$bundles = [
    'core' => 'CoreBundle',
    'pkgatomiclock' => 'AtomicLockBundle',
    'pkgcmsactionplugin' => 'CmsActionPluginBundle',
    'pkgcmscache' => 'CmsCacheBundle',
    'pkgcmscaptcha' => 'CmsCaptchaBundle',
    'pkgcmschangelog' => 'CmsChangeLogBundle',
    'pkgcmsclassmanager' => 'CmsClassManagerBundle',
    'pkgcmscorelog' => 'CmsCoreLogBundle',
    'pkgcmscounter' => 'CmsCounterBundle',
    'pkgcmsevent' => 'CmsEventBundle',
    'pkgcmsfilemanager' => 'CmsFileManagerBundle',
    'pkgcmsinterfacemanager' => 'CmsInterfaceManagerBundle',
    'pkgcmsnavigation' => 'CmsNavigationBundle',
    'pkgcmsresultcache' => 'CmsResultCacheBundle',
    'pkgcmsrouting' => 'CmsRoutingBundle',
    'pkgcmsstringutilities' => 'CmsStringUtilitiesBundle',
    'pkgcmstextblock' => 'CmsTextBlockBundle',
    'pkgcmstextfield' => 'CmsTextFieldBundle',
    'pkgcomment' => 'CommentBundle',
    'pkgcore' => 'PkgCoreBundle',
    'pkgcorevalidatorconstraints' => 'CoreValidatorConstraintsBundle',
    'pkgcsv2sql' => 'Csv2SqlBundle',
    'pkgexternaltracker' => 'ExternalTrackerBundle',
    'pkgexternaltrackergoogleanalytics' => 'ExternalTrackerGoogleAnalyticsBundle',
    'pkgextranet' => 'ExtranetBundle',
    'pkggenerictableexport' => 'GenericTableExportBundle',
    'pkgmultimodule' => 'MultiModuleBundle',
    'pkgnewsletter' => 'NewsletterBundle',
    'pkgrevisionmanagement' => 'RevisionManagementBundle',
    'pkgsnippetrenderer' => 'SnippetRendererBundle',
    'pkgtrackviews' => 'TrackViewsBundle',
    'pkgurlalias' => 'UrlAliasBundle',
    'pkgviewrenderer' => 'ViewRendererBundle',

    'pkgcmsnavigationpkgshop' => 'CmsNavigationPkgShopBundle',
    'pkgextranetregistrationguest' => 'ExtranetRegistrationGuestBundle',
    'pkgimagehotspot' => 'ImageHotspotBundle',
    'pkgsearch' => 'SearchBundle',
    'pkgshop' => 'ShopBundle',
    'pkgshopaffiliate' => 'ShopAffiliateBundle',
    'pkgshoparticledetailpaging' => 'ShopArticleDetailPagingBundle',
    'pkgshoparticlepreorder' => 'ShopArticlePreorderBundle',
    'pkgshoparticlereview' => 'ShopArticleReviewBundle',
    'pkgshopcurrency' => 'ShopCurrencyBundle',
    'pkgshopdhlpackstation' => 'ShopDhlPackstationBundle',
    'pkgshoplistfilter' => 'ShopListFilterBundle',
    'pkgshopnewslettersignupwithorder' => 'ShopNewsletterSignupWithOrderBundle',
    'pkgshoporderstatus' => 'ShopOrderStatusBundle',
    'pkgshoporderviaphone' => 'ShopOrderViaPhoneBundle',
    'pkgshoppaymentipn' => 'ShopPaymentIPNBundle',
    'pkgshoppaymenttransaction' => 'ShopPaymentTransactionBundle',
    'pkgshopprimarynavigation' => 'ShopPrimaryNavigationBundle',
    'pkgshopproductexport' => 'ShopProductExportBundle',
    'pkgshopratingservice' => 'ShopRatingServiceBundle',
    'pkgshopwishlist' => 'ShopWishlistBundle',
    'pkgtshoppaymenthandlersofortueberweisung' => 'ShopPaymentHandlerSofortueberweisungBundle',
];

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$statement = $databaseConnection->executeQuery('SELECT `id`, `snippet_chain` FROM `pkg_cms_theme`');
if (false === $statement->execute()) {
    return;
}

while (false !== $row = $statement->fetch()) {
    $id = $row['id'];
    $snippetChain = \explode("\n", $row['snippet_chain']);
    $isChanged = false;

    foreach ($snippetChain as $index => $entry) {
        if (1 !== \preg_match('#^../../../../vendor/chameleon-system/(\w+)(/|$)#', $entry, $matches)) {
            continue;
        }
        $packageName = $matches[1];
        if (false === isset($bundles[$packageName])) {
            continue;
        }
        $isChanged = true;
        $bundleName = $bundles[$packageName];
        $snippetChain[$index] = \str_replace("../../../../vendor/chameleon-system/$packageName", "@ChameleonSystem$bundleName", $entry);
    }
    if (false === $isChanged) {
        continue;
    }
    $databaseConnection->executeQuery('UPDATE `pkg_cms_theme` SET `snippet_chain` = ? WHERE `id` = ?', [
            implode("\n", $snippetChain),
            $id,
    ]);
}
