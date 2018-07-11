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
     * @return array|string
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
     * @return array|string
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
     * @return array|string
     */
    public function getFilteredPostInput($key, $default = null, $deep = false, $filter = TCMSUSERINPUT_DEFAULTFILTER);

    /**
     * Applies a filter to one or more values.
     *
     * @param array|string $value
     * @param $filterClass - form: classname;path;type|classname;path;type
     *
     * @return array|string
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
