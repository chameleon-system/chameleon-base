<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMessageManagerBackendMessage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Message type */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType $cmsMessageManagerMessageTypeId, 
    /** Message */
    public readonly string $message, 
    /** Belongs to CMS config */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsConfig $cmsConfigId, 
    /** Code */
    public readonly string $name, 
    /** Message description */
    public readonly string $description  ) {}
}