<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopManufacturer {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Active */
    public readonly bool $active, 
    /** Position */
    public readonly int $position, 
    /** Short description */
    public readonly string $descriptionShort, 
    /** @var array&lt;int,string&gt; Icon / logo */
    public readonly array $cmsMediaId, 
    /** Color */
    public readonly string $color, 
    /** CSS file for manufacturer page */
    public readonly string $css, 
    /** Description */
    public readonly string $description, 
    /** Size chart */
    public readonly string $sizetable  ) {}
}