<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchIndexer {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Started on */
    public readonly \DateTime $started, 
    /** Completed */
    public readonly \DateTime $completed, 
    /** Number of lines to process */
    public readonly string $totalRowsToProcess, 
    /** Data */
    public readonly string $processdata  ) {}
}