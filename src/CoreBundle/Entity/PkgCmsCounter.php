<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsCounter {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldExtendedLookupMultiTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfig|\ChameleonSystem\CoreBundle\Entity\CmsPortal|\ChameleonSystem\CoreBundle\Entity\Shop|null - Owner */
private \ChameleonSystem\CoreBundle\Entity\CmsConfig|\ChameleonSystem\CoreBundle\Entity\CmsPortal|\ChameleonSystem\CoreBundle\Entity\Shop|null $owner = null,
// TCMSFieldExtendedLookupMultiTable
/** @var string - Owner */
private string $ownerTable = '', 
    // TCMSFieldNumber
/** @var int - Value */
private int $value = 0  ) {}

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
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
    // TCMSFieldExtendedLookupMultiTable
public function getOwner(): \ChameleonSystem\CoreBundle\Entity\CmsConfig|\ChameleonSystem\CoreBundle\Entity\CmsPortal|\ChameleonSystem\CoreBundle\Entity\Shop|null
{
    return $this->owner;
}
public function setOwner(\ChameleonSystem\CoreBundle\Entity\CmsConfig|\ChameleonSystem\CoreBundle\Entity\CmsPortal|\ChameleonSystem\CoreBundle\Entity\Shop|null $owner): self
{
    $this->owner = $owner;

    return $this;
}

// TCMSFieldExtendedLookupMultiTable
public function getOwnerTable(): string
{
    return $this->ownerTable;
}
public function setOwnerTable(string $ownerTable): self
{
    $this->ownerTable = $ownerTable;

    return $this;
}


  
    // TCMSFieldNumber
public function getValue(): int
{
    return $this->value;
}
public function setValue(int $value): self
{
    $this->value = $value;

    return $this;
}


  
}
