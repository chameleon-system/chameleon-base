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
 * @deprecated since 6.2.0 - no longer used.
 */
class CMSSearch extends TModelBase
{
    public function &Execute()
    {
        $this->data = parent::Execute();
        $this->GetIndexablePortals();

        return $this->data;
    }

    /**
     * sets HTML options list of all portals where index is set to "on".
     */
    protected function GetIndexablePortals()
    {
        $portalQuery = "SELECT * FROM `cms_portal` WHERE `index_search` = '1' ORDER BY `name`";
        $oPortalList = new TCMSPortalList();
        /** @var $oRecordList TCMSPortalList */
        $oPortalList->sTableName = 'cms_portal';
        $oPortalList->Load($portalQuery);

        $options = '';
        while ($oPortal = $oPortalList->Next()) {
            /** @var $oPortal TCMSPortal */
            $options .= '<option value="'.TGlobal::OutHTML($oPortal->id).'">'.TGlobal::OutHTML($oPortal->GetName())."</option>\n";
        }

        $this->data['portalOptions'] = $options;
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('BuildIndex', 'PublishIndex');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * starts indexing.
     */
    public function BuildIndex()
    {
        $this->SetTemplate('CMSSearch', 'index');
        echo '<!DOCTYPE html>
    <html>
      <head>
        <title>Chameleon CMS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
         <script type="text/javascript">
         $("body").attr({ scrollTop: $("body").attr("scrollHeight") });
         </script>
        <meta http-equiv="Content-Language" content="de" />
        <link href="/chameleon/blackbox/themes/standard/css/layout.css" rel="stylesheet" type="text/css" />
        <link href="/chameleon/blackbox/bootstrap/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="'.TGlobal::GetPathTheme().'/coreui/css/coreui-standalone.min.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="/chameleon/blackbox/themes/standard/css/global.css" rel="stylesheet" type="text/css" />             
        <link href="/chameleon/blackbox/bootstrap/css/glyph-icons.css" media="screen" rel="stylesheet" type="text/css" />
      </head>
       <body style="background-color: #fff;">
         <div style="padding: 10px 0px 10px 0px;">
           <h1>'.TGlobal::OutHTML('Index wird aufgebaut...').'</h1>
           <br>
           <table class="table table-striped table-bordered">
           ';
        @flush();
        @ob_flush();

        $sPortalID = $this->global->GetUserData('cms_portal_id');

        $oSearchIndex = new TCMSSearchIndex();
        /** @var $oSearchIndex TCMSSearchIndex */
        $oSearchIndex->BuildIndex($sPortalID);
        echo '</table>
      <h1>'.TGlobal::OutHTML('Fertig...').'</h1>
        </div>
      </body>
      </html>
      ';
    }

    /**
     * copies the new index from the temp table to the live table.
     */
    public function PublishIndex()
    {
        if ($this->global->UserDataExists('cms_portal_id')) {
            $portalID = $this->global->GetUserData('cms_portal_id');
            $oSearchIndex = new TCMSSearchIndex();
            /** @var $oSearchIndex TCMSSearchIndex */
            $oSearchIndex->CopyNewPortalIndex($portalID);
        }

        return true;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jQueryUI/ui.core.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-form-4.2.2/jquery.form.min.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/cms.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }
}
