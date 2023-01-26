<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopListfilter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Title to be shown on top of the filter on the website */
    public readonly string $title, 
    /** Description text shown on top of the filter */
    public readonly string $introtext, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilterItem[] List filter entries */
    public readonly array $pkgShopListfilterItem  ) {}
}