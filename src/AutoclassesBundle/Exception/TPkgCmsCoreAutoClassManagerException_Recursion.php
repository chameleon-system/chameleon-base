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

    private ?string $sNameOfClassAttemptedToGenerate = null;
    private ?array $aClassCallStack = null;

    public function __construct(string $sNameOfClassAttemptedToGenerate, array $aClassCallStack, string $filename, int $lineno)
    {
        $this->sNameOfClassAttemptedToGenerate = $sNameOfClassAttemptedToGenerate;
        $this->aClassCallStack = $aClassCallStack;
        parent::__construct($this->generateMessageFromCallDetails(), 0, E_USER_ERROR, $filename, $lineno);
    }


    private function generateMessageFromCallDetails(): string
    {
        return "Unable to generate auto class {$this->sNameOfClassAttemptedToGenerate} because a recursion occured. Class-Call-Stack: ".implode(
            ', ',
            $this->aClassCallStack
        );
    }
}
