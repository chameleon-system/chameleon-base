<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsDivision {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to portal / website */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Area name */
    public readonly string $name, 
    /** Navigation node */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $cmsTreeIdTree, 
    /** Area language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** @var array&lt;int,string&gt; Images */
    public readonly array $images, 
    /** Background image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $backgroundImage, 
    /** Main color */
    public readonly string $colorPrimaryHexcolor, 
    /** Secondary color */
    public readonly string $colorSecondaryHexcolor, 
    /** Tertiary color */
    public readonly string $colorTertiaryHexcolor, 
    /** Position */
    public readonly int $position, 
    /** Menu direction */
    public readonly string $menuDirection, 
    /** Keywords */
    public readonly string $keywords, 
    /** IVW code */
    public readonly string $ivwCode, 
    /** Stop hover menu at this level */
    public readonly string $menuStopLevel  ) {}
}