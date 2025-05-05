        $o<?php echo $sClassName; ?>List = $this-><?php echo $sCalledMethod; ?>;
        $o<?php echo $sClassName; ?>IDList = $o<?php echo $sClassName; ?>List->GetIdList('id', $bReturnAsCommaSeparatedString);
        return $o<?php echo $sClassName; ?>IDList;
