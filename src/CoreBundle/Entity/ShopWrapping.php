<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopWrapping {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Position */
    public readonly int $position, 
    /** Wrapping item */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId  ) {}
}