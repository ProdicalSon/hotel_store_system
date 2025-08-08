// js/scripts.js
document.addEventListener('DOMContentLoaded', function() {
    // Example: warn before submitting a requisition if quantity seems large
    const reqForm = document.querySelector('form[action="../controllers/requisition_controller.php"]');
    if (reqForm) {
        reqForm.addEventListener('submit', function(e) {
            const qty = parseInt(this.quantity.value || 0, 10);
            if (qty > 1000) {
                if (!confirm('You are issuing a large quantity. Continue?')) e.preventDefault();
            }
        });
    }
});