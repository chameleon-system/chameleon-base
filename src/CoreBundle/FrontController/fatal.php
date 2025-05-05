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

/**
 * @param string $nonEscapedString
 *
 * @return string
 */
function OutHTML($nonEscapedString)
{
    $sEscapedHTML = htmlentities($nonEscapedString, ENT_QUOTES, 'UTF-8');
    $sEscapedHTML = str_replace('=', '&#61;', $sEscapedHTML);
    $sEscapedHTML = str_replace('"', '&#92;', $sEscapedHTML);
    $sEscapedHTML = strip_tags($sEscapedHTML);

    return $sEscapedHTML;
}

if (isset($_GET['sIdentifier'])) {
    $identifier = $_GET['sIdentifier'];
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internal Server Error</title>
    <style type="text/css">
        body {
            font-family: "Segoe UI", Arial, helvetica, sans-serif;
            background-color: #e4e5e6;
            color: #23282c;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        @media (min-width: 320px) {
            h1 {
                font-size: 2em;
            }

            h2 {
                font-size: 1.5em;
            }
        }

        @media (min-width: 480px) {
            h1 {
                font-size: 3em;
            }

            h2 {
                font-size: 1.75em;
            }
        }

        @media (min-width: 600px) {
            h1 {
                font-size: 4em;
            }

            h2 {
                font-size: 2em;
            }
        }

        @media (min-width: 801px) {
            h1 {
                font-size: 5em;
            }

            h2 {
                font-size: 3em;
            }
        }

        @media (min-width: 1025px) {
            h1 {
                font-size: 6em;
            }

            h2 {
                font-size: 4em;
            }
        }

        @media (min-width: 1281px) {
            h1 {
                font-size: 7em;
            }

            h2 {
                font-size: 5em;
            }
        }
    </style>
</head>
<body>
<h1>500</h1>
<h2>INTERNAL SERVER ERROR</h2>
<p>
    <?php
    if (isset($identifier)) {
        ?>
        Error identifier: <strong><?php echo OutHTML($identifier); ?></strong>
        <?php
    }
?>
</p>
</body>
</html>
