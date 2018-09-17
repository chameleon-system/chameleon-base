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
    private $enableTransformation = true;
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
     * @param string                       $subjectPrefix
     */
    public function __construct($transformationTarget, $whiteList, PortalDomainServiceInterface $portalDomainService, $subjectPrefix)
    {
        $this->transformationTarget = $transformationTarget;
        $this->extractWhiteList($whiteList);
        $this->portalDomainService = $portalDomainService;
        $this->subjectPrefix = $subjectPrefix;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated since 6.3.0 - see deprecation note in interface. This implementation is now active by default
     *             and should simply not be called if it should not be used (e.g. by using NullOutgoingMailTargetsService
     *             instead). If deactivated by calling this method, the email subject will still be prefixed.
     */
    public function setEnableTransformation($enableTransformation)
    {
        $this->enableTransformation = $enableTransformation;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated since 6.3.0 - use constructor injection instead.
     */
    public function setSubjectPrefix($prefix)
    {
        $this->subjectPrefix = $prefix;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
