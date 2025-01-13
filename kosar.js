// Select all quantity input fields and remove buttons
document.addEventListener("DOMContentLoaded", () => {
    const cartItems = document.querySelectorAll(".card");
    const summaryTotal = document.querySelector(".card-text strong");

    // Function to calculate and update total price
    function updateTotal() {
        let total = 0;
        cartItems.forEach(item => {
            const priceElement = item.querySelector(".card-text strong");
            const quantityElement = item.querySelector("input[type='number']");
            const price = parseFloat(priceElement.textContent.replace(" Ft", "").replace(",", ""));
            const quantity = parseInt(quantityElement.value, 10);

            total += price * quantity;
        });

        // Update total price in the summary
        summaryTotal.textContent = `${total.toLocaleString()} Ft`;
    }

    // Handle quantity change
    cartItems.forEach(item => {
        const quantityInput = item.querySelector("input[type='number']");
        quantityInput.addEventListener("input", () => {
            // If quantity is less than 1, reset it to 1
            if (quantityInput.value < 1) quantityInput.value = 1;
            updateTotal();
        });

        // Handle remove button
        const removeButton = item.querySelector(".btn-danger");
        removeButton.addEventListener("click", () => {
            item.remove();
            updateTotal();
        });
    });

    // Initial calculation
    updateTotal();
});
