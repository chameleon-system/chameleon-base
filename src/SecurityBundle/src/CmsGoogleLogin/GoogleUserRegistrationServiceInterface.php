<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\Exception\RegisterUserErrorException;
use ChameleonSystem\SecurityBundle\Exception\UpdateUserErrorException;
use League\OAuth2\Client\Provider\GoogleUser;

/**
 * Register a new google user as a cms user
 */
interface GoogleUserRegistrationServiceInterface
{
    /**
     * @param GoogleUser $googleUser
     * @return CmsUserModel
     * @throws RegisterUserErrorException
     */
    public function register(GoogleUser $googleUser): CmsUserModel;

    /**
     * @param GoogleUser $googleUser
     * @return CmsUserModel
     * @throws UpdateUserErrorException
     */
    public function update(GoogleUser $googleUser): CmsUserModel;

    public function exists(GoogleUser $googleUser): bool;
}