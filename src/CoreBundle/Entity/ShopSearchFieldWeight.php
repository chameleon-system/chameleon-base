<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchFieldWeight {
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
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - Language */
private ?string $cmsLanguageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchQuery|null - Selection to be used */
private \ChameleonSystem\CoreBundle\Entity\ShopSearchQuery|null $shopSearchQuery = null,
/** @var null|string - Selection to be used */
private ?string $shopSearchQueryId = null
, 
    // TCMSFieldVarchar
/** @var string - Descriptive name of the field / table combination */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Table */
private string $tablename = '', 
    // TCMSFieldVarchar
/** @var string - Field */
private string $fieldname = '', 
    // TCMSFieldDecimal
/** @var float - Weight */
private float $weight = 0, 
    // TCMSFieldVarchar
/** @var string - Field name in query */
private string $fieldNameInQuery = '', 
    // TCMSFieldBoolean
/** @var bool - Indexing partial words */
private bool $indexPartialWords = true  ) {}

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



  
    // TCMSFieldLookup
public function getCmsLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->cmsLanguage;
}
public function setCmsLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;
    $this->cmsLanguageId = $cmsLanguage?->getId();

    return $this;
}
public function getCmsLanguageId(): ?string
{
    return $this->cmsLanguageId;
}
public function setCmsLanguageId(?string $cmsLanguageId): self
{
    $this->cmsLanguageId = $cmsLanguageId;
    // todo - load new id
    //$this->cmsLanguageId = $?->getId();

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
public function getTablename(): string
{
    return $this->tablename;
}
public function setTablename(string $tablename): self
{
    $this->tablename = $tablename;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFieldname(): string
{
    return $this->fieldname;
}
public function setFieldname(string $fieldname): self
{
    $this->fieldname = $fieldname;

    return $this;
}


  
    // TCMSFieldDecimal
public function getWeight(): float
{
    return $this->weight;
}
public function setWeight(float $weight): self
{
    $this->weight = $weight;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopSearchQuery(): \ChameleonSystem\CoreBundle\Entity\ShopSearchQuery|null
{
    return $this->shopSearchQuery;
}
public function setShopSearchQuery(\ChameleonSystem\CoreBundle\Entity\ShopSearchQuery|null $shopSearchQuery): self
{
    $this->shopSearchQuery = $shopSearchQuery;
    $this->shopSearchQueryId = $shopSearchQuery?->getId();

    return $this;
}
public function getShopSearchQueryId(): ?string
{
    return $this->shopSearchQueryId;
}
public function setShopSearchQueryId(?string $shopSearchQueryId): self
{
    $this->shopSearchQueryId = $shopSearchQueryId;
    // todo - load new id
    //$this->shopSearchQueryId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getFieldNameInQuery(): string
{
    return $this->fieldNameInQuery;
}
public function setFieldNameInQuery(string $fieldNameInQuery): self
{
    $this->fieldNameInQuery = $fieldNameInQuery;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIndexPartialWords(): bool
{
    return $this->indexPartialWords;
}
public function setIndexPartialWords(bool $indexPartialWords): self
{
    $this->indexPartialWords = $indexPartialWords;

    return $this;
}


  
}
