// ===============================
// Pastimes Web App - JavaScript
// ===============================

document.addEventListener("DOMContentLoaded", function () {

    console.log("Pastimes system loaded successfully");

    // ===============================
    // CONFIRM DELETE (Cart + Admin)
    // ===============================
    const deleteLinks = document.querySelectorAll(".btn-delete");

    deleteLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            const confirmAction = confirm("Are you sure you want to remove this item?");
            if (!confirmAction) {
                e.preventDefault();
            }
        });
    });

    // ===============================
    // ADD TO CART FEEDBACK
    // ===============================
    const addButtons = document.querySelectorAll(".add-to-cart-btn");

    addButtons.forEach(button => {
        button.addEventListener("click", function () {
            alert("Item added to cart successfully!");
        });
    });

    // ===============================
    // AUTO-HIDE MESSAGES
    // ===============================
    const messages = document.querySelectorAll(".success-message, .error-message");

    messages.forEach(msg => {
        setTimeout(() => {
            msg.style.display = "none";
        }, 3000);
    });

    // ===============================
    // SIMPLE FORM VALIDATION
    // ===============================
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        form.addEventListener("submit", function (e) {

            const inputs = form.querySelectorAll("input[required]");
            let valid = true;

            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    valid = false;
                    input.style.border = "2px solid red";
                } else {
                    input.style.border = "1px solid #ccc";
                }
            });

            if (!valid) {
                e.preventDefault();
                alert("Please fill in all required fields.");
            }
        });
    });

});