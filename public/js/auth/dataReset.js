function handler(input)
{
    document.getElementById('sonstiges').value = input.value;
}
function handler2(select)
{
    if(select.options[select.selectedIndex].id == "sonstiges"){
        document.getElementById('sonstigesFeld').style.display = 'block';
    } else {
        document.getElementById('sonstigesFeld').style.display = 'none';
    }
}