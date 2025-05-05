<title><?php echo TGlobal::OutHTML($data['sTitle']); ?></title>
<?php if ($sCanonical) {
    ?>
<link rel="canonical" href="<?php echo $sCanonical; ?>"/> <?php echo "\n";
} ?>
<?php
foreach ($data['aMetaData'] as $metaType => $metaData) {
    foreach ($metaData as $key => $content) {
        if ('http-equiv' != $metaType) {
            $content = TGlobal::OutHTML($content);
        }
        if (strlen($content) > 0) {
            echo '<meta '.TGlobal::OutHTML($metaType).'="'.TGlobal::OutHTML($key).'" content="'.$content."\" />\n";
        }
    }
}
?>
<?php if (!empty($data['sCustomHeaderData'])) {
    echo $data['sCustomHeaderData'];
} ?>