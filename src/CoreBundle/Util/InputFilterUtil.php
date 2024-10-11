<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class InputFilterUtil.
 */
class InputFilterUtil implements InputFilterUtilInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredInput($key, $default = null, $deep = false, $filter = TCMSUSERINPUT_DEFAULTFILTER)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $default;
        }

        return $this->filterValue($request->get($key, $default, $deep), $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredGetInput($key, $default = null, $deep = false, $filter = TCMSUSERINPUT_DEFAULTFILTER)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $default;
        }

        try {
            return $this->filterValue($request->query->get($key, $default, $deep), $filter);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('getFilteredGetInput for receiving arrays is deprecated, it just works for scalar values. If you expect an array, please use getFilteredGetInputArray instead.', ['key' => $key, 'default' => $default, 'filter' => $filter]);
            return $this->getFilteredGetInputArray($key, $default, $filter);
        }
    }

    public function getFilteredGetInputArray($key, $default = null, $filter = TCMSUSERINPUT_DEFAULTFILTER)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $default;
        }

        $parameter = $request->query->all($key) ?? $default;

        return $this->filterValue($parameter, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredPostInput($key, $default = null, $deep = false, $filter = TCMSUSERINPUT_DEFAULTFILTER)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $default;
        }

        try {
            return $this->filterValue($request->request->get($key, $default, $deep), $filter);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('getFilteredPostInput for receiving arrays is deprecated, it just works for scalar values. If you expect an array, please use getFilteredPostInputArray instead.', ['key' => $key, 'default' => $default, 'filter' => $filter]);
            return $this->getFilteredPostInputArray($key, $default, $filter);
        }

    }

    public function getFilteredPostInputArray($key, $default = null, $filter = TCMSUSERINPUT_DEFAULTFILTER)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $default;
        }

        $parameter = $request->request->all($key) ?? $default;

        return $this->filterValue($parameter, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function filterValue($value, $filterClass)
    {
        if (null === $value) {
            return $value;
        }
        /** @var string $value */
        if (null === $filterClass || '' === $filterClass || false === $filterClass) {
            return $value;
        }

        static $aFilteredValueCache = array();
        $sCacheKey = '';
        if (is_array($value)) {
            $sCacheKey = md5(serialize($value));
        } else {
            $sCacheKey = md5($value);
        }

        $sCacheKey = $filterClass.'-'.$sCacheKey;
        if (array_key_exists($sCacheKey, $aFilteredValueCache)) {
            return $aFilteredValueCache[$sCacheKey];
        }

        $aFilters = $this->getFilterObject($filterClass);
        foreach ($aFilters as $oFilter) {
            $value = $oFilter->Filter($value);
        }
        $aFilteredValueCache[$sCacheKey] = $value;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterObject($filterClass)
    {
        $aFilters = array();
        $aFilterClasses = explode('|', $filterClass);
        foreach ($aFilterClasses as $sFilter) {
            $aParts = explode(';', $sFilter);
            $sClassName = $aParts[0];
            $aFilters[] = new $sClassName();
        }

        return $aFilters;
    }
}
