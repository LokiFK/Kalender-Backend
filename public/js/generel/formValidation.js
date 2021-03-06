
const allInputs = document.getElementsByClassName('form-input');
for (var i = 0; i < allInputs.length; i++) {
    const data = allInputs[i];
    document.getElementById(allInputs[i].id).addEventListener('input', function() { checkInputField(data) }, false);
};

function checkInputField(inputField) {
    if (inputField.tagName == "SELECT") {
        const select = document.getElementById(inputField.id);
        if (select.children[select.selectedIndex].id == "sonstiges"){
            document.getElementById('sonstigesFeld-' + select.name).style.display = 'block';
        } else {
            document.getElementById('sonstigesFeld-' + select.name).style.display = 'none';
        }
        return;
    }

    if (inputField.id.substring(0,14) == "sonstigesFeld-") {
        document.getElementById('select-'+inputField.name.substring(14)).children[document.getElementById('select-'+inputField.name.substring(14)).children.length-1].innerHTML = inputField.value;
        document.getElementById('select-'+inputField.name.substring(14)).children[document.getElementById('select-'+inputField.name.substring(14)).children.length-1].value = inputField.value;
    }

    console.log(inputField.name);
    $.ajax({
        type: 'get', 
        url: '../../../form/validate', 
        data: JSON.parse(JSON.stringify({
            'name': inputField.name,
            'value': document.getElementsByName(inputField.name)[0].value,
            'validation': document.getElementsByName('validation-' + inputField.name)[0].value
        })),
        dataType: 'json',
        contentType : 'application/json',
        success: function (data) {
            console.log(data);
            document.getElementById('feedback-' + inputField.name).style.color = data.color;
            document.getElementById('feedback-' + inputField.name).innerHTML = data.feedback;
        }
    });
}