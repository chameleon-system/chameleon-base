<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * all CUSTOM user modules (ie modules that can be set by the cms) need to be derived from this class
 * note: you can force a custom user model to act like a plain user model (ie no modulechooser)
 *       by passing 'static'=>true in the pagedef to the model.
 * /**/
class TUserCustomModelBase extends TUserCustomModelBaseCore
{
}
