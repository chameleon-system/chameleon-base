<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Field\Provider;

class ClassFromTableFieldProviderRequestLevelCacheDecorator implements ClassFromTableFieldProviderInterface
{
    /**
     * @var ClassFromTableFieldProviderInterface
     */
    private $subject;
    /**
     * @var array
     */
    private $fieldClassNameCache = [];
    /**
     * @var array
     */
    private $dictionaryCache = [];

    public function __construct(ClassFromTableFieldProviderInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldClassNameFromTableField($tableField)
    {
        if (false === \array_key_exists($tableField, $this->fieldClassNameCache)) {
            $this->fieldClassNameCache[$tableField] = $this->subject->getFieldClassNameFromTableField($tableField);
        }

        return $this->fieldClassNameCache[$tableField];
    }

    /**
     * {@inheritdoc}
     */
    public function getDictionaryFromTableField($fieldIdentifier)
    {
        if (false === \array_key_exists($fieldIdentifier, $this->dictionaryCache)) {
            $this->dictionaryCache[$fieldIdentifier] = $this->subject->getDictionaryFromTableField($fieldIdentifier);
        }

        return $this->dictionaryCache[$fieldIdentifier];
    }
}
