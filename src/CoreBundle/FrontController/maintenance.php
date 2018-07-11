<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

header('HTTP/1.0 503 Service unavailable');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">
  <head>
    <meta http-equiv="Content-Language" content="de" />
     <title>This page is down for maintenance </title>
     <style type="text/css">

        .errorMessage {
          font-family: "Segoe UI", Arial, helvetica, sans-serif;
          line-height: 18px;
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
          width: 250px;
          margin-left: auto;
          margin-right: auto;
        }

     </style>
  </head>
  <body>
    <div align="center">
      <div class="errorMessage">
        Sorry! This page is down for maintenance.<br />
        Diese Seite ist zur Zeit nicht erreichbar.
      </div>
    </div>
  </body>
  </html>