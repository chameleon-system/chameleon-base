<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStatusCode {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\Shop|null - Belongs to shop */
private \ChameleonSystem\CoreBundle\Entity\Shop|null $shop = null,
/** @var null|string - Belongs to shop */
private ?string $shopId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null - Run following transaction, if status is executed */
private \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null $pkgShopPaymentTransactionType = null,
/** @var null|string - Run following transaction, if status is executed */
private ?string $pkgShopPaymentTransactionTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataMailProfile|null - Email profile */
private \ChameleonSystem\CoreBundle\Entity\DataMailProfile|null $dataMailProfile = null,
/** @var null|string - Email profile */
private ?string $dataMailProfileId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Send status notification via email */
private bool $sendMailNotification = true, 
    // TCMSFieldVarchar
/** @var string - System name / merchandise management code */
private string $systemName = '', 
    // TCMSFieldWYSIWYG
/** @var string - Status text */
private string $infoText = ''  ) {}

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


  
    // TCMSFieldLookup
public function getShop(): \ChameleonSystem\CoreBundle\Entity\Shop|null
{
    return $this->shop;
}
public function setShop(\ChameleonSystem\CoreBundle\Entity\Shop|null $shop): self
{
    $this->shop = $shop;
    $this->shopId = $shop?->getId();

    return $this;
}
public function getShopId(): ?string
{
    return $this->shopId;
}
public function setShopId(?string $shopId): self
{
    $this->shopId = $shopId;
    // todo - load new id
    //$this->shopId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isSendMailNotification(): bool
{
    return $this->sendMailNotification;
}
public function setSendMailNotification(bool $sendMailNotification): self
{
    $this->sendMailNotification = $sendMailNotification;

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


  
    // TCMSFieldLookup
public function getPkgShopPaymentTransactionType(): \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null
{
    return $this->pkgShopPaymentTransactionType;
}
public function setPkgShopPaymentTransactionType(\ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType|null $pkgShopPaymentTransactionType): self
{
    $this->pkgShopPaymentTransactionType = $pkgShopPaymentTransactionType;
    $this->pkgShopPaymentTransactionTypeId = $pkgShopPaymentTransactionType?->getId();

    return $this;
}
public function getPkgShopPaymentTransactionTypeId(): ?string
{
    return $this->pkgShopPaymentTransactionTypeId;
}
public function setPkgShopPaymentTransactionTypeId(?string $pkgShopPaymentTransactionTypeId): self
{
    $this->pkgShopPaymentTransactionTypeId = $pkgShopPaymentTransactionTypeId;
    // todo - load new id
    //$this->pkgShopPaymentTransactionTypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getDataMailProfile(): \ChameleonSystem\CoreBundle\Entity\DataMailProfile|null
{
    return $this->dataMailProfile;
}
public function setDataMailProfile(\ChameleonSystem\CoreBundle\Entity\DataMailProfile|null $dataMailProfile): self
{
    $this->dataMailProfile = $dataMailProfile;
    $this->dataMailProfileId = $dataMailProfile?->getId();

    return $this;
}
public function getDataMailProfileId(): ?string
{
    return $this->dataMailProfileId;
}
public function setDataMailProfileId(?string $dataMailProfileId): self
{
    $this->dataMailProfileId = $dataMailProfileId;
    // todo - load new id
    //$this->dataMailProfileId = $?->getId();

    return $this;
}



  
    // TCMSFieldWYSIWYG
public function getInfoText(): string
{
    return $this->infoText;
}
public function setInfoText(string $infoText): self
{
    $this->infoText = $infoText;

    return $this;
}


  
}
