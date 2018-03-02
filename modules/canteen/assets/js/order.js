$(function () {
    $('#dish-list input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'icheckbox_flat-green',
        increaseArea: '50%' // optional
    });

    $('.icheckbox_flat-green').closest('label').css('margin-top', '6px');
});