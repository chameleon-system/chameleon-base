<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSystemInfoModuleConfig {
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
/** @var string - Optional title */
private string $name = '', 
    // TCMSFieldWYSIWYG
/** @var string - Optional introduction text */
private string $intro = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSystemInfo[] - Shop info pages to be displayed */
private \Doctrine\Common\Collections\Collection $shopSystemInfoMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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


  
    // TCMSFieldWYSIWYG
public function getIntro(): string
{
    return $this->intro;
}
public function setIntro(string $intro): self
{
    $this->intro = $intro;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getShopSystemInfoMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSystemInfoMlt;
}
public function setShopSystemInfoMlt(\Doctrine\Common\Collections\Collection $shopSystemInfoMlt): self
{
    $this->shopSystemInfoMlt = $shopSystemInfoMlt;

    return $this;
}


  
}
