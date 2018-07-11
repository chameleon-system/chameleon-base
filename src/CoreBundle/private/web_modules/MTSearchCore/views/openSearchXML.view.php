<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
    <ShortName><?php echo $data['oPortal']->GetName(); ?></ShortName>
    <Description>Such-Plugin search plugin <?php echo $data['oPortal']->sqlData['meta_description']; ?></Description>
    <Tags><?php TGlobal::OutHTML(mb_substr($data['oPortal']->sqlData['meta_keywords'], 0, 255)); ?></Tags>
    <Image height="32" width="32" type="image/vnd.microsoft.icon"><?=$data['domain']; ?>/favicon.ico</Image>
    <Developer>chameleon-cms.com</Developer>
    <?php // echo "<Contact></Contact>";?>
    <Url type="text/html"
         template="<?= $data['domain']; ?><?= PATH_CUSTOMER_FRAMEWORK_CONTROLLER; ?>?pagedef=<?=$data['pageID']; ?>&amp;_fnc=RedirectSearchPage&amp;searchword={searchTerms}"/>
    <SyndicationRight>open</SyndicationRight>
    <AdultContent>false</AdultContent>
    <Language>de-de</Language>
    <OutputEncoding>UTF-8</OutputEncoding>
    <InputEncoding>UTF-8</InputEncoding>
</OpenSearchDescription>
