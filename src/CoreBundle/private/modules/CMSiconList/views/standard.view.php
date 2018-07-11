<script type="text/javascript">
    /*
     * iconList field: sets selected icon
     */
    function chooseIcon(fieldName, iconPath, iconFilename) {
        parent.document.getElementById(fieldName).value = iconFilename;
        parent.document.getElementById(fieldName + '_img').src = _url_cms + iconPath + iconFilename;
    }
</script>
<?php
echo $data['iconList'];
?>