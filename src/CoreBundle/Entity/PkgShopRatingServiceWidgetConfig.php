<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingServiceWidgetConfig {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Module instance */
private ?string $cmsTplModuleInstanceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null - Rating service */
private \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null $pkgShopRatingService = null,
/** @var null|string - Rating service */
private ?string $pkgShopRatingServiceId = null
  ) {}

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



  
    // TCMSFieldLookup
public function getPkgShopRatingService(): \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null
{
    return $this->pkgShopRatingService;
}
public function setPkgShopRatingService(\ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null $pkgShopRatingService): self
{
    $this->pkgShopRatingService = $pkgShopRatingService;
    $this->pkgShopRatingServiceId = $pkgShopRatingService?->getId();

    return $this;
}
public function getPkgShopRatingServiceId(): ?string
{
    return $this->pkgShopRatingServiceId;
}
public function setPkgShopRatingServiceId(?string $pkgShopRatingServiceId): self
{
    $this->pkgShopRatingServiceId = $pkgShopRatingServiceId;
    // todo - load new id
    //$this->pkgShopRatingServiceId = $?->getId();

    return $this;
}



  
}
