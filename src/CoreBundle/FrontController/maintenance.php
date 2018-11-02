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
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>This page is down for maintenance</title>
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

            p {
                font-size: 1em;
            }
        }

        @media (min-width: 480px) {
            h1 {
                font-size: 3em;
            }

            h2 {
                font-size: 1.75em;
            }

            p {
                font-size: 1em;
            }
        }

        @media (min-width: 600px) {
            h1 {
                font-size: 4em;
            }

            h2 {
                font-size: 2em;
            }

            p {
                font-size: 1.25em;
            }
        }

        @media (min-width: 801px) {
            h1 {
                font-size: 5em;
            }

            h2 {
                font-size: 3em;
            }

            p {
                font-size: 2em;
            }
        }

        @media (min-width: 1025px) {
            h1 {
                font-size: 6em;
            }

            h2 {
                font-size: 4em;
            }

            p {
                font-size: 2.25em;
            }
        }

        @media (min-width: 1281px) {
            h1 {
                font-size: 7em;
            }

            h2 {
                font-size: 5em;
            }

            p {
                font-size: 2.5em;
            }
        }
    </style>
</head>
<body>
<div class="center">
    <div>
        <h1>503</h1>
        <h2>Sorry! This page is down for maintenance.</h2>
        <p>
            Try again later.
        </p>
    </div>
</div>
</body>
</html>