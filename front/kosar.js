document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = "http://localhost:8080/cart"; // Change to your actual backend URL

    // Fetch and display cart items from the database
    const fetchCart = async () => {
        try {
            const response = await fetch(apiUrl);
            const cart = await response.json();
            displayCartItems(cart);
        } catch (error) {
            console.error("Error fetching cart:", error);
        }
    };

    // Add product to cart in the database
    const addToCart = async (product) => {
        try {
            const response = await fetch(`${apiUrl}/add`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(product),
            });

            if (response.ok) {
                alert("A termék sikeresen hozzáadva a kosárhoz!");
                fetchCart();
            } else {
                alert("Hiba történt a kosár frissítésekor.");
            }
        } catch (error) {
            console.error("Error adding to cart:", error);
        }
    };

    // Remove item from cart in the database
    const removeFromCart = async (productId) => {
        try {
            const response = await fetch(`${apiUrl}/remove/${productId}`, {
                method: "DELETE",
            });

            if (response.ok) {
                fetchCart();
            } else {
                alert("Hiba történt a termék eltávolításakor.");
            }
        } catch (error) {
            console.error("Error removing from cart:", error);
        }
    };

    // Display cart items
    const displayCartItems = (cart) => {
        const cartContainer = document.querySelector("#cartItems");
        const cartSummary = document.querySelector("#cartSummary");
        if (!cartContainer || !cartSummary) return;

        cartContainer.innerHTML = "";
        if (cart.length === 0) {
            cartContainer.innerHTML = `<p class="text-warning">A kosár üres.</p>`;
            cartSummary.innerHTML = "Összesen: 0 Ft";
            return;
        }

        let total = 0;
        cart.forEach((item) => {
            total += item.price * item.quantity;
            cartContainer.innerHTML += `
                <div class="card mb-3 bg-dark border-warning">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="${item.image}" class="img-fluid rounded-start" alt="${item.name}">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title text-warning">${item.name}</h5>
                                <p class="card-text">Ár: ${item.price.toLocaleString()} Ft</p>
                                <p class="card-text">Mennyiség: ${item.quantity}</p>
                                <button class="btn btn-danger" data-id="${item.id}">Eltávolítás</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        cartSummary.innerHTML = `Összesen: ${total.toLocaleString()} Ft`;

        document.querySelectorAll(".btn-danger").forEach((button) => {
            button.addEventListener("click", () => removeFromCart(button.getAttribute("data-id")));
        });
    };

    // Handle add-to-cart button clicks
    document.querySelectorAll(".add-to-cart").forEach((button) => {
        button.addEventListener("click", () => {
            const product = {
                id: button.getAttribute("data-id"),
                name: button.getAttribute("data-name"),
                price: parseInt(button.getAttribute("data-price")),
                image: button.getAttribute("data-image"),
                quantity: 1, // Default quantity
            };
            addToCart(product);
        });
    });

    // Initialize cart
    fetchCart();
});
