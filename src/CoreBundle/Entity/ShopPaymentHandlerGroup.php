<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandlerGroup {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Overwrite Tdb with this class */
private string $classname = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroupConfig[] - Configuration */
private \Doctrine\Common\Collections\Collection $shopPaymentHandlerGroupConfigCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - IPN Identifier */
private string $ipnGroupIdentifier = '', 
    // TCMSFieldVarchar
/** @var string - Character encoding of data transmitted by the provider */
private string $ipnPayloadCharacterCharset = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus[] - IPN status codes */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentIpnStatusCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler[] - Payment handler */
private \Doctrine\Common\Collections\Collection $shopPaymentHandlerCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod[] - Payment methods */
private \Doctrine\Common\Collections\Collection $shopPaymentMethodCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - IPN may come from the following IP */
private string $ipnAllowedIps = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnTrigger[] - Redirections */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentIpnTriggerCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage[] - IPN messages */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldOption
/** @var string - Environment */
private string $environment = 'default'  ) {}

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
public function getShopPaymentHandlerGroupConfigCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopPaymentHandlerGroupConfigCollection;
}
public function setShopPaymentHandlerGroupConfigCollection(\Doctrine\Common\Collections\Collection $shopPaymentHandlerGroupConfigCollection): self
{
    $this->shopPaymentHandlerGroupConfigCollection = $shopPaymentHandlerGroupConfigCollection;

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
public function getPkgShopPaymentIpnStatusCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentIpnStatusCollection;
}
public function setPkgShopPaymentIpnStatusCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentIpnStatusCollection): self
{
    $this->pkgShopPaymentIpnStatusCollection = $pkgShopPaymentIpnStatusCollection;

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


  
    // TCMSFieldPropertyTable
public function getShopPaymentHandlerCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopPaymentHandlerCollection;
}
public function setShopPaymentHandlerCollection(\Doctrine\Common\Collections\Collection $shopPaymentHandlerCollection): self
{
    $this->shopPaymentHandlerCollection = $shopPaymentHandlerCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopPaymentMethodCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopPaymentMethodCollection;
}
public function setShopPaymentMethodCollection(\Doctrine\Common\Collections\Collection $shopPaymentMethodCollection): self
{
    $this->shopPaymentMethodCollection = $shopPaymentMethodCollection;

    return $this;
}


  
    // TCMSFieldText
public function getIpnAllowedIps(): string
{
    return $this->ipnAllowedIps;
}
public function setIpnAllowedIps(string $ipnAllowedIps): self
{
    $this->ipnAllowedIps = $ipnAllowedIps;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopPaymentIpnTriggerCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentIpnTriggerCollection;
}
public function setPkgShopPaymentIpnTriggerCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentIpnTriggerCollection): self
{
    $this->pkgShopPaymentIpnTriggerCollection = $pkgShopPaymentIpnTriggerCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopPaymentIpnMessageCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentIpnMessageCollection;
}
public function setPkgShopPaymentIpnMessageCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageCollection): self
{
    $this->pkgShopPaymentIpnMessageCollection = $pkgShopPaymentIpnMessageCollection;

    return $this;
}


  
    // TCMSFieldOption
public function getEnvironment(): string
{
    return $this->environment;
}
public function setEnvironment(string $environment): self
{
    $this->environment = $environment;

    return $this;
}


  
}
