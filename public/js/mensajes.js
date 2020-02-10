jQuery(document).ready(function ($) {
    fnSetMessageEvent();
    $('.folder').click(function (event) {
        fnToggleFormComposeMenssage('hide');
        var strIDFolder = $(this).attr('data-folder');
        $('.current-folder').html($(this).attr('data-tooltip'));
        fnGetFolder(strIDFolder);
    });
    $('#mail-create').click(function (event) {
        fnToggleFormComposeMenssage('show');
    });
    $('.compose-msg-head').click(function (event) {
        $(this).parents('.compose-msg-cont').toggleClass('close');
    });
    $('.close-compose-msg').click(function (event) {
        $(this).parents('.compose-msg-cont').hide('slow');
    });
    $('.chips').material_chip({
        'data': [],
        'placeholder': 'To:',
        'secondaryPlaceholder': '',
        'inputname': '_to',
    });
});
var fnMoveMessageTo = function (strIDFolder, fnCallback) {
    if ($('.collection').attr('data-active-folder') != strIDFolder) {
        var strIdMessage = $('#mensaje-id').attr('data-id');
        if (strIdMessage) {
            $.ajax({
                url: base_url + 'admin/mensajes/update_messagefolder_byajax/',
                type: 'POST',
                dataType: 'json',
                data: { 'id': strIdMessage, 'folder': strIDFolder },
            })
                .done(function (json) {
                    if (json.result) {
                        $('.collection-item[data-id=' + strIdMessage + ']').hide('slow');
                        $('.preview').html('');
                        if (typeof (fnCallback) == 'function') {
                            fnCallback();
                        };
                    } else {
                        Materialize.toast('Ocurrio un error', 4000);
                    }
                })
                .fail(function () {
                    console.log("fnMoveMessageTo error");
                })
                .always(function () {
                    console.log("fnMoveMessageTo complete");
                });
        };
    };
}
var fnGetFolder = function (strIDFolder, fnCallback) {
    $.ajax({
        url: base_url + 'admin/mensajes/getfolder/' + strIDFolder,
        type: 'POST',
        dataType: 'html'
    })
        .done(function (html) {
            $('.collection').html(html);
            $('.collection').attr('data-active-folder', strIDFolder);
            $('.preview').html('');

            fnSetMessageEvent();
            if (typeof (fnCallback) == 'function') {
                fnCallback();
            };
        })
        .fail(function () {
            console.log("fnGetFolder error");
        })
        .always(function () {
            console.log("fnGetFolder complete");
        });
}
var fnSetMessageEvent = function () {
    $('.message').click(function (event) {
        var id_mensaje = $(this).attr('data-id');
        var element = this;
        $.ajax({
            url: base_url + 'admin/mensajes/get_mensaje_by_ajax/',
            type: 'POST',
            dataType: 'html',
            data: { 'id_mensaje': id_mensaje },
        })
            .done(function (html) {
                $('.preview').html(html);
                $(element).find('.new.badge').hide('slow');
                fnSetMessageOptionsEvent();
            })
            .fail(function () {
                console.log("fnSetMessageEvent error");
            })
            .always(function () {
                console.log("fnSetMessageEvent complete");
            });
        event.preventDefault();
    });
}
var fnSetMessageOptionsEvent = function () {
    $('.opt-reply').click(function (event) {
        $('.chip').remove();
        var strSubject = $('#_subject').html();
        var strTo = $('#from').html();
        fnToggleFormComposeMenssage('show');
        $(fnChipEmailTemplate(strTo)).insertBefore('#_to');
        $('#subject').val('FW: ' + strSubject);

    });
    $('.opt-delete').click(function (event) {
        fnMoveMessageTo('4', function () {
            Materialize.toast('Eliminado!', 8000);
        });
    });
    $('.opt-archive').click(function (event) {
        fnMoveMessageTo('2', function () {
            Materialize.toast('Archivado!', 8000);
        });
    });
    $('.opt-important').click(function (event) {
        fnMoveMessageTo('6', function () {
            Materialize.toast('Marcado como importante!', 8000);
        });
    });
}
var fnChipEmailTemplate = function (strEmail) {
    return strChipTemplate = '<div class="chip"><span class="txt">' + strEmail + '</span><i class="close material-icons">close</i></div>';
}
var fnToggleFormComposeMenssage = function (toggle) {
    if (toggle == 'show') {
        $('.chip').remove();
        $('.compose-msg-head').removeClass('close');
        $('.compose-msg-cont').show('slow');
    } else {
        $('.compose-msg-cont').hide();
    }
    var fnSetSaveDraftsEvents = function () {
        $('#mensaje').keyup(function (event) {
            console.log(event);
            var objFuntionsEvents = {
                //default: update or create draft
                'default': function () {
                    var strTargetUrl = 'admin/mensajes/updatedraft/';
                    if ('' == 'new') {
                        strTargetUrl = 'admin/mensajes/setdraft/'
                    };
                    $.ajax({
                        url: base_url + strTargetUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: { param1: 'value1' },
                    })
                        .done(function () {
                            console.log("success");
                        })
                        .fail(function () {
                            console.log("error");
                        })
                        .always(function () {
                            console.log("complete");
                        });

                }
            }
            if (objFuntionsEvents[event.keyCode]) {
                objFuntionsEvents[event.keyCode]();
            } else {
                objFuntionsEvents['default']();
            }
        });
    }
}