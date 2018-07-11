<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\TransformOutgoingMailTargetsServiceInterface;

class TransformOutgoingMailTargetsService implements TransformOutgoingMailTargetsServiceInterface
{
    /**
     * @var bool
     */
    private $enableTransformation = false;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var string
     */
    private $transformationTarget;
    /**
     * @var array|null
     */
    private $whiteListedDomains;
    /**
     * @var array|null
     */
    private $whiteListedAddresses;
    /**
     * @var bool
     */
    private $whiteListPortalDomains = false;
    /**
     * @var string
     */
    private $subjectPrefix;

    /**
     * @param string                       $transformationTarget
     * @param string                       $whiteList
     * @param PortalDomainServiceInterface $portalDomainService
     */
    public function __construct($transformationTarget, $whiteList, PortalDomainServiceInterface $portalDomainService)
    {
        $this->portalDomainService = $portalDomainService;
        $this->transformationTarget = $transformationTarget;
        $this->extractWhiteList($whiteList);
    }

    /**
     * @param bool $enableTransformation
     */
    public function setEnableTransformation($enableTransformation)
    {
        $this->enableTransformation = $enableTransformation;
    }

    /**
     * @param string $prefix
     */
    public function setSubjectPrefix($prefix)
    {
        $this->subjectPrefix = $prefix;
    }

    /**
     * @param string $mail
     *
     * @return string
     */
    public function transform($mail)
    {
        if (false === $this->enableTransformation) {
            return $mail;
        }

        if ($this->mailIsWhiteListed($mail)) {
            return $mail;
        }

        return $this->transformationTarget;
    }

    /**
     * @param string $subject
     *
     * @return string
     */
    public function transformSubject($subject)
    {
        if (null === $this->subjectPrefix) {
            return $subject;
        }

        return $this->subjectPrefix.$subject;
    }

    /**
     * @param string $whiteList
     */
    private function extractWhiteList($whiteList)
    {
        $this->whiteListedDomains = array();
        $this->whiteListedAddresses = array();
        $this->whiteListPortalDomains = false;
        $whiteList = str_replace(' ', '', $whiteList);
        $parts = explode(';', $whiteList);
        foreach ($parts as $pattern) {
            if (0 === strcasecmp($pattern, '@PORTAL-DOMAINS')) {
                $this->whiteListPortalDomains = true;
            } elseif ('@' === substr($pattern, 0, 1)) {
                $this->whiteListedDomains[] = substr($pattern, 1);
            } else {
                $this->whiteListedAddresses[] = $pattern;
            }
        }
    }

    /**
     * @param string $mail
     *
     * @return bool
     */
    private function mailIsWhiteListed($mail)
    {
        if (in_array($mail, $this->whiteListedAddresses)) {
            return true;
        }
        $domain = substr($mail, strpos($mail, '@') + 1);
        if (in_array($domain, $this->whiteListedDomains)) {
            return true;
        }

        if (false === $this->whiteListPortalDomains) {
            return false;
        }

        $portalDomains = $this->portalDomainService->getDomainNameList();
        if (in_array($domain, $portalDomains)) {
            return true;
        }
        // try the www version

        if (in_array('www.'.$domain, $portalDomains)) {
            return true;
        }

        return false;
    }
}
