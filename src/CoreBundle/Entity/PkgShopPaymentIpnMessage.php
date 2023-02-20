<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentIpnMessage {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Activated via this portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Activated via this portal */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Belongs to order (ID) */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Belongs to order (ID) */
private ?string $shopOrderId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null - Payment provider */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup = null,
/** @var null|string - Payment provider */
private ?string $shopPaymentHandlerGroupId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus|null - Status */
private \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus|null $pkgShopPaymentIpnStatus = null,
/** @var null|string - Status */
private ?string $pkgShopPaymentIpnStatusId = null
, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessageTrigger[] - Forwarding logs */
private \Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageTriggerCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldCreatedTimestamp
/** @var \DateTime|null - Date */
private \DateTime|null $datecreated = null, 
    // TCMSFieldBoolean
/** @var bool - Processed successfully */
private bool $success = false, 
    // TCMSFieldBoolean
/** @var bool - Processed message */
private bool $completed = false, 
    // TCMSFieldVarchar
/** @var string - Type of error */
private string $errorType = '', 
    // TCMSFieldVarchar
/** @var string - IP */
private string $ip = '', 
    // TCMSFieldVarchar
/** @var string - Request URL */
private string $requestUrl = '', 
    // TCMSFieldBlob
/** @var string - Payload */
private string $payload = '', 
    // TCMSFieldText
/** @var string - Error details */
private string $errors = ''  ) {}

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
    // TCMSFieldPropertyTable
public function getPkgShopPaymentIpnMessageTriggerCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopPaymentIpnMessageTriggerCollection;
}
public function setPkgShopPaymentIpnMessageTriggerCollection(\Doctrine\Common\Collections\Collection $pkgShopPaymentIpnMessageTriggerCollection): self
{
    $this->pkgShopPaymentIpnMessageTriggerCollection = $pkgShopPaymentIpnMessageTriggerCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getShopOrder(): \ChameleonSystem\CoreBundle\Entity\ShopOrder|null
{
    return $this->shopOrder;
}
public function setShopOrder(\ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder): self
{
    $this->shopOrder = $shopOrder;
    $this->shopOrderId = $shopOrder?->getId();

    return $this;
}
public function getShopOrderId(): ?string
{
    return $this->shopOrderId;
}
public function setShopOrderId(?string $shopOrderId): self
{
    $this->shopOrderId = $shopOrderId;
    // todo - load new id
    //$this->shopOrderId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getShopPaymentHandlerGroup(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null
{
    return $this->shopPaymentHandlerGroup;
}
public function setShopPaymentHandlerGroup(\ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroup?->getId();

    return $this;
}
public function getShopPaymentHandlerGroupId(): ?string
{
    return $this->shopPaymentHandlerGroupId;
}
public function setShopPaymentHandlerGroupId(?string $shopPaymentHandlerGroupId): self
{
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroupId;
    // todo - load new id
    //$this->shopPaymentHandlerGroupId = $?->getId();

    return $this;
}



  
    // TCMSFieldCreatedTimestamp
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgShopPaymentIpnStatus(): \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus|null
{
    return $this->pkgShopPaymentIpnStatus;
}
public function setPkgShopPaymentIpnStatus(\ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnStatus|null $pkgShopPaymentIpnStatus): self
{
    $this->pkgShopPaymentIpnStatus = $pkgShopPaymentIpnStatus;
    $this->pkgShopPaymentIpnStatusId = $pkgShopPaymentIpnStatus?->getId();

    return $this;
}
public function getPkgShopPaymentIpnStatusId(): ?string
{
    return $this->pkgShopPaymentIpnStatusId;
}
public function setPkgShopPaymentIpnStatusId(?string $pkgShopPaymentIpnStatusId): self
{
    $this->pkgShopPaymentIpnStatusId = $pkgShopPaymentIpnStatusId;
    // todo - load new id
    //$this->pkgShopPaymentIpnStatusId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isSuccess(): bool
{
    return $this->success;
}
public function setSuccess(bool $success): self
{
    $this->success = $success;

    return $this;
}


  
    // TCMSFieldBoolean
public function isCompleted(): bool
{
    return $this->completed;
}
public function setCompleted(bool $completed): self
{
    $this->completed = $completed;

    return $this;
}


  
    // TCMSFieldVarchar
public function getErrorType(): string
{
    return $this->errorType;
}
public function setErrorType(string $errorType): self
{
    $this->errorType = $errorType;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIp(): string
{
    return $this->ip;
}
public function setIp(string $ip): self
{
    $this->ip = $ip;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRequestUrl(): string
{
    return $this->requestUrl;
}
public function setRequestUrl(string $requestUrl): self
{
    $this->requestUrl = $requestUrl;

    return $this;
}


  
    // TCMSFieldBlob
public function getPayload(): string
{
    return $this->payload;
}
public function setPayload(string $payload): self
{
    $this->payload = $payload;

    return $this;
}


  
    // TCMSFieldText
public function getErrors(): string
{
    return $this->errors;
}
public function setErrors(string $errors): self
{
    $this->errors = $errors;

    return $this;
}


  
}
