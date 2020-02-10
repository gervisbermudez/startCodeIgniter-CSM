$(document).ready(function () {
    $('#username').blur(function (event) {
        if ($('#username').val() != '' && $('#username').val() != $('#username').attr('data-value')) {
            checkvalue('user', 'username', $('#username'));
        } else {
            $('#username').removeClass('valid');
            $('#username').removeClass('invalid');
        }
    });
    $('#email').blur(function (event) {
        if ($('#email').val() != '' && $('#email').val() != $('#email').attr('data-value')) {
            checkvalue('user', 'email', $('#email'));
        } else {
            $('#email').removeClass('valid');
            $('#email').removeClass('invalid');
        }
    });
    $('form').submit(function (event) {
        if ($('.invalid').length > 0) {
            event.preventDefault();
        };
    });
});

function checkvalue(table, campo, valor) {
    jQuery.ajax({
        url: base_url + '/admin/user/checkvalue',
        type: 'POST',
        dataType: 'json',
        data: { 'table': table, 'campo': campo, 'valor': valor.val() },
        complete: function (xhr, textStatus) {
            console.log('/admin/user/checkvalue: complete');
        },
        success: function (data, textStatus, xhr) {
            if (data.result) {
                valor.addClass('invalid');
                $('#buttons').hide('slow');
            } else {
                valor.addClass('valid');
                valor.removeClass('invalid');
                if ($('.invalid').length == 0) {
                    $('#buttons').show('slow');
                }
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('/admin/user/checkvalue: error');
        }
    });
}