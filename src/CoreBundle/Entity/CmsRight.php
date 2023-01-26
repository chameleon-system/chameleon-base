<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsRight {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** CMS abbreviation of the rights type  */
    public readonly string $name, 
    /** German translation - rights type */
    public readonly string $049Trans  ) {}
}