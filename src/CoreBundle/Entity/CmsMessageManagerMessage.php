<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMessageManagerMessage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Code */
    public readonly string $name, 
    /** Message type */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType $cmsMessageManagerMessageTypeId, 
    /** Message description */
    public readonly string $description, 
    /** Message */
    public readonly string $message, 
    /** Type */
    public readonly string $messageLocationType, 
    /** View */
    public readonly string $messageView  ) {}
}