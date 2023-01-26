<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataAtomicLock {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /**  */
    public readonly string $lockkey  ) {}
}