<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetGroup {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Automatic assignment is active */
    public readonly bool $autoAssignActive, 
    /** Auto assignment from order value */
    public readonly float $autoAssignOrderValueStart, 
    /** Auto assignment up to order value */
    public readonly float $autoAssignOrderValueEnd  ) {}
}