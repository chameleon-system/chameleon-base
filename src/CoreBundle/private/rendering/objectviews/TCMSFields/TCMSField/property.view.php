    /**
     * <?php echo $sFieldFullName."\n"; ?>
<?php
foreach ($aFieldDesc as $sLine) {
    echo "      * {$sLine}\n";
}
     ?>
     * @var <?php echo $sFieldType."\n"; ?>
     */
    <?php echo $sFieldVisibility; ?> $<?php echo $sFieldName; ?> = <?php echo $sFieldDefaultValue; ?>;
