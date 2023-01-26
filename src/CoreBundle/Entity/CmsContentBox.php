<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsContentBox {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Widget class */
    public readonly string $className, 
    /** Widget class type */
    public readonly string $classType, 
    /** Widget class subfolder */
    public readonly string $classPath, 
    /** System name */
    public readonly string $systemName, 
    /** Name */
    public readonly string $name, 
    /** Headline color */
    public readonly string $headlinecolHexcolor, 
    /** Headline icon */
    public readonly string $iconList, 
    /** Display in column */
    public readonly string $showInCol  ) {}
}