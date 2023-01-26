<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTags {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Tag */
    public readonly string $name, 
    /** URL name */
    public readonly string $urlname, 
    /** Quantity */
    public readonly string $count  ) {}
}