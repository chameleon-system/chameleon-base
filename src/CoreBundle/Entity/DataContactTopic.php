<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataContactTopic {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name  ) {}
}