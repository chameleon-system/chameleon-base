<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataMailProfile {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** ID code */
    public readonly string $idcode, 
    /** Name */
    public readonly string $name, 
    /** Subject */
    public readonly string $subject, 
    /** Recipient email address */
    public readonly string $mailto, 
    /** Recipient name */
    public readonly string $mailtoName, 
    /** Sender email address */
    public readonly string $mailfrom, 
    /** Sender name */
    public readonly string $mailfromName, 
    /** BCC */
    public readonly string $mailbcc, 
    /** Body */
    public readonly string $body, 
    /** Body (text) */
    public readonly string $bodyText, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] Attach the following files to the email */
    public readonly array $attachment, 
    /** Template */
    public readonly string $template, 
    /** Text template */
    public readonly string $templateText, 
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId  ) {}
}