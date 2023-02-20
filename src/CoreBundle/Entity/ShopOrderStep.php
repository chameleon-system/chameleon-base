<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopOrderStep {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Internal name */
private string $systemname = '', 
    // TCMSFieldVarchar
/** @var string - URL name */
private string $urlName = '', 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Class name */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Class subtype */
private string $classSubtype = 'pkgShop/objects/db/TShopOrderStep', 
    // TCMSFieldVarchar
/** @var string - View to use for the step */
private string $renderViewName = '', 
    // TCMSFieldVarchar
/** @var string - CSS icon class inactive */
private string $cssIconClassInactive = '', 
    // TCMSFieldVarchar
/** @var string - CSS icon class active */
private string $cssIconClassActive = ''  ) {}

  public function getId(): string
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


  
    // TCMSFieldVarchar
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


  
}
