# Table Editor

## Extending a group of fields with additional functionality

It is possible to add additional HTML + assets to one ore a group of fields
The ouput will be below the field. Examples are: text manipulations like AI features, translations, previews etc.

Create a service like this example:

```php 

<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\FieldExtensionInterface;
use \TCMSField;
use \TdbCmsFieldType;

class FieldExtensionExampleService extends FieldExtensionServiceAbstract implements FieldExtensionInterface
{
    public function getFieldExtensionHtml(TCMSField $field): string
    {
        $fieldDefintion = $field->oDefinition;
        $fieldType = $fieldDefintion->GetFieldType();

        if (true === $this->isFieldTypeText($fieldType)) {
            $ajaxUrl = '/cms?tableid='.$field->oDefinition->fieldCmsTblConfId.'&pagedef=tableeditor&id='.$field->recordId.'&module_fnc%5Bcontentmodule%5D=ExecuteAjaxCall&_fnc=getValueForFieldExtension&callFieldMethod=1&_fieldName='.$field->name.'&callingFieldExtension=ChameleonSystem\CoreBundle\Service\FieldExtensionExampleService';
            
            // load a value from the database via ajax, or get the current field value. For wysiwyg you need to call the method, that extracts the current editor value for example.
            return '<button class="btn btn-sm btn-secondary mt-2 myCustomButton" type="button" data-ajax-url="'.$ajaxUrl.'">Do something</button>';
        }
        
        return '';
    }

    public function getHtmlHeadIncludes(TCMSField $field): array
    {
        return [];
    }

    public function getHtmlFooterIncludes(TCMSField $field): array
    {
        $html = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">My Demo Modal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Do something</button>
      </div>
    </div>
  </div>
</div>
';
        
        $script = "<script>
            document.addEventListener('DOMContentLoaded', function() {
              var buttons = document.querySelectorAll('.myCustomButton');
            
              buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                  var url = this.getAttribute('data-ajax-url');
                  var xhr = new XMLHttpRequest();
                  xhr.open('GET', url, true);
                  xhr.onreadystatechange = function() {
                    if(xhr.readyState == 4 && xhr.status == 200) {
                      document.querySelector('#myModal .modal-body').innerHTML = JSON.parse(xhr.responseText);
                      showModal();
                    }
                  };
                  xhr.send();
                });
              });
            });
            
            function showModal() {
              var modal = new bootstrap.Modal(document.getElementById('myModal'), {
                keyboard: false
              });
              modal.show();
            }
        </script>";
        
        $includes[] = $html;
        $includes[] = $script;
        
        return $includes;
    }

    public function getFieldValue(TCMSField $field): string
    {
        $table = $field->oTableRow->table;
        $recordId = $field->recordId;
        
        if ('cms_tpl_page' === $table && 'meta_description' == $field->name) {
            // return the page name for meta description manipulation 
            return $field->oTableRow->sqlData['name'];
        }

        return $field->data;
    }
}

```

Tag the service with "chameleon_system_core.field_extension" to add it to all field renderings.
(theoretically all services implementing the FieldExtensionInterface should be automatically tagged, but that didn't work yet.)

```xml
    <service id="chameleon_system_core.service.field_extension_example_service" class="ChameleonSystem\CoreBundle\Service\FieldExtensionExampleService">
      <tag name="chameleon_system_core.field_extension"/>
    </service>
```

And you are done.
