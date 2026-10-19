(function () {
    "use strict";

    var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var PHONE = /^[0-9+\-\s]{7,15}$/;
    var PASS  = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>_\-]).{8,}$/;

    var IMAGE_TYPES = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    var IMAGE_EXTS  = ["jpg", "jpeg", "png", "gif", "webp"];
    var MAX_IMAGE   = 2 * 1024 * 1024;   // 2 MB

    // ---------- helpers ----------

    function wrapper(input) { return input.closest(".field"); }

    function showError(input, message) {
        var box = wrapper(input);
        if (!box) return;
        box.classList.add("invalid");
        var span = box.querySelector(".field-error");
        if (span) span.textContent = message;
        input.setAttribute("aria-invalid", "true");
    }

    function clearError(input) {
        var box = wrapper(input);
        if (!box) return;
        box.classList.remove("invalid");
        var span = box.querySelector(".field-error");
        if (span) span.textContent = "";
        input.removeAttribute("aria-invalid");
    }

    function clearAll(form) {
        Array.prototype.forEach.call(form.elements, function (el) { clearError(el); });
    }

    // Returns an error message for an optional image input, or "" if it is fine.
    function imageProblem(input) {
        if (!input || !input.files || input.files.length === 0) return "";
        var file = input.files[0];
        var ext = file.name.split(".").pop().toLowerCase();

        if (IMAGE_TYPES.indexOf(file.type) === -1 || IMAGE_EXTS.indexOf(ext) === -1) {
            return "Only JPG, PNG, GIF and WEBP images are allowed.";
        }
        if (file.size > MAX_IMAGE) {
            return "Image must be smaller than 2 MB.";
        }
        return "";
    }

    // Runs `validator(form, fail)`; blocks the submit if anything failed.
    function attach(formId, validator) {
        var form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener("submit", function (e) {
            clearAll(form);
            var firstBad = null;

            function fail(target, message) {
                var input = (typeof target === "string") ? form.elements[target] : target;
                showError(input, message);
                if (!firstBad) firstBad = input;
            }

            validator(form, fail);

            if (firstBad) {
                e.preventDefault();
                firstBad.focus();
                return;
            }

            // Loading state so the form is not submitted twice
            var button = form.querySelector('button[type="submit"]');
            if (button) {
                button.setAttribute("data-label", button.textContent);
                button.textContent = "Please wait...";
                button.disabled = true;
            }
        });

        // Remove an error as soon as the user edits that field
        form.addEventListener("input",  function (e) { clearError(e.target); });
        form.addEventListener("change", function (e) { clearError(e.target); });
        // Inputs linked with form="..." sit outside the <form> tag, so their
        // events do not reach the form: listen on each field as well.
        Array.prototype.forEach.call(form.elements, function (el) {
            el.addEventListener("input", function () { clearError(el); });
        });
    }

    // Restore buttons if the user comes back with the browser's Back button
    window.addEventListener("pageshow", function () {
        Array.prototype.forEach.call(document.querySelectorAll("button[data-label]"), function (b) {
            b.textContent = b.getAttribute("data-label");
            b.disabled = false;
        });
    });

    // ---------- forms ----------

    attach("register-form", function (f, fail) {
        var name = f.elements.customer_name.value.trim();
        if (name.length < 2) fail("customer_name", "Please enter your full name (at least 2 characters).");

        var email = f.elements.customer_email.value.trim();
        if (!EMAIL.test(email)) fail("customer_email", "Please enter a valid email address.");
        else if (email.length > 50) fail("customer_email", "Email must be 50 characters or fewer.");

        if (!PASS.test(f.elements.customer_pass.value)) {
            fail("customer_pass", "Use at least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.");
        }

        if (f.elements.customer_country.value === "") fail("customer_country", "Please select your country.");

        if (f.elements.customer_city.value.trim().length < 2) fail("customer_city", "Please enter your city.");

        if (!PHONE.test(f.elements.customer_contact.value.trim())) {
            fail("customer_contact", "Enter 7 to 15 digits (you may use +, - and spaces).");
        }

        var problem = imageProblem(f.elements.customer_image);
        if (problem) fail("customer_image", problem);
    });

    attach("login-form", function (f, fail) {
        if (!EMAIL.test(f.elements.customer_email.value.trim())) fail("customer_email", "Please enter a valid email address.");
        if (f.elements.customer_pass.value === "") fail("customer_pass", "Please enter your password.");
    });

    attach("brand-form", function (f, fail) {
        var n = f.elements.brand_name.value.trim();
        if (n.length < 2 || n.length > 100) fail("brand_name", "Brand name must be 2 to 100 characters.");
    });

    attach("category-form", function (f, fail) {
        var n = f.elements.cat_name.value.trim();
        if (n.length < 2 || n.length > 100) fail("cat_name", "Category name must be 2 to 100 characters.");
    });

    // Cart: quantity form (Task 12). Every quantity must be a whole number 1-99.
    attach("update-form", function (f, fail) {
        Array.prototype.forEach.call(f.elements, function (el) {
            if (el.name === "qty[]" && !/^[1-9][0-9]?$/.test(el.value.trim())) {
                fail(el, "Enter a whole number from 1 to 99.");
            }
        });
    });

    // Cart: remove form - at least one item must be ticked
    var removeForm = document.getElementById("remove-form");
    if (removeForm) {
        removeForm.addEventListener("submit", function (e) {
            var box = document.getElementById("cart-js-message");
            if (document.querySelectorAll('input[name="remove[]"]:checked').length === 0) {
                e.preventDefault();
                if (box) {
                    box.textContent = "Tick the items you want to remove first.";
                    box.hidden = false;
                }
            } else if (box) {
                box.hidden = true;
            }
        });
    }

    attach("product-form", function (f, fail) {
        var title = f.elements.product_title.value.trim();
        if (title.length < 2 || title.length > 200) fail("product_title", "Title must be 2 to 200 characters.");

        if (f.elements.product_cat.value === "")   fail("product_cat", "Please choose a category.");
        if (f.elements.product_brand.value === "") fail("product_brand", "Please choose a brand.");

        var price = parseFloat(f.elements.product_price.value);
        if (isNaN(price) || price <= 0) fail("product_price", "Price must be a number greater than zero.");

        if (f.elements.product_keywords.value.length > 100) fail("product_keywords", "Keywords must be 100 characters or fewer.");
        if (f.elements.product_desc.value.length > 500)     fail("product_desc", "Description must be 500 characters or fewer.");

        var problem = imageProblem(f.elements.product_image);
        if (problem) fail("product_image", problem);
    });
})();
