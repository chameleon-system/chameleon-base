<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class CmsWizardStep {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - CMS display name */
private string $displayName = '', 
    // TCMSFieldVarchar
/** @var string - Title / headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Internal name */
private string $systemname = '', 
    // TCMSFieldVarchar
/** @var string - URL name */
private string $urlName = '', 
    // TCMSFieldVarchar
/** @var string - Class name */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Class subtype */
private string $classSubtype = '', 
    // TCMSFieldVarchar
/** @var string - View to be used for the step */
private string $renderViewName = '', 
    // TCMSFieldVarchar
/** @var string - View subtype – where is the view relative to view folder */
private string $renderViewSubtype = ''  ) {}

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
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDisplayName(): string
{
    return $this->displayName;
}
public function setDisplayName(string $displayName): self
{
    $this->displayName = $displayName;

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
public function getRenderViewSubtype(): string
{
    return $this->renderViewSubtype;
}
public function setRenderViewSubtype(string $renderViewSubtype): self
{
    $this->renderViewSubtype = $renderViewSubtype;

    return $this;
}


  
}
