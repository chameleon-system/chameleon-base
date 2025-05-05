<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TUserCustomModelBaseCore extends TUserModelBase
{
    /**
     * add your custom methods as array to $this->methodCallAllowed here
     * to allow them to be called from web.
     *
     * @return void
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['GenerateModuleNavigation'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * use this method to set your module based custom navigation
     * you need to handle the navigation li classes to identify firstNode,
     * lastNode and activeNode in your method.
     *
     * @return string - html ul,li
     */
    public function GenerateModuleNavigation()
    {
        $this->data['sModuleNavigation'] = '';
    }
}
