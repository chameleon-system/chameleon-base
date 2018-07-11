            if (!empty($this->sqlData['<?=$aFieldData['sFieldDatabaseName']; ?>_table_name'])) {
                return TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS ,$this->sqlData['<?=$aFieldData['sFieldDatabaseName']; ?>_table_name']);
            } else {
                return '';
            }
