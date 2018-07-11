<?php

// Example:
// Dumps nodes from testfile.html.
// To run: php < ex_dumpurl.php
include 'HTMLParser.class.php';
$parser = HtmlParser_ForFile('testfile.html');
// $parser = HtmlParser_ForURL ("http://yahoo.com");
while ($parser->parse()) {
    echo "-----------------------------------\r\n";
    echo 'Name='.$parser->iNodeName.';';
    echo 'Type='.$parser->iNodeType.';';
    if (NODE_TYPE_TEXT == $parser->iNodeType || NODE_TYPE_COMMENT == $parser->iNodeType) {
        echo "Value='".$parser->iNodeValue."'";
    }
    echo "\r\n";
    if (NODE_TYPE_ELEMENT == $parser->iNodeType) {
        echo 'ATTRIBUTES: ';
        $attrValues = $parser->iNodeAttributes;
        $attrNames = array_keys($attrValues);
        $size = count($attrNames);
        for ($i = 0; $i < $size; ++$i) {
            $name = $attrNames[$i];
            echo $attrNames[$i].'="'.$attrValues[$name].'" ';
        }
    }
    echo "\r\n";
}
