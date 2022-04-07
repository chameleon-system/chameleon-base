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

use TCMSUserInputFilter_BaseClass;

/**
 * Interface InputFilterUtilInterface defines a service that can be used to filter user input for security reasons.
 * It also allows to retrieve filtered user input directly from the request.
 */
interface InputFilterUtilInterface
{
    /**
     * Retrieves a value from user input and filters it according to the given input filter.
     * The fetch semantics and the first three arguments are the same as for Symfony\Component\HttpFoundation\Request::get().
     *
     * @param $key
     * @param $default
     * @param bool   $deep
     * @param string $filter
     *
     * @return string|null
     *
     * @psalm-template TDefault
     * @psalm-param TDefault $default
     * @psalm-return string|TDefault
     */
    public function getFilteredInput($key, $default = null, $deep = false, $filter = TCMSUSERINPUT_DEFAULTFILTER);

    /**
     * Retrieves a value from user input (GET only) and filters it according to the given input filter.
     * The fetch semantics and the first three arguments are the same as for Symfony\Component\HttpFoundation\Request::query::get().
     *
     * @param $key
     * @param $default
     * @param bool   $deep
     * @param string $filter
     *
     * @return string|null
     *
     * @psalm-template TDefault
     * @psalm-param TDefault $default
     * @psalm-return string|TDefault
     */
    public function getFilteredGetInput($key, $default = null, $deep = false, $filter = TCMSUSERINPUT_DEFAULTFILTER);

    /**
     * Retrieves a value from user input (POST only) and filters it according to the given input filter.
     * The fetch semantics and the first three arguments are the same as for Symfony\Component\HttpFoundation\Request::request::get().
     *
     * @param $key
     * @param $default
     * @param bool   $deep
     * @param string $filter
     *
     * @return string|null
     *
     * @psalm-template TDefault
     * @psalm-param TDefault $default
     * @psalm-return string|TDefault
     */
    public function getFilteredPostInput($key, $default = null, $deep = false, $filter = TCMSUSERINPUT_DEFAULTFILTER);

    /**
     * Applies a filter to one or more values.
     *
     * @param string|null $filter
     * @param string $filterClass - form: classname;path;type|classname;path;type
     * @return string|null - null if $filter is null
     *
     * @psalm-return ($filter is null ? null : string)
     */
    public function filterValue($value, $filterClass);

    /**
     * Splits the given filter string into an array of filter objects of TCMSUserInputFilter or its subclasses.
     *
     * @param string $filterClass - form: classname;path;type|classname;path;type
     *
     * @return TCMSUserInputFilter_BaseClass[]
     */
    public function getFilterObject($filterClass);
}
