// Kosár kezelése
document.addEventListener("DOMContentLoaded", () => {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    // Kosár frissítése a helyi tárolóban
    const updateCart = () => {
        localStorage.setItem("cart", JSON.stringify(cart));
        displayCartItems();
    };

    // Termék hozzáadása a kosárhoz
    const addToCart = (product) => {
        const existingProduct = cart.find((item) => item.id === product.id);
        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        updateCart();
        alert("A termék sikeresen hozzáadva a kosárhoz!");
    };

    // Termékek megjelenítése a kosárban
    const displayCartItems = () => {
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
        cart.forEach((item, index) => {
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
                                <button class="btn btn-danger" data-index="${index}">Eltávolítás</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        cartSummary.innerHTML = `Összesen: ${total.toLocaleString()} Ft`;
        addRemoveEventListeners();
    };

    // Termék eltávolítása
    const removeFromCart = (index) => {
        cart.splice(index, 1);
        updateCart();
    };

    // Eltávolítás gombok eseménykezelőinek hozzáadása
    const addRemoveEventListeners = () => {
        document.querySelectorAll(".btn-danger").forEach((button) => {
            button.addEventListener("click", () => {
                const index = button.getAttribute("data-index");
                removeFromCart(index);
            });
        });
    };

    // Kosár inicializálása
    displayCartItems();

    // Termék hozzáadása a bolt oldalon
    document.querySelectorAll(".add-to-cart").forEach((button) => {
        button.addEventListener("click", () => {
            const product = {
                id: button.getAttribute("data-id"),
                name: button.getAttribute("data-name"),
                price: parseInt(button.getAttribute("data-price")),
                image: button.getAttribute("data-image"),
            };
            addToCart(product);
        });
    });
});
