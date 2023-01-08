<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use League\OAuth2\Client\Provider\GoogleUser;

/**
 * Register a new google user as a cms user
 */
interface GoogleUserRegistrationServiceInterface
{
    /**
     * @param GoogleUser $googleUser
     * @return CmsUserModel
     * @throws \Exception
     */
    public function register(GoogleUser $googleUser): CmsUserModel;

    public function update(GoogleUser $googleUser): CmsUserModel;

    public function exists(GoogleUser $googleUser): bool;
}