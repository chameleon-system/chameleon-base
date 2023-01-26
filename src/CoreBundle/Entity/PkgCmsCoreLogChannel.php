<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsCoreLogChannel {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Maximum age of entries for this channel (in seconds) */
    public readonly string $maxLogAgeInSeconds  ) {}
}