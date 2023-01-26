<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopModuleArticlelistOrderby {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Internal name */
    public readonly string $internalname, 
    /** Public name */
    public readonly string $namePublic, 
    /** Name */
    public readonly string $name, 
    /** Position */
    public readonly int $position, 
    /** SQL ORDER BY String */
    public readonly string $sqlOrderBy, 
    /** Sorting direction */
    public readonly string $orderDirection, 
    /** SQL secondary sorting */
    public readonly string $sqlSecondaryOrderByString  ) {}
}