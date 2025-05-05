<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Response;

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\TokenInjectionFailedException;

/**
 * Defines a service that replaces variables in a string. The intended usage pattern is as follows:
 * - Add variables by calling addVariable() anywhere during the request (these calls might be distributed over a complex code base).
 * - Replace placeholders with variable values by calling replaceVariables() at the end of the request.
 */
interface ResponseVariableReplacerInterface
{
    /**
     * Add a variable that can then be replaced by replaceVariables(). Note that variables MUST already be escaped by
     * the caller, as implementations of this interface do not know the context in which the variables are used and are
     * therefore unable to perform the correct escaping.
     */
    public function addVariable(string $key, string $value): void;

    /**
     * Replace variables in $content with values set before by addVariable().
     * When passing an object or an array, variables will be replaced recursively in all values, not in keys.
     * Only public properties of an object will be processed.
     * Independent from defined variables, the method will also inject the authenticity token into the content.
     *
     * @template T of \stdClass|array|string
     *
     * @param T $content
     *
     * @return T
     *
     * @throws TokenInjectionFailedException
     */
    public function replaceVariables($content);
}
