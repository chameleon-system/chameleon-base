<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopListfilterItemType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Filter element class */
    public readonly string $class, 
    /** Class subtypes of the filter element */
    public readonly string $classSubtype, 
    /** Class type of the filter element */
    public readonly string $classType, 
    /** View of the filter element */
    public readonly string $view, 
    /** Class type of the view for the filter element */
    public readonly string $viewClassType, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsFieldConf[] Available fields of the filter element */
    public readonly array $cmsFieldConfMlt  ) {}
}