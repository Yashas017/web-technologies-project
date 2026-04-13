const exp7Products = [
    { name: "Data Structures Book", category: "Books", price: 420, stock: 10, rating: 4.8 },
    { name: "Wireless Keyboard", category: "Electronics", price: 899, stock: 6, rating: 4.6 },
    { name: "Scientific Calculator", category: "Accessories", price: 760, stock: 9, rating: 4.9 },
    { name: "Notebook Pack", category: "Stationery", price: 180, stock: 25, rating: 4.2 },
    { name: "USB Study Lamp", category: "Electronics", price: 340, stock: 14, rating: 4.5 },
    { name: "Operating Systems Guide", category: "Books", price: 390, stock: 12, rating: 4.4 },
    { name: "Pen and Marker Kit", category: "Stationery", price: 140, stock: 28, rating: 4.1 },
    { name: "Laptop Sleeve", category: "Accessories", price: 320, stock: 18, rating: 4.3 }
];

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const categoryInput = document.getElementById("categoryInput");
    const sortInput = document.getElementById("sortInput");
    const resetBtn = document.getElementById("resetBtn");
    const downloadBtn = document.getElementById("downloadBtn");
    const productGrid = document.getElementById("productGrid");
    const totalItems = document.getElementById("totalItems");
    const totalStock = document.getElementById("totalStock");
    const inventoryValue = document.getElementById("inventoryValue");
    const topRated = document.getElementById("topRated");
    const summaryText = document.getElementById("summaryText");

    const formatMoney = (value) => `Rs. ${value.toLocaleString("en-IN")}`;

    function getVisibleProducts() {
        const searchValue = searchInput.value.trim().toLowerCase();
        const categoryValue = categoryInput.value;
        const sortValue = sortInput.value;

        let filtered = exp7Products.filter((product) => {
            const matchesSearch = product.name.toLowerCase().includes(searchValue);
            const matchesCategory = categoryValue === "All" || product.category === categoryValue;
            return matchesSearch && matchesCategory;
        });

        if (sortValue === "price-low") {
            filtered = [...filtered].sort((a, b) => a.price - b.price);
        } else if (sortValue === "price-high") {
            filtered = [...filtered].sort((a, b) => b.price - a.price);
        } else if (sortValue === "rating-high") {
            filtered = [...filtered].sort((a, b) => b.rating - a.rating);
        }

        return filtered;
    }

    function renderProducts(products) {
        if (!products.length) {
            productGrid.innerHTML = '<article class="info-card"><h3>No products found</h3><p>Try changing the search or filter values.</p></article>';
            return;
        }

        productGrid.innerHTML = products.map((product) => `
            <article class="product-tile">
                <div class="tile-top">
                    <span class="tag">${product.category}</span>
                    <strong>${formatMoney(product.price)}</strong>
                </div>
                <h3>${product.name}</h3>
                <p class="helper-text">Available stock: ${product.stock}</p>
                <div class="tile-meta">
                    <span>Rating ${product.rating.toFixed(1)}</span>
                    <span>Value ${formatMoney(product.price * product.stock)}</span>
                </div>
            </article>
        `).join("");
    }

    function renderStats(products) {
        const stockSum = products.reduce((sum, product) => sum + product.stock, 0);
        const valueSum = products.reduce((sum, product) => sum + (product.price * product.stock), 0);
        const topProduct = products.length ? [...products].sort((a, b) => b.rating - a.rating)[0].name : "-";

        totalItems.textContent = String(products.length);
        totalStock.textContent = String(stockSum);
        inventoryValue.textContent = formatMoney(valueSum);
        topRated.textContent = topProduct;
        summaryText.textContent = `Showing ${products.length} product(s) based on current filters.`;
    }

    function renderAll() {
        const products = getVisibleProducts();
        renderProducts(products);
        renderStats(products);
    }

    function downloadCsv() {
        const products = getVisibleProducts();
        let csv = "Name,Category,Price,Stock,Rating\n";

        products.forEach((product) => {
            csv += `${product.name},${product.category},${product.price},${product.stock},${product.rating}\n`;
        });

        const blob = new Blob([csv], { type: "text/csv" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "experiment7-products.csv";
        link.click();
    }

    [searchInput, categoryInput, sortInput].forEach((element) => {
        element.addEventListener("input", renderAll);
        element.addEventListener("change", renderAll);
    });

    resetBtn.addEventListener("click", () => {
        searchInput.value = "";
        categoryInput.value = "All";
        sortInput.value = "default";
        renderAll();
    });

    downloadBtn.addEventListener("click", downloadCsv);
    renderAll();
});
