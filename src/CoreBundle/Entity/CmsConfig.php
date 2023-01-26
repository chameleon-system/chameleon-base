<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsConfigImagemagick[] ImageMagick settings */
    public readonly array $cmsConfigImagemagick, 
    /** Chunk size for uploader in KB */
    public readonly string $uploaderChunkSize, 
    /** Basic language (needed for field-based translations) */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $translationBaseLanguageId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage[] Supported languages (needed for field-based translations) */
    public readonly array $cmsLanguageMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsConfigParameter[] Configuration settings */
    public readonly array $cmsConfigParameter, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerBackendMessage[] System messages / error codes */
    public readonly array $cmsMessageManagerBackendMessage, 
    /** SMTP server */
    public readonly string $smtpServer, 
    /** SMTP user */
    public readonly string $smtpUser, 
    /** SMTP password */
    public readonly string $smtpPassword, 
    /** SMTP port */
    public readonly string $smtpPort, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsConfigCmsmoduleExtensions[] CMS module extensions */
    public readonly array $cmsConfigCmsmoduleExtensions, 
    /** Backend Theme */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCmsTheme $pkgCmsThemeId, 
    /** CMS themes */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsConfigThemes $cmsConfigThemesId, 
    /** Show template engine */
    public readonly bool $showTemplateEngine, 
    /** Main portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsIpWhitelist[] Permitted IPs */
    public readonly array $cmsIpWhitelist, 
    /** Maximum file size of file uploads (in KB)  */
    public readonly string $maxDocumentUploadSize, 
    /** Additional files to be deleted when clearing the cache */
    public readonly string $additionalFilesToDeleteFromCache, 
    /** CMS owner */
    public readonly string $name, 
    /** WYSIWYG editor CSS file */
    public readonly string $wysiwygeditorCssUrl, 
    /** Maximum image file size in kb */
    public readonly string $maxImageUploadSize, 
    /** Update server */
    public readonly string $updateServer, 
    /** List of search engines */
    public readonly string $botlist, 
    /** Turn off all websites */
    public readonly bool $shutdownWebsites, 
    /** Cronjobs enabled */
    public readonly bool $cronjobsEnabled, 
    /** Lines per page */
    public readonly string $entryPerPage, 
    /** Database version */
    public readonly string $databaseversion, 
    /** Build no. */
    public readonly string $build  ) {}
}