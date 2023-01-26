<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsLanguage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Activated for frontend */
    public readonly bool $activeForFrontEnd, 
    /** ISO 639-1 language code */
    public readonly string $iso6391  ) {}
}