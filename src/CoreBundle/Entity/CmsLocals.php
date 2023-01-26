<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsLocals {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Date format */
    public readonly string $dateFormat, 
    /** Time format */
    public readonly string $timeFormat, 
    /** PHP local name */
    public readonly string $phpLocalName, 
    /** Short format */
    public readonly string $dateFormatCalendar, 
    /** Numbers */
    public readonly string $numbers  ) {}
}