<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleDocumentType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Title / Headline */
    public readonly string $name, 
    /** System name */
    public readonly string $systemname  ) {}
}