<?php
        use yii\helpers\Url;
        ?>
                
$('button[data-event=\'showImageDialog\']').attr('data-toggle', 'image').removeAttr('data-event');
$(document).delegate('button[data-toggle=\'image\']', 'click', function() {
$('#modal-image').remove();
        $(this).parents('.note-editor').find('.note-editable').focus();
        $.ajax({
        url: '<?= Url::to(['/filemanager/image/index'])?>',
                dataType: 'html',
                beforeSend: function() {
                $('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                        $('#button-image').prop('disabled', true);
                },
                complete: function() {
                $('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
                        $('#button-image').prop('disabled', false);
                },
                success: function(html) {
                $('body').append('<div id="modal-image" class="modal">' + html + '</div>');
                        $('#modal-image').modal('show');
                }
        });
});
// Image Manager
$(document).delegate('a[data-toggle=\'image\']', 'click', function (e) {
        e.preventDefault();
        var element = this;
        $(element).popover({
        html: true,
        placement: 'right',
        trigger: 'manual',
        content: function () {
        return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
        }
        });
        $(element).popover('toggle');
        $('#button-image').on('click', function () {
        $('#modal-image').remove();
        var target = $(element).parent().find('input').attr('id');
        var thumb = $(element).attr('id');
        $.ajax({
        url: '<?= Url::to(['/filemanager/image/index'])?>&target=' + target + '&thumb=' + thumb,
                dataType: 'html',
                beforeSend: function () {
                $('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                        $('#button-image').prop('disabled', true);
                },
                complete: function () {
                $('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
                        $('#button-image').prop('disabled', false);
                },
                success: function (html) {
                $('body').append('<div id="modal-image" class="modal">' + html + '</div>');
                        $('#modal-image').modal('show');
                }
        });
        $(element).popover('hide');
});
        $('#button-clear').on('click', function () {
        $(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));
        $(element).parent().find('input').attr('value', '');
        $(element).parent().find('input').trigger('change');
        $(element).popover('hide');
});
        });