tinymce.init({
    selector: "textarea",
    menubar : false,
    plugins: "textcolor",
    height: 150,
    statusbar: false,
    forced_root_block : "",
    toolbar: ["styleselect | fontsizeselect", "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | forecolor backcolor"]
});

$('.desc-edit').editable();

console.log('loaded');

function validateCreateAuth() {
    // TODO: validate tht the password matches... other rules if needed

    return true;
}