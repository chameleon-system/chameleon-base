<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsWizardConfig {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module instance */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldVarchar
/** @var string - Title / headline */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Specifies whether the list of steps is in a package */
private bool $listIsPackage = false  ) {}

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


  
    // TCMSFieldBoolean
public function isListIsPackage(): bool
{
    return $this->listIsPackage;
}
public function setListIsPackage(bool $listIsPackage): self
{
    $this->listIsPackage = $listIsPackage;

    return $this;
}


  
}
