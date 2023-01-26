<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsCoreLog {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Time stamp */
    public readonly string $timestamp, 
    /** Channel */
    public readonly string $channel, 
    /**  */
    public readonly string $level, 
    /** Message */
    public readonly string $message, 
    /** User session ID */
    public readonly string $session, 
    /** Request ID */
    public readonly string $uid, 
    /** File name */
    public readonly string $file, 
    /** Line */
    public readonly string $line, 
    /** Request URL */
    public readonly string $requestUrl, 
    /**  */
    public readonly string $referrerUrl, 
    /** HTTP method */
    public readonly string $httpMethod, 
    /** Server name */
    public readonly string $server, 
    /** Client IP address */
    public readonly string $ip, 
    /** Extranet user ID */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Extranet user login */
    public readonly string $dataExtranetUserName, 
    /** CMS user */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /**  */
    public readonly string $context  ) {}
}