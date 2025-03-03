    fetch("add_cart.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            userId: userId,
            productId: productId,
            quantity: quantity
        })
    })
    .then(response => response.text()) // Convert to text first
    .then(text => {
        console.log("Raw Response:", text); // Debugging
        return JSON.parse(text); // Now try to parse JSON
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Fetch Error:", error));
    document.addEventListener("DOMContentLoaded", function () {
        let addToCartButtons = document.querySelectorAll(".add-to-cart");

        addToCartButtons.forEach(button => {
            button.addEventListener("click", function () {
                let productId = this.getAttribute("data-id");
                let productName = this.getAttribute("data-name");
                let productPrice = parseInt(this.getAttribute("data-price"));
                let productImage = this.getAttribute("data-image");

                let cart = JSON.parse(localStorage.getItem("cart")) || [];

                let existingProduct = cart.find(item => item.id === productId);

                if (existingProduct) {
                    existingProduct.quantity += 1;
                } else {
                    cart.push({ id: productId, name: productName, price: productPrice, image: productImage, quantity: 1 });
                }

                localStorage.setItem("cart", JSON.stringify(cart));
                alert("A termék hozzáadva a kosárhoz!");
            });
        });
    });

