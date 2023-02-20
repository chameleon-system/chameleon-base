<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleListConfig {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module instance */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Teaser target page */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $targetPage = null, 
    // TCMSFieldTablefieldname
/** @var string - Sort list by */
private string $moduleListCmsfieldname = '', 
    // TCMSFieldOption
/** @var string - Order direction */
private string $sortOrderDirection = '', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Theme */
private string $subHeadline = '', 
    // TCMSFieldWYSIWYG
/** @var string - Text */
private string $description = ''  ) {}

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
    // TCMSFieldTreeNode
public function getTargetPage(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->targetPage;
}
public function setTargetPage(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $targetPage): self
{
    $this->targetPage = $targetPage;

    return $this;
}


  
    // TCMSFieldTablefieldname
public function getModuleListCmsfieldname(): string
{
    return $this->moduleListCmsfieldname;
}
public function setModuleListCmsfieldname(string $moduleListCmsfieldname): self
{
    $this->moduleListCmsfieldname = $moduleListCmsfieldname;

    return $this;
}


  
    // TCMSFieldOption
public function getSortOrderDirection(): string
{
    return $this->sortOrderDirection;
}
public function setSortOrderDirection(string $sortOrderDirection): self
{
    $this->sortOrderDirection = $sortOrderDirection;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

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
public function getSubHeadline(): string
{
    return $this->subHeadline;
}
public function setSubHeadline(string $subHeadline): self
{
    $this->subHeadline = $subHeadline;

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


  
}
