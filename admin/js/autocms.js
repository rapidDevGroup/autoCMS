tinymce.init({
    selector: "textarea",
    menubar : false,
    plugins: "textcolor",
    height: 150,
    statusbar: false,
    forced_root_block : "",
    mode : "textareas",
    theme : "advanced",
    toolbar: ["styleselect | fontsizeselect", "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | forecolor backcolor"]
});

$('.desc-edit').editable();

function validateCreateAuth() {
    // TODO: validate the the password matches... other rules if needed

    return true;
}