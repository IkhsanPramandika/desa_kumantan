<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({
            selector: 'textarea#wysiwyg',
            plugins: 'table lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link',
            height: 500,
            menubar: false,
        });
    });
</script>