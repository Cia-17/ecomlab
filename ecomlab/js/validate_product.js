document.addEventListener("DOMContentLoaded", function () {
    var form = document.querySelector("form[enctype='multipart/form-data']");
    if (!form) return;

    var priceInput = form.querySelector("input[name='product_price']");
    var imageInput = form.querySelector("input[name='product_image']");
    var message    = document.getElementById("productFormMessage");

    if (!message) {
        message = document.createElement("p");
        message.id = "productFormMessage";
        form.parentNode.insertBefore(message, form.nextSibling);
    }

    form.addEventListener("submit", function (e) {
        var ok = true;
        message.textContent = "";

        if (priceInput && parseFloat(priceInput.value) <= 0) {
            message.textContent = "Price must be greater than zero.";
            ok = false;
        }
        
        if (ok && imageInput && imageInput.files.length > 0) {
            var file = imageInput.files[0];
            var allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];

            if (allowedTypes.indexOf(file.type) === -1) {
                message.textContent = "Only JPG, PNG, GIF and WEBP images are allowed.";
                ok = false;
            } else if (file.size > 2 * 1024 * 1024) {
                message.textContent = "Image must be smaller than 2 MB.";
                ok = false;
            }
        }

        if (!ok) {
            e.preventDefault();
        }
    });
});