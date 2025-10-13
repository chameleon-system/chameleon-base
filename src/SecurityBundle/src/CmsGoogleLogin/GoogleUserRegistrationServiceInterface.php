<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\SecurityBundle\Interfaces\ChameleonCmsUserInterface;
use ChameleonSystem\SecurityBundle\Exception\RegisterUserErrorException;
use ChameleonSystem\SecurityBundle\Exception\UpdateUserErrorException;
use League\OAuth2\Client\Provider\GoogleUser;

/**
 * Register a new google user as a cms user.
 */
interface GoogleUserRegistrationServiceInterface
{
    /**
     * @throws RegisterUserErrorException
     */
    public function register(GoogleUser $googleUser): ChameleonCmsUserInterface;

    /**
     * @throws UpdateUserErrorException
     */
    public function update(GoogleUser $googleUser): ChameleonCmsUserInterface;

    public function exists(GoogleUser $googleUser): bool;
}
