tinymce.init({
    selector: "textarea",
    menubar : false,
    plugins: "textcolor",
    height: 200,
    statusbar: false,
    toolbar: ["styleselect | fontsizeselect", "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | forecolor backcolor"]
});

function validateCreateAuth() {
    // TODO: validate tht the password matches... other rules if needed

    return true;
}