<?php

namespace ChameleonSystem\CoreBundle\Routing;

class DomainValidatorRequestLevelCacheDecorator implements DomainValidatorInterface
{
    /**
     * @var DomainValidatorInterface
     */
    private $subject;
    /**
     * @var array
     */
    private $cache = [];

    public function __construct(DomainValidatorInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidDomain($domain, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $secure = true)
    {
        $cacheKey = $this->getCacheKey($domain, $portal, $language, $secure);
        if (false === isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->subject->getValidDomain($domain, $portal, $language, $secure);
        }

        return $this->cache[$cacheKey];
    }

    /**
     * @param string $domain
     * @param bool $secure
     *
     * @return string
     */
    private function getCacheKey($domain, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $secure = true)
    {
        $keyParts = [];
        $keyParts[] = $domain;
        $keyParts[] = null === $portal ? 'xx' : $portal->id;
        $keyParts[] = null === $language ? 'xx' : $language->id;
        $keyParts[] = $secure;

        return md5(implode('-', $keyParts));
    }
}
