$(function() {

    var imagePreview = false;

    $('.form-task .btn-preview').on('click', function() {
        var formData = $('.form-task').serializeArray();

        var $modal = $('#previewModal').modal();

        $.post('/tasks/preview', formData, function(response) {
            $modal.find('.modal-body').html(response);
            imagePreview && $modal.find('.modal-body img')
                .attr('src', imagePreview)
                .css({'max-width': 320, 'max-height': 240});

            $modal.modal('show');
        });
    });

    $('.form-task #inputPicture').on('change', function() {
        var fr = new FileReader;

        fr.onload = function() {
            imagePreview = fr.result;
        };

        if (this.files[0].type.indexOf('image') >= 0) {
            fr.readAsDataURL(this.files[0]);
        } else imagePreview = false;
    });
});