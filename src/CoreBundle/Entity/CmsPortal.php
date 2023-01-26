<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsPortal {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Portal title */
    public readonly string $title, 
    /** Identifier / prefix */
    public readonly string $identifier, 
    /** External portal name */
    public readonly string $externalIdentifier, 
    /** Portal language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Enable multi-language ability */
    public readonly bool $useMultilanguage, 
    /** Show untranslated links */
    public readonly bool $showNotTanslated, 
    /** Navigation start node */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $mainNodeTree, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortalNavigation[] Navigations */
    public readonly array $propertyNavigations, 
    /** Portal home page */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $homeNodeId, 
    /** Page not found */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $pageNotFoundNode, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsDivision[] Sections */
    public readonly array $cmsPortalDivisions, 
    /** Sorting */
    public readonly int $sortOrder, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortalDomains[] Domains */
    public readonly array $cmsPortalDomains, 
    /** Favicon URL */
    public readonly string $faviconUrl, 
    /** Logo */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $images, 
    /** Logo for watermarking */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $watermarkLogo, 
    /** Background image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $backgroundImage, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessage[] System messages / error codes */
    public readonly array $cmsMessageManagerMessage, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortalSystemPage[] System pages */
    public readonly array $cmsPortalSystemPage, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage[] Portal languages */
    public readonly array $cmsLanguageMlt, 
    /** Google sitemap */
    public readonly bool $useGooglesitemap, 
    /** Short description */
    public readonly string $metaDescription, 
    /** Search terms */
    public readonly string $metaKeywords, 
    /** Author */
    public readonly string $metaAuthor, 
    /** Publisher */
    public readonly string $metaPublisher, 
    /** Locale */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLocals $cmsLocalsId, 
    /** Your meta data */
    public readonly string $customMetadata, 
    /** Website presentation / theme */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCmsTheme $pkgCmsThemeId, 
    /** Action-Plugins */
    public readonly string $actionPluginList, 
    /** Google Analytics ID */
    public readonly string $googleAnalyticNumber, 
    /** etracker ID */
    public readonly string $etrackerId, 
    /** IVW ID */
    public readonly string $ivwId, 
    /** Include in search index generation */
    public readonly bool $indexSearch, 
    /** Use / instead of .html in SEO URLs */
    public readonly bool $useSlashInSeoUrls, 
    /** Deactivate portal */
    public readonly bool $deactivePortal, 
    /** WYSIWYG text editor CSS URL */
    public readonly string $wysiwygCssUrl, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsUrlAlias[] URL alias list */
    public readonly array $cmsUrlAlias, 
    /** robots.txt */
    public readonly string $robots  ) {}
}