
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // ELEMENTOS DEL DOM
    // ==========================================

    const productsGrid = document.getElementById('products-grid');
    const productsEmpty = document.getElementById('products-empty');
    const searchInput = document.getElementById('product-search');
    const clearSearch = document.getElementById('clear-search');

    const weightInput = document.getElementById('scale-weight');

    const ticketItems = document.getElementById('ticket-items');
    const ticketSubtotal = document.getElementById('ticket-subtotal');
    const ticketDiscount = document.getElementById('ticket-discount');
    const ticketTax = document.getElementById('ticket-tax');
    const ticketTotal = document.getElementById('ticket-total');

    const btnClearTicket = document.getElementById('btn-clear-ticket');

    document.getElementById('discount-type').addEventListener('change', updateTotals);
    document.getElementById('discount-value').addEventListener('input', updateTotals);
    document.getElementById('tax-value').addEventListener('input', updateTotals);

    if (!productsGrid) return;

    // ==========================================
    // VARIABLES
    // ==========================================

    let products = [];
    let cart = [];
    let selectedCategory = 'all';

    // ==========================================
    // FORMATO DE MONEDA
    // ==========================================

    function formatMoney(value) {
        return '$' + Number(value || 0).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ==========================================
    // PLACEHOLDER DE IMAGEN
    // ==========================================

    function createProductPlaceholder() {
        const placeholder = document.createElement('div');

        placeholder.className = 'product-placeholder';
        placeholder.textContent = '🥩';

        return placeholder;
    }

    // ==========================================
    // CARGAR PRODUCTOS
    // ==========================================

    async function loadProducts() {

        try {

            if (!window.cashRegisterRoutes ||
                !window.cashRegisterRoutes.products) {
                throw new Error('No se configuró la ruta de productos.');
            }

            productsGrid.innerHTML = `
                <div class="products-loading">
                    Cargando productos...
                </div>
            `;

            const response = await fetch(
                window.cashRegisterRoutes.products,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error(`Error HTTP ${response.status}`);
            }

            const data = await response.json();

            products = Array.isArray(data.products)
                ? data.products
                : [];

            renderProducts();

            console.log('Productos cargados:', products.length);

        } catch (error) {

            console.error('Error al cargar productos:', error);

            productsGrid.innerHTML = `
                <div class="products-loading">
                    Error al cargar los productos.
                </div>
            `;

            if (productsEmpty) {
                productsEmpty.hidden = true;
            }
        }
    }

    // ==========================================
    // CREAR TARJETA DE PRODUCTO
    // ==========================================

    function createProductCard(product) {

        const card = document.createElement('button');

        card.type = 'button';
        card.className = 'product-card';

        card.dataset.id = product.id;

        // IMAGEN
        const image = document.createElement('div');
        image.className = 'product-image';

        if (product.image_url) {

            const img = document.createElement('img');

            img.src = product.image_url;
            img.alt = product.name || 'Producto';
            img.loading = 'lazy';

            img.onerror = function () {
                this.replaceWith(createProductPlaceholder());
            };

            image.appendChild(img);

        } else {

            image.appendChild(createProductPlaceholder());

        }

        // INFORMACIÓN
        const info = document.createElement('div');
        info.className = 'product-info';

        const name = document.createElement('strong');
        name.textContent = product.name || 'Producto sin nombre';

        const price = document.createElement('span');
        price.className = 'product-price';
        price.textContent = formatMoney(product.price) + ' / kg';

        const code = document.createElement('small');
        code.textContent = product.code || 'Sin código';

        const category = document.createElement('small');
        category.textContent =
            (product.category_name || '') +
            ' · ' +
            (product.subcategory_name || '');

        info.append(name, price, code, category);

        card.append(image, info);

        // AL SELECCIONAR PRODUCTO, AGREGAR AL TICKET
        card.addEventListener('click', function () {
            addProductToTicket(product);
        });

        return card;
    }

    // ==========================================
    // MOSTRAR CATÁLOGO
    // ==========================================

    function renderProducts() {

        const search = searchInput
            ? searchInput.value.trim().toLowerCase()
            : '';

        const filteredProducts = products.filter(product => {

            const matchesCategory =
                selectedCategory === 'all' ||
                String(product.category_id) === selectedCategory;

            const matchesSearch =
                String(product.name || '').toLowerCase().includes(search) ||
                String(product.code || '').toLowerCase().includes(search);

            return matchesCategory && matchesSearch;
        });

        productsGrid.innerHTML = '';

        if (productsEmpty) {
            productsEmpty.hidden = filteredProducts.length > 0;
        }

        filteredProducts.forEach(product => {
            productsGrid.appendChild(createProductCard(product));
        });
    }

    // ==========================================
    // AGREGAR PRODUCTO AL TICKET
    // ==========================================

    function addProductToTicket(product) {

        if (!weightInput) return;

        const weight = Number(weightInput.value);
        const price = Number(product.price || 0);

        // VALIDAR PESO
        if (!Number.isFinite(weight) || weight <= 0) {

            mdalert({
                title: lang["name"],
                type: "error",
                msg: "Primero captura un peso válido en la báscula."
            });

            weightInput.focus();

            return;
        }

        if (price <= 0) {

            alert('Este producto no tiene un precio válido.');

            return;
        }

        // BUSCAR SI YA EXISTE EN EL TICKET
        const existingProduct = cart.find(item =>
            String(item.id) === String(product.id)
        );

        if (existingProduct) {

            existingProduct.weight += weight;
            existingProduct.amount =
                existingProduct.weight * existingProduct.price;

        } else {

            cart.push({
                id: product.id,
                code: product.code || '',
                name: product.name,
                price: price,
                weight: weight,
                amount: weight * price
            });

        }

        // LIMPIAR PESO PARA EL SIGUIENTE PESAJE
        weightInput.value = '0.000';

        renderTicket();

        weightInput.focus();

        console.log('Producto agregado al ticket:', product.name, weight);
    }

    // ==========================================
    // MOSTRAR TICKET
    // ==========================================

    function renderTicket() {

        if (!ticketItems) return;

        ticketItems.innerHTML = '';

        if (cart.length === 0) {

            ticketItems.innerHTML = `
                <tr class="ticket-empty-row">
                    <td colspan="6">
                        Captura el peso y selecciona un producto
                    </td>
                </tr>
            `;

            updateTotals();

            return;
        }

        cart.forEach((product, index) => {

            const row = document.createElement('tr');

            // NÚMERO
            const numberCell = document.createElement('td');
            numberCell.textContent = index + 1;

            // PRODUCTO
            const nameCell = document.createElement('td');

            const name = document.createElement('strong');
            name.textContent = product.name;

            const code = document.createElement('small');
            code.textContent = product.code;

            nameCell.append(name, document.createElement('br'), code);

            // PESO
            const weightCell = document.createElement('td');
            weightCell.textContent =
                product.weight.toFixed(3);

            // PRECIO
            const priceCell = document.createElement('td');
            priceCell.textContent = formatMoney(product.price);

            // IMPORTE
            const amountCell = document.createElement('td');
            amountCell.textContent = formatMoney(product.amount);

            // ELIMINAR
            const actionCell = document.createElement('td');

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn-remove-product';
            removeButton.textContent = '×';
            removeButton.title = 'Eliminar producto';

            removeButton.addEventListener('click', function () {

                cart.splice(index, 1);

                renderTicket();

            });

            actionCell.appendChild(removeButton);

            row.append(
                numberCell,
                nameCell,
                weightCell,
                priceCell,
                amountCell,
                actionCell
            );

            ticketItems.appendChild(row);

        });

        updateTotals();
    }

    // ==========================================
    // CALCULAR TOTALES
    // ==========================================



    function updateTotals() {
        const subtotal = cart.reduce((total, item) => {
            return total + (Number(item.weight) * Number(item.price));
        }, 0);

        const discountType = document.getElementById('discount-type').value;
        const discountValue = Math.max(
            0,
            Number(document.getElementById('discount-value').value) || 0
        );

        const taxValue = Math.max(
            0,
            Number(document.getElementById('tax-value').value) || 0
        );

        let discount = discountType === 'percent'
            ? subtotal * Math.min(discountValue, 100) / 100
            : discountValue;

        discount = Math.min(discount, subtotal);

        const taxableAmount = subtotal - discount;
        const tax = taxableAmount * taxValue / 100;
        const total = taxableAmount + tax;

        document.getElementById('ticket-subtotal').textContent = formatMoney(subtotal);
        document.getElementById('ticket-discount').textContent = formatMoney(discount);
        document.getElementById('ticket-tax').textContent = formatMoney(tax);
        document.getElementById('ticket-total').textContent = formatMoney(total);
    }



    // ==========================================
    // FILTRO DE CATEGORÍAS
    // ==========================================

    document.querySelectorAll('.category-btn').forEach(button => {

        button.addEventListener('click', function () {

            document.querySelectorAll('.category-btn').forEach(item => {
                item.classList.remove('active');
            });

            this.classList.add('active');

            selectedCategory = this.dataset.category || 'all';

            renderProducts();

        });

    });

    // ==========================================
    // BUSCADOR
    // ==========================================

    if (searchInput) {

        searchInput.addEventListener('input', renderProducts);

    }

    // ==========================================
    // LIMPIAR BUSCADOR
    // ==========================================

    if (clearSearch) {

        clearSearch.addEventListener('click', function () {

            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }

            renderProducts();

        });

    }

    // ==========================================
    // LIMPIAR TICKET
    // ==========================================

    if (btnClearTicket) {

        btnClearTicket.addEventListener('click', function () {

            if (cart.length === 0) return;

            if (!confirm('¿Deseas eliminar todos los productos del ticket?')) {
                return;
            }

            cart = [];

            renderTicket();

            if (weightInput) {
                weightInput.value = '0.000';
                weightInput.focus();
            }

        });

    }

    // ==========================================
    // INICIALIZAR
    // ==========================================

    renderTicket();

    loadProducts();

});
