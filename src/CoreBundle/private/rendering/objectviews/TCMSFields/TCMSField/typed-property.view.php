    /**
     * <?php echo $sFieldFullName."\n"; ?>
<?php
foreach ($aFieldDesc as $sLine) {
    echo "      * {$sLine}\n";
}
?>
     */
    <?php echo $sFieldVisibility; ?> <?php echo $sFieldType?> $<?php echo $sFieldName; ?> = <?php echo $sFieldDefaultValue; ?>;
