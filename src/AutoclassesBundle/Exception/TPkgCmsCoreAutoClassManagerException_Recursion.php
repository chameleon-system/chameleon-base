<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Exception;

class TPkgCmsCoreAutoClassManagerException_Recursion extends TPkgCmsCoreAutoClassManagerException
{
    /**
     * @var string|null
     */
    private $sNameOfClassAttemptedToGenerate;
    /**
     * @var array|null
     */
    private $aClassCallStack;

    /**
     * @param string $sNameOfClassAttemptedToGenerate
     * @param array $aClassCallStack
     * @param string $filename
     * @param int $lineno
     */
    public function __construct($sNameOfClassAttemptedToGenerate, $aClassCallStack, $filename, $lineno)
    {
        $this->sNameOfClassAttemptedToGenerate = $sNameOfClassAttemptedToGenerate;
        $this->aClassCallStack = $aClassCallStack;
        parent::__construct($this->generateMessageFromCallDetails(), 0, E_USER_ERROR, $filename, $lineno);
    }

    /**
     * @return string
     */
    private function generateMessageFromCallDetails()
    {
        return "Unable to generate auto class {$this->sNameOfClassAttemptedToGenerate} because a recursion occured. Class-Call-Stack: ".implode(
            ', ',
            $this->aClassCallStack
        );
    }
}
