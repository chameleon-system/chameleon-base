    /**
     * <?php echo $sFieldFullName."\n"; ?>
<?php
foreach ($aFieldDesc as $sLine) {
    echo "      * {$sLine}\n";
}
?>
     * @var <?php echo $sFieldType."\n"; ?>
     */
    <?= $sFieldVisibility; ?> $<?= $sFieldName; ?> = <?= $sFieldDefaultValue; ?>;
