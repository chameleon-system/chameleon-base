<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsCronjobs {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Is running at the moment (locked) */
    public readonly bool $lock, 
    /** Last executed on */
    public readonly string $lastExecution, 
    /** Last excecuted (real) */
    public readonly \DateTime $realLastExecution, 
    /** Class name/service ID */
    public readonly string $cronClass, 
    /** Class type */
    public readonly string $classLocation, 
    /** Class path */
    public readonly string $classSubtype, 
    /** Reset lock after N minutes */
    public readonly string $unlockAfterNMinutes, 
    /** Execute every N minutes */
    public readonly string $executeEveryNMinutes, 
    /** Active until */
    public readonly \DateTime $endExecution, 
    /** Active from */
    public readonly \DateTime $startExecution, 
    /** Active */
    public readonly bool $active, 
    /** Name */
    public readonly string $name  ) {}
}