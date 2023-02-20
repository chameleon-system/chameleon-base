<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStep {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Internal name */
private string $systemname = '', 
    // TCMSFieldSEOURLTitle
/** @var string - URL name */
private string $urlName = '', 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Show in navigation list */
private bool $showInNavigation = true, 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldVarchar
/** @var string - Class name */
private string $class = '', 
    // TCMSFieldOption
/** @var string - Class type */
private string $classType = 'Core', 
    // TCMSFieldVarchar
/** @var string - Class subtype */
private string $classSubtype = 'pkgShop/objects/db/TShopOrderStep', 
    // TCMSFieldVarchar
/** @var string - View to use for the step */
private string $renderViewName = '', 
    // TCMSFieldOption
/** @var string - View type */
private string $renderViewType = 'Core', 
    // TCMSFieldVarchar
/** @var string - CSS icon class inactive */
private string $cssIconClassInactive = '', 
    // TCMSFieldVarchar
/** @var string - CSS icon class active */
private string $cssIconClassActive = '', 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Use template */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $templateNodeCmsTreeId = null  ) {}

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
    // TCMSFieldVarchar
public function getSystemname(): string
{
    return $this->systemname;
}
public function setSystemname(string $systemname): self
{
    $this->systemname = $systemname;

    return $this;
}


  
    // TCMSFieldSEOURLTitle
public function getUrlName(): string
{
    return $this->urlName;
}
public function setUrlName(string $urlName): self
{
    $this->urlName = $urlName;

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


  
    // TCMSFieldBoolean
public function isShowInNavigation(): bool
{
    return $this->showInNavigation;
}
public function setShowInNavigation(bool $showInNavigation): self
{
    $this->showInNavigation = $showInNavigation;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

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


  
    // TCMSFieldVarchar
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

    return $this;
}


  
    // TCMSFieldOption
public function getClassType(): string
{
    return $this->classType;
}
public function setClassType(string $classType): self
{
    $this->classType = $classType;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassSubtype(): string
{
    return $this->classSubtype;
}
public function setClassSubtype(string $classSubtype): self
{
    $this->classSubtype = $classSubtype;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRenderViewName(): string
{
    return $this->renderViewName;
}
public function setRenderViewName(string $renderViewName): self
{
    $this->renderViewName = $renderViewName;

    return $this;
}


  
    // TCMSFieldOption
public function getRenderViewType(): string
{
    return $this->renderViewType;
}
public function setRenderViewType(string $renderViewType): self
{
    $this->renderViewType = $renderViewType;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCssIconClassInactive(): string
{
    return $this->cssIconClassInactive;
}
public function setCssIconClassInactive(string $cssIconClassInactive): self
{
    $this->cssIconClassInactive = $cssIconClassInactive;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCssIconClassActive(): string
{
    return $this->cssIconClassActive;
}
public function setCssIconClassActive(string $cssIconClassActive): self
{
    $this->cssIconClassActive = $cssIconClassActive;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getTemplateNodeCmsTreeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->templateNodeCmsTreeId;
}
public function setTemplateNodeCmsTreeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $templateNodeCmsTreeId): self
{
    $this->templateNodeCmsTreeId = $templateNodeCmsTreeId;

    return $this;
}


  
}
