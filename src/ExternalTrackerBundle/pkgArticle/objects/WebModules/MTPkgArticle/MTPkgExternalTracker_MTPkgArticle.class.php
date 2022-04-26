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
 * @psalm-suppress UndefinedClass - AutoParent does not seem to exist at scan-time.
 */
class MTPkgExternalTracker_MTPkgArticle extends MTPkgExternalTracker_MTPkgArticleAutoParent
{
    public function Init()
    {
        parent::Init();
        // Add view count
        $oActiveArticle = &TdbPkgArticle::GetActiveInstance();
        // extenral tracker
        if (is_object($oActiveArticle)) {
            TdbPkgExternalTrackerList::GetActiveInstance()->AddStateData('oPkgArticle', $oActiveArticle);
        }
    }
}
