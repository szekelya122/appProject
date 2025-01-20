// JavaScript for filtering products
document.addEventListener("DOMContentLoaded", () => {
    const filterDropdown = document.getElementById('categoryFilter');
    const products = document.querySelectorAll('.product-item');

    filterDropdown.addEventListener('change', () => {
        const selectedCategory = filterDropdown.value;

        products.forEach(product => {
            if (selectedCategory === 'all' || product.dataset.category === selectedCategory) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    });
});
