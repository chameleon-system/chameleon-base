<?php

namespace ChameleonSystem\CoreBundle\BackwardsCompatibilityShims;

use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * @example
 *
 * class FooBar {
 *      use NamedConstructorSupport;
 *
 *     public function __construct($id) {
 *          // ...
 *      }
 *
 *      public function Foobar() {
 *          $this->callConstructorAndLogDeprecation(func_get_args());
 *      }
 *
 * }
 */
trait NamedConstructorSupport
{
    /**
     * Calls the main `__constructor` method and logs a deprecation message.
     * This method is meant to be called inside of deprecated named constructors
     * in order to provide backwards compatibility with extending classes in
     * projects that may call the named constructor instead of `parent::__construct`;.
     */
    protected function callConstructorAndLogDeprecation(array $arguments): void
    {
        ServiceLocator::get('logger')->withName('deprecation')->warning(
            'Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.',
            [
                'class' => __CLASS__,
                'calledFrom' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1] ?? null,
            ]
        );

        self::__construct(...$arguments);
    }
}
