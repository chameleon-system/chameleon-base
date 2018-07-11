<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error', true, 500);
  function OutHTML($nonEscapedString)
  {
      $sEscapedHTML = htmlentities($nonEscapedString, ENT_QUOTES, 'UTF-8');
      $sEscapedHTML = str_replace('=', '&#61;', $sEscapedHTML);
      $sEscapedHTML = str_replace('"', '&#92;', $sEscapedHTML);
      $sEscapedHTML = strip_tags($sEscapedHTML);

      return $sEscapedHTML;
  }
    $sIdentifier = urldecode($_GET['sIdentifier']);
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?><!DOCTYPE html>
<html lang="en">
        <head>
           <title>The System Crashed Fatal!</title>
           <style type="text/css">
              body {
                font-family: "Segoe UI", Arial, helvetica, sans-serif;
                text-align:center;
                line-height: 18px;
              }
              .errorMessage {
                background: #F8F2AA url(/chameleon/blackbox/images/nav_icons/notice.png) no-repeat 5px 9px;
                color: #0B224B;
                border: 2px solid #FFB608;
                padding-left: 45px;
                padding-right: 10px;
                padding-top: 5px;
                padding-bottom: 5px;
                font-weight: bold;
                font-size: 12px;
                min-height: 40px;
                display: block;
                margin-top: 100px;
              }
           </style>
        </head>
        <body>
          <div align="center">
            <div class="errorMessage">
              The System Crashed Fatal!<br />
              <br />
              Your Identifier: <strong><?=OutHTML($sIdentifier); ?></strong><br />
              <br />
              The responsible developer is notified about your error via e-mail.<br />
            </div>
          </div>
        </body>
      </html>