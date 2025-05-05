            if (!empty($this->sqlData['<?php echo $aFieldData['sFieldDatabaseName']; ?>_table_name'])) {
                return TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS ,$this->sqlData['<?php echo $aFieldData['sFieldDatabaseName']; ?>_table_name']);
            } else {
                return '';
            }
