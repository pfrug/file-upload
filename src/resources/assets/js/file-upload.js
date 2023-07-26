
$(document).ready(function () {
    $('.btn_delete_image').on('click', markImageToDelete);
    $('.btn_cancel_delete_image').on('click', cancelImageDelete);
    $('.file_image_preview').on('change', changeImageHandler);
});

function markImageToDelete(e)
{
    e.preventDefault();
    var container = $(e.target).closest('.container_remove_image');
    container.find('.h_image_delete').val(1);
    showCancelDeleteImage(container);
}

function cancelImageDelete(e)
{
    e.preventDefault();
    var container = $(e.target).closest('.container_remove_image');
    container.find('.h_image_delete').val(0);
    showDeleteImage(container);
}

function showDeleteImage(container)
{
    container.find('.cancel_img_remove_container').hide();
    container.find('.img_remove_container').show();
}

function showCancelDeleteImage(container)
{
    container.find('.cancel_img_remove_container').show();
    container.find('.img_remove_container').hide();
}

function changeImageHandler(e)
{
    var input = e.target ;
    var container = $(e.target).closest('.container_remove_image')
    var img = container.find('.img_preview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            img.show();
            img.attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
        showDeleteImage(container);
    }
}
