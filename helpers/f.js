function checkInputField(inputFieldName) {
document.getElementsByClassName('')
    $.get(
        '../../../form/validate',
        {
            'name': inputFieldName,
            'value': document.getElementsByName(inputFieldName)[0].value,
            'validation': document.getElementsByName('validation-' + inputFieldName)[0].value
        }
    ).done(function(data) {
        alert(data);
    });

    $.ajax({
        type: 'get', 
        url: '../../../form/validate', 
        data: JSON.stringify({
            'name': inputFieldName,
            'value': document.getElementsByName(inputFieldName)[0].value,
            'validation': document.getElementsByName('validation-' + inputFieldName)[0].value
        }),
        dataType: 'json',
        contentType : 'application/json',
        success: function (data) {
            console.log(data);
        }
    });
}