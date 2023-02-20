<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsDivision {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal / website */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal / website */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Area language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - Area language */
private ?string $cmsLanguageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Background image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $backgroundImage = null,
/** @var null|string - Background image */
private ?string $backgroundImageId = null
, 
    // TCMSFieldVarchar
/** @var string - Area name */
private string $name = '', 
    // TCMSFieldNavigationTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Navigation node */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTreeIdTree = null, 
    // TCMSFieldMedia
/** @var array<string> - Images */
private array $images = [], 
    // TCMSFieldColorpicker
/** @var string - Main color */
private string $colorPrimaryHexcolor = '', 
    // TCMSFieldColorpicker
/** @var string - Secondary color */
private string $colorSecondaryHexcolor = '', 
    // TCMSFieldColorpicker
/** @var string - Tertiary color */
private string $colorTertiaryHexcolor = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldOption
/** @var string - Menu direction */
private string $menuDirection = 'Rechts', 
    // TCMSFieldText
/** @var string - Keywords */
private string $keywords = '', 
    // TCMSFieldVarchar
/** @var string - IVW code */
private string $ivwCode = '', 
    // TCMSFieldNumber
/** @var int - Stop hover menu at this level */
private int $menuStopLevel = 0  ) {}

  public function getId(): ?string
  {
    return $this->id;
  }
  public function setId(string $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function getCmsident(): ?int
  {
    return $this->cmsident;
  }
  public function setCmsident(int $cmsident): self
  {
    $this->cmsident = $cmsident;
    return $this;
  }
    // TCMSFieldLookup
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldNavigationTreeNode
public function getCmsTreeIdTree(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->cmsTreeIdTree;
}
public function setCmsTreeIdTree(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTreeIdTree): self
{
    $this->cmsTreeIdTree = $cmsTreeIdTree;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->cmsLanguage;
}
public function setCmsLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;
    $this->cmsLanguageId = $cmsLanguage?->getId();

    return $this;
}
public function getCmsLanguageId(): ?string
{
    return $this->cmsLanguageId;
}
public function setCmsLanguageId(?string $cmsLanguageId): self
{
    $this->cmsLanguageId = $cmsLanguageId;
    // todo - load new id
    //$this->cmsLanguageId = $?->getId();

    return $this;
}



  
    // TCMSFieldMedia
public function getImages(): array
{
    return $this->images;
}
public function setImages(array $images): self
{
    $this->images = $images;

    return $this;
}


  
    // TCMSFieldLookup
public function getBackgroundImage(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->backgroundImage;
}
public function setBackgroundImage(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $backgroundImage): self
{
    $this->backgroundImage = $backgroundImage;
    $this->backgroundImageId = $backgroundImage?->getId();

    return $this;
}
public function getBackgroundImageId(): ?string
{
    return $this->backgroundImageId;
}
public function setBackgroundImageId(?string $backgroundImageId): self
{
    $this->backgroundImageId = $backgroundImageId;
    // todo - load new id
    //$this->backgroundImageId = $?->getId();

    return $this;
}



  
    // TCMSFieldColorpicker
public function getColorPrimaryHexcolor(): string
{
    return $this->colorPrimaryHexcolor;
}
public function setColorPrimaryHexcolor(string $colorPrimaryHexcolor): self
{
    $this->colorPrimaryHexcolor = $colorPrimaryHexcolor;

    return $this;
}


  
    // TCMSFieldColorpicker
public function getColorSecondaryHexcolor(): string
{
    return $this->colorSecondaryHexcolor;
}
public function setColorSecondaryHexcolor(string $colorSecondaryHexcolor): self
{
    $this->colorSecondaryHexcolor = $colorSecondaryHexcolor;

    return $this;
}


  
    // TCMSFieldColorpicker
public function getColorTertiaryHexcolor(): string
{
    return $this->colorTertiaryHexcolor;
}
public function setColorTertiaryHexcolor(string $colorTertiaryHexcolor): self
{
    $this->colorTertiaryHexcolor = $colorTertiaryHexcolor;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
    // TCMSFieldOption
public function getMenuDirection(): string
{
    return $this->menuDirection;
}
public function setMenuDirection(string $menuDirection): self
{
    $this->menuDirection = $menuDirection;

    return $this;
}


  
    // TCMSFieldText
public function getKeywords(): string
{
    return $this->keywords;
}
public function setKeywords(string $keywords): self
{
    $this->keywords = $keywords;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIvwCode(): string
{
    return $this->ivwCode;
}
public function setIvwCode(string $ivwCode): self
{
    $this->ivwCode = $ivwCode;

    return $this;
}


  
    // TCMSFieldNumber
public function getMenuStopLevel(): int
{
    return $this->menuStopLevel;
}
public function setMenuStopLevel(int $menuStopLevel): self
{
    $this->menuStopLevel = $menuStopLevel;

    return $this;
}


  
}
