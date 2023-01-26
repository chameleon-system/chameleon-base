<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleFeedback {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Headline */
    public readonly string $name, 
    /** Text */
    public readonly string $text, 
    /** Closing text */
    public readonly string $doneText, 
    /** Feedback recipient (email address) */
    public readonly string $toEmail, 
    /** Feedback blind copy recipient (email address) */
    public readonly string $bccEmail, 
    /** Sender (email address) */
    public readonly string $fromEmail, 
    /** Default subject */
    public readonly string $defaultSubject, 
    /** Default text */
    public readonly string $defaultBody  ) {}
}