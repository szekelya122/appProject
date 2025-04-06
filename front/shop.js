document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", async (event) => {
        const productId = event.target.dataset.productId;
        const quantity = 1; 

        const response = await fetch("../backend/add_to_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ productId, quantity })
        });

        const result = await response.json();
        alert(result.message || result.error);
    });
});
