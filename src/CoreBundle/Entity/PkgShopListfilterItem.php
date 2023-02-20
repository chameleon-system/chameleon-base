<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopListfilterItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null - Belongs to list filter configuration */
private \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilter = null,
/** @var null|string - Belongs to list filter configuration */
private ?string $pkgShopListfilterId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItemType|null - Filter type */
private \ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItemType|null $pkgShopListfilterItemType = null,
/** @var null|string - Filter type */
private ?string $pkgShopListfilterItemTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopAttribute|null - Belonging product attribute */
private \ChameleonSystem\CoreBundle\Entity\ShopAttribute|null $shopAttribute = null,
/** @var null|string - Belonging product attribute */
private ?string $shopAttributeId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = '', 
    // TCMSFieldBoolean
/** @var bool - Multiple selections */
private bool $allowMultiSelection = false, 
    // TCMSFieldBoolean
/** @var bool - Show all when opening the page? */
private bool $showAllOnPageLoad = true, 
    // TCMSFieldNumber
/** @var int - Window size */
private int $previewSize = 0, 
    // TCMSFieldBoolean
/** @var bool - Show scrollbars instead of "show all" button? */
private bool $showScrollbars = false, 
    // TCMSFieldNumber
/** @var int - Lowest value */
private int $minValue = 0, 
    // TCMSFieldNumber
/** @var int - Highest value */
private int $maxValue = 0, 
    // TCMSFieldVarchar
/** @var string - MySQL field name */
private string $mysqlFieldName = '', 
    // TCMSFieldVarchar
/** @var string - View */
private string $view = '', 
    // TCMSFieldOption
/** @var string - View class type */
private string $viewClassType = 'Customer', 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $position = 0, 
    // TCMSFieldVarchar
/** @var string - System name of the variant type */
private string $variantIdentifier = ''  ) {}

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
public function getPkgShopListfilter(): \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null
{
    return $this->pkgShopListfilter;
}
public function setPkgShopListfilter(\ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilter): self
{
    $this->pkgShopListfilter = $pkgShopListfilter;
    $this->pkgShopListfilterId = $pkgShopListfilter?->getId();

    return $this;
}
public function getPkgShopListfilterId(): ?string
{
    return $this->pkgShopListfilterId;
}
public function setPkgShopListfilterId(?string $pkgShopListfilterId): self
{
    $this->pkgShopListfilterId = $pkgShopListfilterId;
    // todo - load new id
    //$this->pkgShopListfilterId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getPkgShopListfilterItemType(): \ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItemType|null
{
    return $this->pkgShopListfilterItemType;
}
public function setPkgShopListfilterItemType(\ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItemType|null $pkgShopListfilterItemType): self
{
    $this->pkgShopListfilterItemType = $pkgShopListfilterItemType;
    $this->pkgShopListfilterItemTypeId = $pkgShopListfilterItemType?->getId();

    return $this;
}
public function getPkgShopListfilterItemTypeId(): ?string
{
    return $this->pkgShopListfilterItemTypeId;
}
public function setPkgShopListfilterItemTypeId(?string $pkgShopListfilterItemTypeId): self
{
    $this->pkgShopListfilterItemTypeId = $pkgShopListfilterItemTypeId;
    // todo - load new id
    //$this->pkgShopListfilterItemTypeId = $?->getId();

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


  
    // TCMSFieldLookup
public function getShopAttribute(): \ChameleonSystem\CoreBundle\Entity\ShopAttribute|null
{
    return $this->shopAttribute;
}
public function setShopAttribute(\ChameleonSystem\CoreBundle\Entity\ShopAttribute|null $shopAttribute): self
{
    $this->shopAttribute = $shopAttribute;
    $this->shopAttributeId = $shopAttribute?->getId();

    return $this;
}
public function getShopAttributeId(): ?string
{
    return $this->shopAttributeId;
}
public function setShopAttributeId(?string $shopAttributeId): self
{
    $this->shopAttributeId = $shopAttributeId;
    // todo - load new id
    //$this->shopAttributeId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isAllowMultiSelection(): bool
{
    return $this->allowMultiSelection;
}
public function setAllowMultiSelection(bool $allowMultiSelection): self
{
    $this->allowMultiSelection = $allowMultiSelection;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowAllOnPageLoad(): bool
{
    return $this->showAllOnPageLoad;
}
public function setShowAllOnPageLoad(bool $showAllOnPageLoad): self
{
    $this->showAllOnPageLoad = $showAllOnPageLoad;

    return $this;
}


  
    // TCMSFieldNumber
public function getPreviewSize(): int
{
    return $this->previewSize;
}
public function setPreviewSize(int $previewSize): self
{
    $this->previewSize = $previewSize;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowScrollbars(): bool
{
    return $this->showScrollbars;
}
public function setShowScrollbars(bool $showScrollbars): self
{
    $this->showScrollbars = $showScrollbars;

    return $this;
}


  
    // TCMSFieldNumber
public function getMinValue(): int
{
    return $this->minValue;
}
public function setMinValue(int $minValue): self
{
    $this->minValue = $minValue;

    return $this;
}


  
    // TCMSFieldNumber
public function getMaxValue(): int
{
    return $this->maxValue;
}
public function setMaxValue(int $maxValue): self
{
    $this->maxValue = $maxValue;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMysqlFieldName(): string
{
    return $this->mysqlFieldName;
}
public function setMysqlFieldName(string $mysqlFieldName): self
{
    $this->mysqlFieldName = $mysqlFieldName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getView(): string
{
    return $this->view;
}
public function setView(string $view): self
{
    $this->view = $view;

    return $this;
}


  
    // TCMSFieldOption
public function getViewClassType(): string
{
    return $this->viewClassType;
}
public function setViewClassType(string $viewClassType): self
{
    $this->viewClassType = $viewClassType;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
    // TCMSFieldVarchar
public function getVariantIdentifier(): string
{
    return $this->variantIdentifier;
}
public function setVariantIdentifier(string $variantIdentifier): self
{
    $this->variantIdentifier = $variantIdentifier;

    return $this;
}


  
}
