var ref_name = "topics";
var button_id = 0;

function ShowErrorMessage(message)
{
    alert(message);
}

function Topics_CallAddItem(source, result_area)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        title_en: $form.find("input[name='add_title_en']").val(),
        title_ru: $form.find("input[name='add_title_ru']").val(),
        title_uk: $form.find("input[name='add_title_uk']").val(),
        ref_name: ref_name
    } );
    posting.done(function(data){
        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $(result_area).empty().load("ref_topics/ref.topics.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            // Some errors, show message!
            $( source ).dialog( "close" );
        }
    });
}

function Topics_CallLoadItem(destination, id) // номер записи, целевая форма
{
    url = 'ref_topics/ref.topics.action.getitem.php';
    var getting = $.get(url, {
        id: id,
        ref: ref_name
    });

    var $form = $(destination).find('form');

    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            // загружаем данные в поля формы
            $form.find("input[name='edit_title_en']").val( result['data']['title_en'] );
            $form.find("input[name='edit_title_ru']").val( result['data']['title_ru'] );
            $form.find("input[name='edit_title_uk']").val( result['data']['title_uk'] );
        } else {
            // ошибка загрузки
        }
    });
}

function Topics_CallUpdateItem(source, id, result_area)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        title_en: $form.find("input[name='edit_title_en']").val(),
        title_ru: $form.find("input[name='edit_title_ru']").val(),
        title_uk: $form.find("input[name='edit_title_uk']").val(),
        ref_name: ref_name,
        id: id
    } );
    posting.done(function(data){
        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $(result_area).empty().load("ref_topics/ref.topics.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            // Some errors, show message!
            $( source ).dialog( "close" );
        }
    });
}

function Topics_CallRemoveItem(target, id, result_area)
{
    url = 'ref_topics/ref.topics.action.removeitem.php?ref='+ref_name;
    var getting = $.get(url, {
        ref_name: ref_name,
        id: id
    });
    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            $(result_area).empty().load("ref_topics/ref.topics.action.list.php?ref="+ref_name);
            $( target ).dialog( "close" );
        } else {
            // удаление невозможно
            ShowErrorMessage(result['message']); // Невозможно удалить топик, вероятно в нем есть статьи!
            $( target ).dialog( "close" );
        }
    });

}
