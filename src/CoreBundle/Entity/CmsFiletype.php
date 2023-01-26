<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsFiletype {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Content type */
    public readonly string $contentType, 
    /** File type */
    public readonly string $name, 
    /** File extension */
    public readonly string $fileExtension  ) {}
}