<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopListfilter;

class PkgShopListfilterItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var PkgShopListfilter|null - Belongs to list filter configuration */
private ?PkgShopListfilter $pkgShopListfilter = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = '', 
    // TCMSFieldVarchar
/** @var string - Window size */
private string $previewSize = '', 
    // TCMSFieldVarchar
/** @var string - Lowest value */
private string $minValue = '', 
    // TCMSFieldVarchar
/** @var string - Highest value */
private string $maxValue = '', 
    // TCMSFieldVarchar
/** @var string - MySQL field name */
private string $mysqlFieldName = '', 
    // TCMSFieldVarchar
/** @var string - View */
private string $view = '', 
    // TCMSFieldVarchar
/** @var string - System name of the variant type */
private string $variantIdentifier = ''  ) {}

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
    // TCMSFieldLookupParentID
public function getPkgShopListfilter(): ?PkgShopListfilter
{
    return $this->pkgShopListfilter;
}

public function setPkgShopListfilter(?PkgShopListfilter $pkgShopListfilter): self
{
    $this->pkgShopListfilter = $pkgShopListfilter;

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


  
    // TCMSFieldVarchar
public function getPreviewSize(): string
{
    return $this->previewSize;
}
public function setPreviewSize(string $previewSize): self
{
    $this->previewSize = $previewSize;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMinValue(): string
{
    return $this->minValue;
}
public function setMinValue(string $minValue): self
{
    $this->minValue = $minValue;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMaxValue(): string
{
    return $this->maxValue;
}
public function setMaxValue(string $maxValue): self
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
