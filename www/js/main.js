jQuery(function () {

    $('[role="limit_change"]').on({
        'change': updateWidth,
        'keyup': function (e) {
            var field = $(e.target);
            var val = field.val();
            if (val < 5) {
                field.val(5);
            } else if (val > 2000) {
                field.val(2000);
            }
        },
        'keydown': updateWidth,
    });
    $('[role="limit_change"]').change();
    $('[role="limit_change"]').data('val', -1);
    
    $('[data-role="login"]').on('submit', function (e) {
        e.preventDefault();
        var form = $(e.target);
        var dialog = $('<div class="dialog">Logging in...</div>').dialog({
            modal: true,
            resizable: false,
            draggable: false,
            buttons: {
            },
            closeOnEscape: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            }
        });        
        $.ajax({
            method: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function (data) {
                dialog.remove();
                window.location = form.data('redirect-url');                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                dialog.remove();
                var data = jQuery.parseJSON(xhr.responseText);
                if (typeof data.errors !== 'undefined') {
                    if (typeof data.errors.global !== 'undefined') {
                        showGlobalMessage(data.errors.global, 'danger');
                    }
                } else {
                    showGlobalMessage('Unknown error', 'error');
                }
            }
        });
    });    
    
    $('[data-role="delete"]').on('click', function (e) {
        e.preventDefault();
        var link = $(e.currentTarget);
        $.ajax({
            method: "DELETE",
            url: link.attr('href'),
            dataType: 'json',
            success: function (data) {
                $('.comments .cdata, .comments .empty').remove();
                $('.comments').prepend(data.comments);
                $('.pages').replaceWith(data.pagination);
                showGlobalMessage('Comment deleted succesfully', 'success');
            }
        });
    });
    
    $('[data-role="limit-save"]').on('submit', function (e) {
        e.preventDefault();
        var form = $(e.target);
        $.ajax({
            method: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function (data) {
                $('.comments .comment, .comments .empty').remove();
                $('.comments').prepend(data.comments);
                $('.pages').replaceWith(data.pagination);
            }
        });
    });

    $('[data-role="comment_submit"]').on('submit', function (e) {
        e.preventDefault();
        var form = $(e.target);
        form.find('[data-role="error"]').addClass('hidden');
        var dialog = $('<div class="dialog">Submiting comment...</div>').dialog({
            modal: true,
            resizable: false,
            draggable: false,
            buttons: {
            },
            closeOnEscape: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            }
        });
        $.ajax({
            method: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function (data) {
                dialog.remove();
                showGlobalMessage('Comment was added', 'success');
                if (data.replace || ($('.comments .comment').length === 0)) {
                     $('.comments .comment, .comments .empty').remove();
                     $('.comments').prepend(data.comments);
                } else {
                     $('.comments').first().append(data.comments);
                }
                form.find('input, textarea').val('');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                dialog.remove();
                var data = jQuery.parseJSON(xhr.responseText);
                if (typeof data.errors !== 'undefined') {
                    if (typeof data.errors.global !== 'undefined') {
                        showGlobalMessage(data.errors.global, 'danger');
                        delete(data.errors.global);
                    }
                    for (var name in data.errors) {
                        var error_block = form.find('[data-role="error"][data-for="' + name + '"]');
                        error_block.text(data.errors[name]);
                        error_block.removeClass('hidden');
                    }
                } else {
                    showGlobalMessage('Unknown error', 'error');
                }
            }
        });
    });


});


function showGlobalMessage(content, type) {
    var msg = $('<div class="alert alert-' + type + ' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + content + '</div>');
    $('[data-role="messages"]').append(msg);
    msg.delay(50000).hide('blind', {}, 500);
}

function updateWidth(e) {
    var field = $(e.target);
    var val = field.val();
    var len = val.toString().length + 1;
    field.css('width', len.toString() + 'em');            
    $('[role="limit_change"]').data('val', 1000);
    setTimeout(waitUntilChangesFinished, 1000);
}

function waitUntilChangesFinished() {
    var val = $('[role="limit_change"]').data('val');
    if (val > 0) {
        $('[role="limit_change"]').data('val', null);
        setTimeout(waitUntilChangesFinished, 1000);
    } else if (val == -1) {    
        $('[role="limit_change"]').data('val', null);
    } else {
        $($('[role="limit_change"]')[0].form).submit();
    }
}