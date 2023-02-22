<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroupConfig;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus;
use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler;
use ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger;
use ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage;

class ShopPaymentHandlerGroup {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Overwrite Tdb with this class */
private string $classname = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopPaymentHandlerGroupConfig> - Configuration */
private Collection $shopPaymentHandlerGroupConfigCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - IPN Identifier */
private string $ipnGroupIdentifier = '', 
    // TCMSFieldVarchar
/** @var string - Character encoding of data transmitted by the provider */
private string $ipnPayloadCharacterCharset = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgShopPaymentIpnStatus> - IPN status codes */
private Collection $pkgShopPaymentIpnStatusCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopPaymentHandler> - Payment handler */
private Collection $shopPaymentHandlerCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopPaymentMethod> - Payment methods */
private Collection $shopPaymentMethodCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgShopPaymentIpnTrigger> - Redirections */
private Collection $pkgShopPaymentIpnTriggerCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgShopPaymentIpnMessage> - IPN messages */
private Collection $pkgShopPaymentIpnMessageCollection = new ArrayCollection()
  ) {}

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
public function getClassname(): string
{
    return $this->classname;
}
public function setClassname(string $classname): self
{
    $this->classname = $classname;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopPaymentHandlerGroupConfig>
*/
public function getShopPaymentHandlerGroupConfigCollection(): Collection
{
    return $this->shopPaymentHandlerGroupConfigCollection;
}

public function addShopPaymentHandlerGroupConfigCollection(ShopPaymentHandlerGroupConfig $shopPaymentHandlerGroupConfig): self
{
    if (!$this->shopPaymentHandlerGroupConfigCollection->contains($shopPaymentHandlerGroupConfig)) {
        $this->shopPaymentHandlerGroupConfigCollection->add($shopPaymentHandlerGroupConfig);
        $shopPaymentHandlerGroupConfig->setShopPaymentHandlerGroup($this);
    }

    return $this;
}

public function removeShopPaymentHandlerGroupConfigCollection(ShopPaymentHandlerGroupConfig $shopPaymentHandlerGroupConfig): self
{
    if ($this->shopPaymentHandlerGroupConfigCollection->removeElement($shopPaymentHandlerGroupConfig)) {
        // set the owning side to null (unless already changed)
        if ($shopPaymentHandlerGroupConfig->getShopPaymentHandlerGroup() === $this) {
            $shopPaymentHandlerGroupConfig->setShopPaymentHandlerGroup(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getIpnGroupIdentifier(): string
{
    return $this->ipnGroupIdentifier;
}
public function setIpnGroupIdentifier(string $ipnGroupIdentifier): self
{
    $this->ipnGroupIdentifier = $ipnGroupIdentifier;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIpnPayloadCharacterCharset(): string
{
    return $this->ipnPayloadCharacterCharset;
}
public function setIpnPayloadCharacterCharset(string $ipnPayloadCharacterCharset): self
{
    $this->ipnPayloadCharacterCharset = $ipnPayloadCharacterCharset;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, PkgShopPaymentIpnStatus>
*/
public function getPkgShopPaymentIpnStatusCollection(): Collection
{
    return $this->pkgShopPaymentIpnStatusCollection;
}

public function addPkgShopPaymentIpnStatusCollection(PkgShopPaymentIpnStatus $pkgShopPaymentIpnStatus): self
{
    if (!$this->pkgShopPaymentIpnStatusCollection->contains($pkgShopPaymentIpnStatus)) {
        $this->pkgShopPaymentIpnStatusCollection->add($pkgShopPaymentIpnStatus);
        $pkgShopPaymentIpnStatus->setShopPaymentHandlerGroup($this);
    }

    return $this;
}

public function removePkgShopPaymentIpnStatusCollection(PkgShopPaymentIpnStatus $pkgShopPaymentIpnStatus): self
{
    if ($this->pkgShopPaymentIpnStatusCollection->removeElement($pkgShopPaymentIpnStatus)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentIpnStatus->getShopPaymentHandlerGroup() === $this) {
            $pkgShopPaymentIpnStatus->setShopPaymentHandlerGroup(null);
        }
    }

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopPaymentHandler>
*/
public function getShopPaymentHandlerCollection(): Collection
{
    return $this->shopPaymentHandlerCollection;
}

public function addShopPaymentHandlerCollection(ShopPaymentHandler $shopPaymentHandler): self
{
    if (!$this->shopPaymentHandlerCollection->contains($shopPaymentHandler)) {
        $this->shopPaymentHandlerCollection->add($shopPaymentHandler);
        $shopPaymentHandler->setShopPaymentHandlerGroup($this);
    }

    return $this;
}

public function removeShopPaymentHandlerCollection(ShopPaymentHandler $shopPaymentHandler): self
{
    if ($this->shopPaymentHandlerCollection->removeElement($shopPaymentHandler)) {
        // set the owning side to null (unless already changed)
        if ($shopPaymentHandler->getShopPaymentHandlerGroup() === $this) {
            $shopPaymentHandler->setShopPaymentHandlerGroup(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopPaymentMethod>
*/
public function getShopPaymentMethodCollection(): Collection
{
    return $this->shopPaymentMethodCollection;
}

public function addShopPaymentMethodCollection(ShopPaymentMethod $shopPaymentMethod): self
{
    if (!$this->shopPaymentMethodCollection->contains($shopPaymentMethod)) {
        $this->shopPaymentMethodCollection->add($shopPaymentMethod);
        $shopPaymentMethod->setShopPaymentHandlerGroup($this);
    }

    return $this;
}

public function removeShopPaymentMethodCollection(ShopPaymentMethod $shopPaymentMethod): self
{
    if ($this->shopPaymentMethodCollection->removeElement($shopPaymentMethod)) {
        // set the owning side to null (unless already changed)
        if ($shopPaymentMethod->getShopPaymentHandlerGroup() === $this) {
            $shopPaymentMethod->setShopPaymentHandlerGroup(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, PkgShopPaymentIpnTrigger>
*/
public function getPkgShopPaymentIpnTriggerCollection(): Collection
{
    return $this->pkgShopPaymentIpnTriggerCollection;
}

public function addPkgShopPaymentIpnTriggerCollection(PkgShopPaymentIpnTrigger $pkgShopPaymentIpnTrigger): self
{
    if (!$this->pkgShopPaymentIpnTriggerCollection->contains($pkgShopPaymentIpnTrigger)) {
        $this->pkgShopPaymentIpnTriggerCollection->add($pkgShopPaymentIpnTrigger);
        $pkgShopPaymentIpnTrigger->setShopPaymentHandlerGroup($this);
    }

    return $this;
}

public function removePkgShopPaymentIpnTriggerCollection(PkgShopPaymentIpnTrigger $pkgShopPaymentIpnTrigger): self
{
    if ($this->pkgShopPaymentIpnTriggerCollection->removeElement($pkgShopPaymentIpnTrigger)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentIpnTrigger->getShopPaymentHandlerGroup() === $this) {
            $pkgShopPaymentIpnTrigger->setShopPaymentHandlerGroup(null);
        }
    }

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, PkgShopPaymentIpnMessage>
*/
public function getPkgShopPaymentIpnMessageCollection(): Collection
{
    return $this->pkgShopPaymentIpnMessageCollection;
}

public function addPkgShopPaymentIpnMessageCollection(PkgShopPaymentIpnMessage $pkgShopPaymentIpnMessage): self
{
    if (!$this->pkgShopPaymentIpnMessageCollection->contains($pkgShopPaymentIpnMessage)) {
        $this->pkgShopPaymentIpnMessageCollection->add($pkgShopPaymentIpnMessage);
        $pkgShopPaymentIpnMessage->setShopPaymentHandlerGroup($this);
    }

    return $this;
}

public function removePkgShopPaymentIpnMessageCollection(PkgShopPaymentIpnMessage $pkgShopPaymentIpnMessage): self
{
    if ($this->pkgShopPaymentIpnMessageCollection->removeElement($pkgShopPaymentIpnMessage)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopPaymentIpnMessage->getShopPaymentHandlerGroup() === $this) {
            $pkgShopPaymentIpnMessage->setShopPaymentHandlerGroup(null);
        }
    }

    return $this;
}


  
}
