document.addEventListener("DOMContentLoaded", function () {
    fetchProducts();
});

function fetchProducts() {
    fetch('backend/modell/product.php') // Fetch product data from the server
        .then(response => response.json())
        .then(products => {
            const productList = document.getElementById('productList');
            productList.innerHTML = ''; // Clear existing content

            products.forEach(product => {
                const productCard = `
                    <div class="col-md-4 mb-4">
                        <div class="card bg-dark text-white">
                            <img src="${product.img_path}" class="card-img-top" alt="${product.name}">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text">Price: €${product.price.toFixed(2)}</p>
                                <button class="btn btn-gold" onclick="addToCart('${product.name}', ${product.price})">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                `;
                productList.innerHTML += productCard;
            });
        })
        .catch(error => {
            console.error('Error fetching products:', error);
        });
}

function addToCart(name, price) {
    // Add to cart logic (you can implement this in kosar.js)
    console.log(`Added ${name} to cart at €${price}`);
}