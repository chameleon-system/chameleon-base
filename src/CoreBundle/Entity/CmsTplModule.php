<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplModule {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Description */
    public readonly string $description, 
    /** Icon */
    public readonly string $iconList, 
    /** Icon font CSS class */
    public readonly string $iconFontCssClass, 
    /** View / mapper configuration */
    public readonly string $viewMapperConfig, 
    /** Mapper chain */
    public readonly string $mapperChain, 
    /** Translations of the views */
    public readonly string $viewMapping, 
    /** Enable revision management */
    public readonly bool $revisionManagementActive, 
    /** Module contents are copied */
    public readonly bool $isCopyAllowed, 
    /** Show in template engine */
    public readonly bool $showInTemplateEngine, 
    /** Position */
    public readonly int $position, 
    /** Offer module to specific groups only */
    public readonly bool $isRestricted, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] Allow for these groups */
    public readonly array $cmsUsergroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Display in portal */
    public readonly array $cmsPortalMlt, 
    /** Module name */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf[] Connected tables */
    public readonly array $cmsTblConfMlt, 
    /** Class name / service ID */
    public readonly string $classname  ) {}
}