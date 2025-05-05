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
 * used to show and write article reviews.
 * /**/
class MTPkgComment extends MTPkgCommentCore
{
    /**
     * use this method to modify data of a comment before saving it, e.g. when
     * you have custom fields for you comment you need to fill.
     *
     * @param array $aData
     *
     * @return array
     */
    protected function AddCustomDataToCommentBeforeSave($aData)
    {
        $oGlobal = TGlobal::instance();
        $aData['title'] = $oGlobal->GetUserData('title');

        return $aData;
    }

    /**
     * Validates user input data for comment.
     *
     * @param array $aData
     *
     * @return bool
     */
    protected function ValidateCommentData($aData)
    {
        $bIsValid = parent::ValidateCommentData($aData);
        if (empty($aData['title'])) {
            $oMessageManager = TCMSMessageManager::GetInstance();
            $oMessageManager->AddMessage(TdbPkgComment::MESSAGE_CONSUMER_NAME.$aData['item_id'], 'PKG-COMMENT-MISSING-TITLE');
            $bIsValid = false;
        }
        if (!$bIsValid) {
            $this->SetSuppressRedirectAfterAction(true);
        }

        return $bIsValid;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = "
      <script type=\"text/javascript\">
        $(document).ready(function(){
        $('#comment_preview_button').toggle(function(){
          $('#comment_preview').show();
          $('#comment_form').hide();
          $('#comment_preview .text').html($('#comment_form .commenttextarea').val());
          $('#comment_preview .title').html($('#comment_form .title').val());
          $(this).html('".TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.edit'))."');
        },function(){
          $('#comment_preview').hide();
          $('#comment_form').show();
          $(this).html('".TGlobal::OutHtml(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_comment.action.preview'))."');
        });
      });
      </script>";

        return $aIncludes;
    }
}
