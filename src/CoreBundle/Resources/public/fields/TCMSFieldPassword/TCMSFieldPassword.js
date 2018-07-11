/*
 * checks if password fields have the same value
 */
function checkPasswordSimilarity(field1, field2, errorMessage)
{
    if($('#'+field1).val() !== $('#'+field2).val()) {
        $('#'+field2).val('');
        toasterMessage(errorMessage,'WARNING')
    }
}
