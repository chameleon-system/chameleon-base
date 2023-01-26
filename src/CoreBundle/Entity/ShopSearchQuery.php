<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchQuery {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name / title of query */
    public readonly string $name, 
    /** Query */
    public readonly string $query, 
    /** Index is running */
    public readonly bool $indexRunning, 
    /** Index started on */
    public readonly \DateTime $indexStarted, 
    /** Index completed on */
    public readonly \DateTime $indexCompleted  ) {}
}