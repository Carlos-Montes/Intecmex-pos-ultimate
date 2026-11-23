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

    const customerSelect = document.getElementById('customer-select');
    const customerDiscountLabel = document.getElementById('customer-discount-label');

    const discountType = document.getElementById('discount-type');
    const discountValue = document.getElementById('discount-value');
    const taxValue = document.getElementById('tax-value');

    const ticketSubtotal = document.getElementById('ticket-subtotal');
    const ticketDiscount = document.getElementById('ticket-discount');
    const ticketTax = document.getElementById('ticket-tax');
    const ticketTotal = document.getElementById('ticket-total');

    const btnClearTicket = document.getElementById('btn-clear-ticket');
    const btnCancel = document.getElementById('btn-cancel');

    // Apertura
    const cashForm = document.getElementById('form-open-cash');
    const cashAccount = document.getElementById('cash-account');
    const cashOpeningError = document.getElementById('cash-opening-error');
    const btnOpenCash = document.getElementById('btn-open-cash');

    // Cobro
    const btnCharge = document.getElementById('btn-charge');
    const checkoutOverlay = document.getElementById('checkout-overlay');
    const checkoutForm = document.getElementById('checkout-form');
    const cashReceivedInput = document.getElementById('cash-received');

    const checkoutSubtotal = document.getElementById('checkout-subtotal');
    const checkoutCustomerDiscount = document.getElementById('checkout-customer-discount');
    const checkoutManualDiscount = document.getElementById('checkout-manual-discount');
    const checkoutTax = document.getElementById('checkout-tax');
    const checkoutTotal = document.getElementById('checkout-total');
    const checkoutChange = document.getElementById('checkout-change');

    const checkoutError = document.getElementById('checkout-error');
    const btnCancelCheckout = document.getElementById('btn-cancel-checkout');
    const btnConfirmSale = document.getElementById('btn-confirm-sale');
    const btnCloseCheckout = document.getElementById('btn-close-checkout');

    // Cierre
    const btnCloseCash = document.getElementById('btn-close-cash');
    const cashClosingOverlay = document.getElementById('cash-closing-overlay');
    const btnCloseCashModal = document.getElementById('btn-close-cash-modal');
    const btnCancelCashClosing = document.getElementById('btn-cancel-cash-closing');
    const btnConfirmCashClosing = document.getElementById('btn-confirm-cash-closing');

    const closingOpening = document.getElementById('closing-opening');
    const closingSales = document.getElementById('closing-sales');
    const closingSalesCount = document.getElementById('closing-sales-count');
    const closingExpected = document.getElementById('closing-expected');

    const closingCounted = document.getElementById('closing-counted');
    const closingDifference = document.getElementById('closing-difference');
    const closingDifferenceLine = document.getElementById('closing-difference-line');
    const closingDifferenceMessage = document.getElementById('closing-difference-message');

    const cashClosingError = document.getElementById('cash-closing-error');
    const cashDenominationsContainer = document.getElementById('cash-denominations');

    if (!productsGrid) {
        console.warn('No se encontró el contenedor de productos.');
        return;
    }

    // ==========================================
    // VARIABLES
    // ==========================================

    let products = [];
    let cart = [];
    let selectedCategory = 'all';
    let customerDiscount = 0;

    let cashClosingInProgress = false;
    let saleInProgress = false;
    let cashCloseSummary = null;

    let currentTotals = {
        subtotal: 0,
        customerDiscount: 0,
        manualDiscount: 0,
        discount: 0,
        tax: 0,
        total: 0
    };

    // ==========================================
    // DENOMINACIONES
    // Los importes se calculan en centavos.
    // ==========================================

    const cashDenominations = {
        bill_1000: { label: 'Billetes de $1,000', cents: 100000 },
        bill_500: { label: 'Billetes de $500', cents: 50000 },
        bill_200: { label: 'Billetes de $200', cents: 20000 },
        bill_100: { label: 'Billetes de $100', cents: 10000 },
        bill_50: { label: 'Billetes de $50', cents: 5000 },
        bill_20: { label: 'Billetes de $20', cents: 2000 },
        coin_20: { label: 'Monedas de $20', cents: 2000 },
        coin_10: { label: 'Monedas de $10', cents: 1000 },
        coin_5: { label: 'Monedas de $5', cents: 500 },
        coin_2: { label: 'Monedas de $2', cents: 200 },
        coin_1: { label: 'Monedas de $1', cents: 100 },
        coin_50c: { label: 'Monedas de $0.50', cents: 50 }
    };

    // ==========================================
    // UTILIDADES
    // ==========================================

    function formatMoney(value) {
        return '$' + Number(value || 0).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function roundMoney(value) {
        return Math.round(
            (Number(value || 0) + Number.EPSILON) * 100
        ) / 100;
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content ||
            document.querySelector('[name="_token"]')?.value || '';
    }

    async function readJsonResponse(response) {
        const text = await response.text();

        try {
            return text ? JSON.parse(text) : {};
        } catch (error) {
            throw new Error('El servidor devolvió una respuesta no válida.');
        }
    }

    function showError(message) {
        if (typeof mdalert === 'function') {
            mdalert({
                title: window.lang?.name || 'INTECMEX',
                type: 'error',
                msg: message
            });
        } else {
            alert(message);
        }
    }

    function showSuccess(message) {
        if (typeof mdalert === 'function') {
            mdalert({
                title: window.lang?.name || 'INTECMEX',
                type: 'success',
                msg: message
            });
        } else {
            alert(message);
        }
    }

    function showLoader() {
        if (typeof loader_action_status === 'function') {
            loader_action_status('show');
        }
    }

    function hideLoader() {
        if (typeof loader_action_status === 'function') {
            loader_action_status('hide');
        }
    }

    function showInlineError(element, message) {
        if (!element) return;
        element.textContent = message;
        element.hidden = false;
    }

    function hideInlineError(element) {
        if (!element) return;
        element.textContent = '';
        element.hidden = true;
    }

    // ==========================================
    // PLACEHOLDER DE PRODUCTO
    // ==========================================

    function createProductPlaceholder() {
        const placeholder = document.createElement('div');
        placeholder.className = 'product-placeholder';
        placeholder.textContent = '🥩';
        return placeholder;
    }

    // ==========================================
    // TOTALES
    // ==========================================

    function updateTotals() {

        const subtotal = roundMoney(
            cart.reduce((total, item) => {
                return total + (
                    Number(item.weight) * Number(item.price)
                );
            }, 0)
        );

        const appliedCustomerDiscount = Math.min(
            Math.max(0, customerDiscount),
            subtotal
        );

        const remaining = Math.max(
            0,
            subtotal - appliedCustomerDiscount
        );

        const type = discountType?.value || 'amount';

        const value = Math.max(
            0,
            Number(discountValue?.value) || 0
        );

        const taxPercentage = Math.min(
            100,
            Math.max(0, Number(taxValue?.value) || 0)
        );

        let manualDiscount = 0;

        if (type === 'percent') {
            manualDiscount = remaining * Math.min(value, 100) / 100;
        } else {
            manualDiscount = Math.min(value, remaining);
        }

        manualDiscount = roundMoney(manualDiscount);

        const totalDiscount = roundMoney(
            appliedCustomerDiscount + manualDiscount
        );

        const taxableAmount = Math.max(
            0,
            subtotal - totalDiscount
        );

        const tax = roundMoney(
            taxableAmount * taxPercentage / 100
        );

        const total = roundMoney(taxableAmount + tax);

        currentTotals = {
            subtotal,
            customerDiscount: roundMoney(appliedCustomerDiscount),
            manualDiscount,
            discount: totalDiscount,
            tax,
            total
        };

        if (ticketSubtotal) ticketSubtotal.textContent = formatMoney(subtotal);
        if (ticketDiscount) ticketDiscount.textContent = formatMoney(totalDiscount);
        if (ticketTax) ticketTax.textContent = formatMoney(tax);
        if (ticketTotal) ticketTotal.textContent = formatMoney(total);

        updateCheckoutSummary();

        return currentTotals;
    }

    discountType?.addEventListener('change', updateTotals);
    discountValue?.addEventListener('input', updateTotals);
    taxValue?.addEventListener('input', updateTotals);

    // ==========================================
    // CARGAR PRODUCTOS
    // ==========================================

    async function loadProducts() {

        try {

            if (!window.cashRegisterRoutes?.products) {
                throw new Error('No se configuró la ruta de productos.');
            }

            productsGrid.innerHTML = `
                <div class="products-loading">Cargando productos...</div>
            `;

            const response = await fetch(window.cashRegisterRoutes.products, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await readJsonResponse(response);

            if (!response.ok) {
                throw new Error(data.message || `Error HTTP ${response.status}`);
            }

            products = Array.isArray(data.products) ? data.products : [];

            renderProducts();

            console.log('Productos cargados:', products.length);

        } catch (error) {

            console.error('Error al cargar productos:', error);

            productsGrid.innerHTML = `
                <div class="products-loading">Error al cargar los productos.</div>
            `;

            if (productsEmpty) productsEmpty.hidden = true;
        }
    }

    // ==========================================
    // CARGAR CLIENTES
    // ==========================================

    async function loadCustomers() {

        if (!customerSelect) return;

        try {

            if (!window.cashRegisterRoutes?.customers) {
                throw new Error('No se configuró la ruta de clientes.');
            }

            customerSelect.innerHTML = `
                <option value="0" data-discount="0">Público General</option>
            `;

            const response = await fetch(window.cashRegisterRoutes.customers, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await readJsonResponse(response);

            if (!response.ok) {
                throw new Error(data.message || `Error HTTP ${response.status}`);
            }

            const customers = Array.isArray(data.customers)
                ? data.customers
                : [];

            customers.forEach(customer => {

                const option = document.createElement('option');

                option.value = customer.id;
                option.dataset.discount = Number(customer.indiscount || 0);
                option.textContent = customer.name || 'Cliente sin nombre';

                customerSelect.appendChild(option);
            });

            console.log('Clientes cargados:', customers.length);

        } catch (error) {
            console.error('Error al cargar clientes:', error);
        }
    }

    // ==========================================
    // SELECCIONAR CLIENTE
    // ==========================================

    customerSelect?.addEventListener('change', function () {

        const option = this.options[this.selectedIndex];

        customerDiscount = Math.max(
            0,
            Number(option?.dataset.discount) || 0
        );

        if (customerDiscountLabel) {
            customerDiscountLabel.textContent = customerDiscount > 0
                ? `Descuento del cliente: ${formatMoney(customerDiscount)}`
                : 'Sin descuento';
        }

        updateTotals();
    });

    // ==========================================
    // TARJETA DE PRODUCTO
    // ==========================================

    function createProductCard(product) {

        const card = document.createElement('button');

        card.type = 'button';
        card.className = 'product-card';
        card.dataset.id = product.id;

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

        const info = document.createElement('div');
        info.className = 'product-info';

        const name = document.createElement('strong');
        name.textContent = product.name || 'Producto sin nombre';

        const price = document.createElement('span');
        price.className = 'product-price';
        price.textContent = formatMoney(product.price) + ' / kg';

        const inventory = document.createElement('small');
        inventory.className = 'product-inventory';

        const quantity = Number(product.quantity_kg || 0);

        inventory.textContent = `Existencia: ${quantity.toFixed(3)} kg`;

        if (quantity <= 0) {
            inventory.classList.add('out-of-stock');
        } else if (quantity <= 5) {
            inventory.classList.add('low-stock');
        } else {
            inventory.classList.add('available-stock');
        }

        const code = document.createElement('small');
        code.textContent = product.code || 'Sin código';

        const category = document.createElement('small');
        category.textContent =
            (product.category_name || '') + ' · ' +
            (product.subcategory_name || '');

        info.append(name, price, inventory, code, category);
        card.append(image, info);

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
        const availableStock = Number(product.quantity_kg || 0);

        if (!Number.isFinite(weight) || weight <= 0) {
            showError('Primero captura un peso válido en la báscula.');
            weightInput.focus();
            return;
        }

        if (!Number.isFinite(price) || price <= 0) {
            showError('Este producto no tiene un precio válido.');
            return;
        }

        if (!Number.isFinite(availableStock) || availableStock <= 0) {
            showError('Este producto no tiene inventario disponible.');
            return;
        }

        const existingProduct = cart.find(item =>
            String(item.id) === String(product.id)
        );

        const currentWeight = existingProduct
            ? existingProduct.weight
            : 0;

        const newWeight = Math.round(
            (currentWeight + weight) * 1000
        ) / 1000;

        if (newWeight > availableStock) {
            showError(
                `Inventario insuficiente. Disponible: ${availableStock.toFixed(3)} kg.`
            );
            return;
        }

        if (existingProduct) {

            existingProduct.weight = newWeight;

            existingProduct.amount = roundMoney(
                existingProduct.weight * existingProduct.price
            );

        } else {

            cart.push({
                id: product.id,
                code: product.code || '',
                name: product.name,
                price,
                weight: Math.round(weight * 1000) / 1000,
                amount: roundMoney(weight * price)
            });
        }

        weightInput.value = '';

        renderTicket();
        weightInput.focus();
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

            const numberCell = document.createElement('td');
            numberCell.textContent = index + 1;

            const nameCell = document.createElement('td');

            const name = document.createElement('strong');
            name.textContent = product.name;

            const code = document.createElement('small');
            code.textContent = product.code;

            nameCell.append(name, document.createElement('br'), code);

            const weightCell = document.createElement('td');
            weightCell.textContent = product.weight.toFixed(3);

            const priceCell = document.createElement('td');
            priceCell.textContent = formatMoney(product.price);

            const amountCell = document.createElement('td');
            amountCell.textContent = formatMoney(product.amount);

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
    // LIMPIAR TICKET
    // ==========================================

    function limpiarTicket() {

        cart = [];

        if (customerSelect) customerSelect.value = '0';

        customerDiscount = 0;

        if (customerDiscountLabel) {
            customerDiscountLabel.textContent = 'Sin descuento';
        }

        if (discountType) discountType.value = 'percent';
        if (discountValue) discountValue.value = '0';
        if (taxValue) taxValue.value = '0';
        if (weightInput) weightInput.value = '';
        if (searchInput) searchInput.value = '';

        selectedCategory = 'all';

        document.querySelectorAll('.category-btn').forEach(button => {
            button.classList.toggle(
                'active',
                button.dataset.category === 'all'
            );
        });

        renderProducts();
        renderTicket();

        if (cashReceivedInput) cashReceivedInput.value = '0';
        if (checkoutChange) checkoutChange.textContent = formatMoney(0);

        hideInlineError(checkoutError);

        if (typeof md_alert_status === 'function') {
            md_alert_status('hide');
        }

        weightInput?.focus();
    }

    window.limpiarTicket = limpiarTicket;

    // ==========================================
    // CATEGORÍAS
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

    searchInput?.addEventListener('input', renderProducts);

    clearSearch?.addEventListener('click', function () {

        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }

        renderProducts();
    });

    // ==========================================
    // LIMPIAR TICKET CON CONFIRMACIÓN
    // ==========================================

    btnClearTicket?.addEventListener('click', function (event) {

        event.preventDefault();

        if (cart.length === 0) return;

        if (typeof mdalert !== 'function') {
            if (confirm('¿Deseas limpiar el ticket?')) limpiarTicket();
            return;
        }

        mdalert({
            title: '¿Deseas limpiar el ticket?',
            type: 'inactive',
            msg: 'Se eliminarán todos los productos agregados al ticket.',
            actions: JSON.stringify([
                {
                    name: 'Sí, limpiar ticket',
                    type: 'danger',
                    callback: 'limpiarTicket',
                    params: ''
                }
            ])
        });
    });

    // ==========================================
    // CANCELAR VENTA
    // ==========================================

    btnCancel?.addEventListener('click', function (event) {

        event.preventDefault();

        if (cart.length === 0) {
            showError('No hay una venta en proceso para cancelar.');
            return;
        }

        if (typeof mdalert !== 'function') {
            if (confirm('¿Deseas cancelar esta venta?')) limpiarTicket();
            return;
        }

        mdalert({
            title: '¿Deseas cancelar esta venta?',
            type: 'inactive',
            msg: 'Se eliminarán los productos, cliente, descuentos, IVA y datos capturados.',
            actions: JSON.stringify([
                {
                    name: 'Sí, cancelar venta',
                    type: 'danger',
                    callback: 'limpiarTicket',
                    params: ''
                }
            ])
        });
    });

    // ==========================================
    // APERTURA DE CAJA
    // ==========================================

    async function loadCashRegisterStatus() {

        try {

            if (!window.cashRegisterRoutes?.status) {
                throw new Error('No se configuró la ruta de estado de caja.');
            }

            if (!cashAccount || !btnOpenCash) return;

            const response = await fetch(window.cashRegisterRoutes.status, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await readJsonResponse(response);

            if (!response.ok) {
                throw new Error(data.message || `Error HTTP ${response.status}`);
            }

            if (data.opened) {
                window.location.reload();
                return;
            }

            cashAccount.innerHTML = `
                <option value="">Selecciona una cuenta</option>
            `;

            if (!Array.isArray(data.accounts) || data.accounts.length === 0) {

                cashAccount.innerHTML = `
                    <option value="">No hay cuentas disponibles</option>
                `;

                return;
            }

            data.accounts.forEach(account => {

                const option = document.createElement('option');

                option.value = account.id;
                option.textContent =
                    `${account.account_number} - ${account.name}`;

                cashAccount.appendChild(option);
            });

            btnOpenCash.disabled = false;

        } catch (error) {

            console.error('Error al consultar las cuentas:', error);

            if (cashAccount) {
                cashAccount.innerHTML = `
                    <option value="">Error al cargar cuentas</option>
                `;
            }
        }
    }

    if (cashForm) {

        loadCashRegisterStatus();

        cashForm.addEventListener('submit', async function (event) {

            event.preventDefault();

            if (!btnOpenCash || !cashOpeningError) return;

            hideInlineError(cashOpeningError);

            btnOpenCash.disabled = true;
            btnOpenCash.textContent = 'Abriendo caja...';

            try {

                if (!window.cashRegisterRoutes?.open) {
                    throw new Error('No se configuró la ruta para abrir caja.');
                }

                const response = await fetch(window.cashRegisterRoutes.open, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: new FormData(cashForm)
                });

                const data = await readJsonResponse(response);

                if (!response.ok) {

                    const errors = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : data.message || 'No fue posible abrir la caja.';

                    throw new Error(errors);
                }

                window.location.reload();

            } catch (error) {

                console.error('Error al abrir caja:', error);

                showInlineError(cashOpeningError, error.message);

                btnOpenCash.disabled = false;
                btnOpenCash.textContent = 'Abrir caja';
            }
        });
    }

    // ==========================================
    // RESUMEN DE COBRO
    // ==========================================

    function updateCheckoutSummary() {

        if (checkoutSubtotal) {
            checkoutSubtotal.textContent = formatMoney(currentTotals.subtotal);
        }

        if (checkoutCustomerDiscount) {
            checkoutCustomerDiscount.textContent =
                formatMoney(currentTotals.customerDiscount);
        }

        if (checkoutManualDiscount) {
            checkoutManualDiscount.textContent =
                formatMoney(currentTotals.manualDiscount);
        }

        if (checkoutTax) {
            checkoutTax.textContent = formatMoney(currentTotals.tax);
        }

        if (checkoutTotal) {
            checkoutTotal.textContent = formatMoney(currentTotals.total);
        }

        updateCheckoutChange();
    }

    // ==========================================
    // CAMBIO
    // ==========================================

    function updateCheckoutChange() {

        if (!cashReceivedInput || !checkoutChange) return;

        const received = Number(cashReceivedInput.value) || 0;

        const change = Math.max(
            0,
            roundMoney(received - currentTotals.total)
        );

        checkoutChange.textContent = formatMoney(change);
    }

    // ==========================================
    // ABRIR COBRO
    // ==========================================

    function openCheckout() {

        if (!checkoutOverlay || !checkoutForm || !cashReceivedInput) {
            showError('No se encontró el formulario de cobro.');
            return;
        }

        if (cart.length === 0) {
            showError('Agrega al menos un producto antes de cobrar.');
            return;
        }

        if (saleInProgress || cashClosingInProgress) return;

        updateTotals();

        hideInlineError(checkoutError);

        cashReceivedInput.value = currentTotals.total.toFixed(2);

        updateCheckoutSummary();

        checkoutOverlay.hidden = false;
        checkoutOverlay.style.display = 'flex';

        cashReceivedInput.focus();
        cashReceivedInput.select();
    }

    // ==========================================
    // CERRAR COBRO
    // ==========================================

    function closeCheckout() {

        if (!checkoutOverlay) return;

        checkoutOverlay.hidden = true;
        checkoutOverlay.style.display = 'none';

        hideInlineError(checkoutError);

        if (btnConfirmSale) {
            btnConfirmSale.disabled = false;
            btnConfirmSale.textContent = 'Confirmar y cobrar';
        }
    }

    btnCharge?.addEventListener('click', openCheckout);
    btnCancelCheckout?.addEventListener('click', closeCheckout);
    btnCloseCheckout?.addEventListener('click', closeCheckout);

    cashReceivedInput?.addEventListener('input', updateCheckoutChange);

    // ==========================================
    // CORTE DE CAJA: ERRORES
    // ==========================================

    function showCashClosingError(message) {
        showInlineError(cashClosingError, message);
    }

    function hideCashClosingError() {
        hideInlineError(cashClosingError);
    }

    // ==========================================
    // CALCULAR ARQUEO
    // ==========================================

    function calculateCashCount() {

        let totalCents = 0;
        const cashCount = {};

        Object.entries(cashDenominations).forEach(([key, denomination]) => {

            const input = cashDenominationsContainer?.querySelector(
                `[data-denomination="${key}"]`
            );

            const rawQuantity = Number(input?.value);

            const quantity = Number.isFinite(rawQuantity)
                ? Math.max(0, Math.floor(rawQuantity))
                : 0;

            const subtotalCents = quantity * denomination.cents;

            totalCents += subtotalCents;

            cashCount[key] = quantity;

            const row = input?.closest('.cash-denomination-row');
            const subtotalElement = row?.querySelector('strong');

            if (subtotalElement) {
                subtotalElement.textContent =
                    formatMoney(subtotalCents / 100);
            }
        });

        return {
            total: roundMoney(totalCents / 100),
            cash_count: cashCount
        };
    }

    // ==========================================
    // ACTUALIZAR DIFERENCIA
    // ==========================================

    function updateCashClosingCalculation() {

        const counted = calculateCashCount();

        const expected = Number(
            cashCloseSummary?.expected_amount || 0
        );

        const difference = roundMoney(counted.total - expected);

        if (closingCounted) {
            closingCounted.textContent = formatMoney(counted.total);
        }

        if (closingDifference) {
            closingDifference.textContent =
                formatMoney(Math.abs(difference));

            closingDifference.style.color =
                difference < 0 ? '#b42318' :
                difference > 0 ? '#067647' :
                '#202938';
        }

        if (closingDifferenceLine) {
            closingDifferenceLine.dataset.status =
                difference < 0 ? 'shortage' :
                difference > 0 ? 'surplus' : 'exact';
        }

        if (closingDifferenceMessage) {

            if (difference < 0) {
                closingDifferenceMessage.textContent =
                    `Faltante de ${formatMoney(Math.abs(difference))}.`;
            } else if (difference > 0) {
                closingDifferenceMessage.textContent =
                    `Sobrante de ${formatMoney(difference)}.`;
            } else {
                closingDifferenceMessage.textContent =
                    'El efectivo contado coincide con el efectivo esperado.';
            }
        }
    }

    // ==========================================
    // CONSULTAR RESUMEN DE CAJA
    // ==========================================

    async function fetchCashClosingSummary() {

        if (!window.cashRegisterRoutes?.summary) {
            throw new Error('No se configuró la ruta del resumen de caja.');
        }

        const response = await fetch(window.cashRegisterRoutes.summary, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await readJsonResponse(response);

        if (!response.ok || !data.success || !data.summary) {
            throw new Error(
                data.message || 'No fue posible consultar el resumen de caja.'
            );
        }

        return data.summary;
    }

    // ==========================================
    // ABRIR MODAL DE CIERRE
    // ==========================================

    async function openCashClosing() {

        if (cashClosingInProgress || saleInProgress) return;

        if (!cashClosingOverlay) {
            showError('No se encontró el modal de cierre de caja.');
            return;
        }

        if (cart.length > 0) {
            showError(
                'Primero debes cobrar o cancelar los productos del ticket.'
            );
            return;
        }

        if (checkoutOverlay && !checkoutOverlay.hidden) {
            showError('Primero cierra la ventana de cobro.');
            return;
        }

        hideCashClosingError();

        cashClosingOverlay.hidden = false;
        cashClosingOverlay.style.display = 'flex';

        if (btnConfirmCashClosing) {
            btnConfirmCashClosing.disabled = true;
            btnConfirmCashClosing.textContent = 'Consultando caja...';
        }

        try {

            const summary = await fetchCashClosingSummary();

            cashCloseSummary = summary;

            if (closingOpening) {
                closingOpening.textContent =
                    formatMoney(summary.opening_amount);
            }

            if (closingSales) {
                closingSales.textContent =
                    formatMoney(summary.cash_sales);
            }

            if (closingSalesCount) {
                closingSalesCount.textContent =
                    `${Number(summary.sales_count || 0)} ventas`;
            }

            if (closingExpected) {
                closingExpected.textContent =
                    formatMoney(summary.expected_amount);
            }

            // Reiniciar conteo cada vez que se abre el modal.
            cashDenominationsContainer?.querySelectorAll(
                '[data-denomination]'
            ).forEach(input => {
                input.value = '0';
            });

            updateCashClosingCalculation();

            if (btnConfirmCashClosing) {
                btnConfirmCashClosing.disabled = false;
                btnConfirmCashClosing.textContent =
                    '🔒 Confirmar corte y cerrar caja';
            }

        } catch (error) {

            console.error('Error al consultar resumen:', error);

            showCashClosingError(error.message);

            if (btnConfirmCashClosing) {
                btnConfirmCashClosing.disabled = true;
                btnConfirmCashClosing.textContent = 'No disponible';
            }
        }
    }

    // ==========================================
    // CERRAR MODAL SIN CERRAR LA CAJA
    // ==========================================

    function closeCashClosingModal() {

        if (cashClosingInProgress) return;
        if (!cashClosingOverlay) return;

        cashClosingOverlay.hidden = true;
        cashClosingOverlay.style.display = 'none';

        hideCashClosingError();
    }

    // ==========================================
    // EVENTOS DEL ARQUEO
    // ==========================================

    cashDenominationsContainer?.addEventListener('input', function (event) {

        if (!event.target.matches('[data-denomination]')) return;

        const value = Number(event.target.value);

        if (value < 0) {
            event.target.value = '0';
        }

        updateCashClosingCalculation();
    });

    btnCloseCash?.addEventListener('click', openCashClosing);

    btnCloseCashModal?.addEventListener(
        'click',
        closeCashClosingModal
    );

    btnCancelCashClosing?.addEventListener(
        'click',
        closeCashClosingModal
    );

    // ==========================================
    // SEGURIDAD QZ TRAY
    // ==========================================

    let qzConfigured = false;
    let qzConnectionPromise = null;

    function configureQZ() {

        if (qzConfigured) return;

        if (typeof qz === 'undefined') {
            throw new Error('No se cargó la librería qz-tray.js.');
        }

        if (!window.qzRoutes?.certificate || !window.qzRoutes?.sign) {
            throw new Error('No se configuraron las rutas de seguridad QZ.');
        }

        qz.security.setCertificatePromise(function (resolve, reject) {

            fetch(window.qzRoutes.certificate, {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'Accept': 'text/plain'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('No se pudo cargar el certificado QZ.');
                    }
                    return response.text();
                })
                .then(resolve)
                .catch(reject);
        });

        qz.security.setSignatureAlgorithm('SHA512');

        qz.security.setSignaturePromise(function (toSign) {

            return function (resolve, reject) {

                fetch(window.qzRoutes.sign, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'text/plain'
                    },
                    body: new URLSearchParams({
                        request: toSign
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Laravel no pudo firmar la solicitud QZ.');
                        }
                        return response.text();
                    })
                    .then(resolve)
                    .catch(reject);
            };
        });

        qzConfigured = true;

        console.log('Seguridad QZ Tray configurada.');
    }

    // ==========================================
    // CONECTAR QZ TRAY
    // ==========================================

    async function connectQZ() {

        if (typeof qz === 'undefined') {
            throw new Error('QZ Tray no está cargado en el navegador.');
        }

        configureQZ();

        if (qz.websocket.isActive()) {
            return true;
        }

        if (!qzConnectionPromise) {

            qzConnectionPromise = qz.websocket.connect()
                .then(() => {
                    console.log('Conexión establecida con QZ Tray.');
                    return true;
                })
                .catch(error => {
                    qzConnectionPromise = null;
                    throw error;
                });
        }

        return qzConnectionPromise;
    }

    // ==========================================
    // ESCAPAR HTML
    // ==========================================

    function escapeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ==========================================
    // CONSTRUIR TICKET DE VENTA
    // ==========================================

    function buildReceiptHtml(sale) {

        const details = Array.isArray(sale.details)
            ? sale.details
            : [];

        const customerName =
            sale.customer?.name || 'Público General';

        const sellerName =
            sale.user?.name || 'Vendedor';

        const saleDate = sale.sold_at
            ? new Date(sale.sold_at).toLocaleString('es-MX')
            : new Date().toLocaleString('es-MX');

        const productsHtml = details.map(item => {

            const name = escapeHtml(item.product_name);
            const weight = Number(item.quantity_kg || 0).toFixed(3);

            return `
                <div class="item">
                    <div class="item-name">${name}</div>
                    <div class="item-detail">
                        ${weight} kg x ${formatMoney(item.unit_price)}
                    </div>
                    <div class="item-total">
                        ${formatMoney(item.subtotal)}
                    </div>
                </div>
            `;
        }).join('');

        return `
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    @page { size: 58mm auto; margin: 0; }

                    * { box-sizing: border-box; }

                    html, body {
                        width: 48mm;
                        max-width: 48mm;
                        margin: 0;
                        padding: 0;
                        background: #fff;
                        color: #000;
                        font-family: Arial, sans-serif;
                        font-size: 9px;
                    }

                    .receipt {
                        width: 48mm;
                        max-width: 48mm;
                        padding: 2mm 1mm;
                        margin: 0 auto;
                    }

                    .center { text-align: center; }

                    h2 {
                        font-size: 17px;
                        margin: 0 0 4px;
                    }

                    p { margin: 3px 0; }

                    hr {
                        border: 0;
                        border-top: 1px dashed #000;
                        margin: 7px 0;
                    }

                    .item { margin: 7px 0; }
                    .item-name { font-weight: bold; overflow-wrap: anywhere; }
                    .item-detail { margin-top: 2px; }
                    .item-total { text-align: right; font-weight: bold; }

                    .line {
                        display: flex;
                        justify-content: space-between;
                        gap: 5px;
                        margin: 4px 0;
                    }

                    .line span { flex: 1; }
                    .line strong { text-align: right; }

                    .total {
                        font-size: 14px;
                        font-weight: bold;
                    }

                    .footer {
                        text-align: center;
                        margin-top: 10px;
                    }
                </style>
            </head>
            <body>
                <section class="receipt">

                    <div class="center">
                        <h2>${escapeHtml(window.lang?.app_name || 'INTECMEX')}</h2>
                        <p>Ticket de venta</p>
                    </div>

                    <hr>

                    <p><strong>Folio:</strong> ${escapeHtml(sale.folio)}</p>
                    <p><strong>Fecha:</strong> ${escapeHtml(saleDate)}</p>
                    <p><strong>Cliente:</strong> ${escapeHtml(customerName)}</p>
                    <p><strong>Atendió:</strong> ${escapeHtml(sellerName)}</p>

                    <hr>

                    <strong>PRODUCTOS</strong>

                    ${productsHtml}

                    <hr>

                    <div class="line">
                        <span>Subtotal:</span>
                        <strong>${formatMoney(sale.subtotal)}</strong>
                    </div>

                    <div class="line">
                        <span>Desc. cliente:</span>
                        <strong>${formatMoney(sale.customer_discount_amount)}</strong>
                    </div>

                    <div class="line">
                        <span>Desc. manual:</span>
                        <strong>${formatMoney(sale.manual_discount_amount)}</strong>
                    </div>

                    <div class="line">
                        <span>IVA:</span>
                        <strong>${formatMoney(sale.tax_amount)}</strong>
                    </div>

                    <hr>

                    <div class="line total">
                        <span>TOTAL:</span>
                        <strong>${formatMoney(sale.total)}</strong>
                    </div>

                    <div class="line">
                        <span>Recibido:</span>
                        <strong>${formatMoney(sale.cash_received)}</strong>
                    </div>

                    <div class="line">
                        <span>Cambio:</span>
                        <strong>${formatMoney(sale.change_amount)}</strong>
                    </div>

                    <hr>

                    <div class="footer">
                        <p>¡Gracias por su compra!</p>
                        <p>Conserve su ticket.</p>
                    </div>

                </section>
            </body>
            </html>
        `;
    }

    // ==========================================
    // IMPRIMIR TICKET DE VENTA
    // ==========================================

    async function printSaleTicket(sale) {

        await connectQZ();

        const printerName = 'POS-58';
        const printer = await qz.printers.find(printerName);

        if (!printer) {
            throw new Error(`No se encontró la impresora ${printerName}.`);
        }

        const config = qz.configs.create(printer, {
            encoding: 'UTF-8',
            units: 'mm',
            size: { width: 58, height: 187 },
            margins: 0,
            scaleContent: true
        });

        await qz.print(config, [
            {
                type: 'pixel',
                format: 'html',
                flavor: 'plain',
                data: buildReceiptHtml(sale)
            }
        ]);

        console.log('Ticket enviado a POS-58.');

        return true;
    }

    // ==========================================
    // CONSTRUIR TICKET DE CORTE
    // ==========================================

    function buildCashClosingReceipt(result) {

        const summary = result.summary || {};
        const session = result.session || {};

        const accountName =
            session.account?.name ||
            summary.account?.name ||
            'Cuenta';

        const userName =
            session.user?.name ||
            summary.user?.name ||
            'Usuario';

        const denominationsHtml = Object.entries(
            summary.cash_count?.denominations || {}
        ).map(([key, item]) => {

            return `
                <div class="line">
                    <span>${escapeHtml(item.label)} x ${item.quantity}</span>
                    <strong>${formatMoney(item.subtotal)}</strong>
                </div>
            `;
        }).join('');

        const difference = Number(summary.difference || 0);

        const differenceLabel =
            difference < 0 ? 'FALTANTE' :
            difference > 0 ? 'SOBRANTE' :
            'SIN DIFERENCIA';

        return `
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    @page { size: 58mm auto; margin: 0; }

                    * { box-sizing: border-box; }

                    html, body {
                        width: 48mm;
                        max-width: 48mm;
                        margin: 0;
                        padding: 0;
                        color: #000;
                        font-family: Arial, sans-serif;
                        font-size: 9px;
                    }

                    .receipt {
                        width: 48mm;
                        padding: 2mm 1mm;
                    }

                    .center { text-align: center; }

                    h2 {
                        margin: 0 0 5px;
                        font-size: 16px;
                    }

                    p { margin: 3px 0; }

                    hr {
                        border: 0;
                        border-top: 1px dashed #000;
                        margin: 7px 0;
                    }

                    .line {
                        display: flex;
                        justify-content: space-between;
                        gap: 5px;
                        margin: 4px 0;
                    }

                    .line span { flex: 1; }
                    .line strong { text-align: right; }

                    .total {
                        font-size: 13px;
                        font-weight: bold;
                    }

                    .difference {
                        font-size: 12px;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="receipt">

                    <div class="center">
                        <h2>${escapeHtml(window.lang?.app_name || 'INTECMEX')}</h2>
                        <strong>CORTE DE CAJA</strong>
                    </div>

                    <hr>

                    <p><strong>Cuenta:</strong> ${escapeHtml(accountName)}</p>
                    <p><strong>Responsable:</strong> ${escapeHtml(userName)}</p>
                    <p><strong>Sesión:</strong> ${escapeHtml(summary.session_id)}</p>
                    <p><strong>Apertura:</strong> ${escapeHtml(summary.opened_at || '')}</p>
                    <p><strong>Cierre:</strong> ${escapeHtml(summary.closed_at || '')}</p>

                    <hr>

                    <strong>RESUMEN</strong>

                    <div class="line">
                        <span>Fondo inicial</span>
                        <strong>${formatMoney(summary.opening_amount)}</strong>
                    </div>

                    <div class="line">
                        <span>Ventas (${Number(summary.sales_count || 0)})</span>
                        <strong>${formatMoney(summary.cash_sales)}</strong>
                    </div>

                    <div class="line">
                        <span>Entradas</span>
                        <strong>${formatMoney(summary.cash_entries)}</strong>
                    </div>

                    <div class="line">
                        <span>Salidas</span>
                        <strong>${formatMoney(summary.cash_withdrawals)}</strong>
                    </div>

                    <hr>

                    <div class="line total">
                        <span>ESPERADO</span>
                        <strong>${formatMoney(summary.expected_amount)}</strong>
                    </div>

                    <hr>

                    <strong>ARQUEO DE EFECTIVO</strong>

                    ${denominationsHtml}

                    <hr>

                    <div class="line total">
                        <span>CONTADO</span>
                        <strong>${formatMoney(summary.closing_amount)}</strong>
                    </div>

                    <div class="line difference">
                        <span>${differenceLabel}</span>
                        <strong>${formatMoney(Math.abs(difference))}</strong>
                    </div>

                    <hr>

                    <div class="center">
                        <p>Corte de caja finalizado.</p>
                        <p>Conserve este comprobante.</p>
                    </div>

                </div>
            </body>
            </html>
        `;
    }

    // ==========================================
    // IMPRIMIR CORTE DE CAJA
    // ==========================================

    async function printCashClosingReceipt(result) {

        await connectQZ();

        const printerName = 'POS-58';
        const printer = await qz.printers.find(printerName);

        if (!printer) {
            throw new Error(`No se encontró la impresora ${printerName}.`);
        }

        const config = qz.configs.create(printer, {
            encoding: 'UTF-8',
            units: 'mm',
            size: { width: 58, height: 187 },
            margins: 0,
            scaleContent: true
        });

        await qz.print(config, [
            {
                type: 'pixel',
                format: 'html',
                flavor: 'plain',
                data: buildCashClosingReceipt(result)
            }
        ]);

        console.log('Comprobante de corte enviado a POS-58.');

        return true;
    }

    // ==========================================
    // CONFIRMAR CIERRE DEFINITIVO
    // ==========================================

    async function confirmCashClosing() {

        if (cashClosingInProgress || saleInProgress) return;

        if (!cashCloseSummary) {
            showCashClosingError(
                'Primero debes consultar el resumen de caja.'
            );
            return;
        }

        if (cart.length > 0) {
            showCashClosingError(
                'No puedes cerrar la caja con productos pendientes.'
            );
            return;
        }

        if (!window.cashRegisterRoutes?.close) {
            showCashClosingError(
                'No se configuró la ruta para cerrar caja.'
            );
            return;
        }

        const cash = calculateCashCount();

        const expected = Number(
            cashCloseSummary.expected_amount || 0
        );

        const difference = roundMoney(cash.total - expected);

        const confirmationMessage =
            `Efectivo esperado: ${formatMoney(expected)}\n` +
            `Efectivo contado: ${formatMoney(cash.total)}\n` +
            `Diferencia: ${formatMoney(difference)}\n\n` +
            '¿Confirmas el cierre definitivo de esta caja?';

        if (!window.confirm(confirmationMessage)) return;

        cashClosingInProgress = true;

        hideCashClosingError();

        if (btnConfirmCashClosing) {
            btnConfirmCashClosing.disabled = true;
            btnConfirmCashClosing.textContent = 'Cerrando caja...';
        }

        showLoader();

        let closeRegistered = false;

        try {

            const response = await fetch(window.cashRegisterRoutes.close, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    cash_count: cash.cash_count
                })
            });

            const data = await readJsonResponse(response);

            if (!response.ok || !data.success || !data.session || !data.summary) {

                const errors = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : data.message || 'No fue posible cerrar la caja.';

                throw new Error(errors);
            }

            // El servidor ya confirmó el cierre.
            closeRegistered = true;

            cashClosingOverlay.hidden = true;
            cashClosingOverlay.style.display = 'none';

            hideLoader();

            // Imprimir el corte una sola vez.
            try {

                showLoader();

                await printCashClosingReceipt(data);

                hideLoader();

                showSuccess(
                    'El corte de caja se realizó correctamente y el comprobante fue enviado a POS-58.'
                );

            } catch (printError) {

                hideLoader();

                console.error(
                    'Caja cerrada, pero no se pudo imprimir el corte:',
                    printError
                );

                showError(
                    'La caja ya quedó cerrada correctamente, pero no se pudo imprimir el comprobante. Revisa QZ Tray y la impresora POS-58. No vuelvas a cerrar la caja.'
                );
            }

            /*
             * Recargar después del cierre.
             * Si la impresión falla, la caja NO se vuelve a cerrar.
             */
            setTimeout(function () {
                window.location.reload();
            }, 1800);

        } catch (error) {

            console.error('Error al cerrar caja:', error);

            hideLoader();

            if (!closeRegistered) {

                showCashClosingError(error.message);

                if (btnConfirmCashClosing) {
                    btnConfirmCashClosing.disabled = false;
                    btnConfirmCashClosing.textContent =
                        '🔒 Confirmar corte y cerrar caja';
                }

                cashClosingInProgress = false;
            }
        }
    }

    btnConfirmCashClosing?.addEventListener(
        'click',
        confirmCashClosing
    );

    // ==========================================
    // REGISTRAR VENTA
    // ==========================================

    checkoutForm?.addEventListener('submit', async function (event) {

        event.preventDefault();

        if (saleInProgress || cashClosingInProgress) return;

        if (cart.length === 0) {
            showError('No hay productos para registrar.');
            return;
        }

        if (!window.cashRegisterRoutes?.sales) {
            showError('No se configuró la ruta para registrar ventas.');
            return;
        }

        updateTotals();

        const cashReceived = roundMoney(
            Number(cashReceivedInput?.value) || 0
        );

        if (cashReceived < currentTotals.total) {

            showInlineError(
                checkoutError,
                'El efectivo recibido no cubre el total de la venta.'
            );

            cashReceivedInput?.focus();

            return;
        }

        saleInProgress = true;

        if (btnConfirmSale) {
            btnConfirmSale.disabled = true;
            btnConfirmSale.textContent = 'Registrando venta...';
        }

        hideInlineError(checkoutError);

        let saleRegistered = false;

        showLoader();

        try {

            const payload = {
                customer_id: Number(customerSelect?.value || 0),

                manual_discount_type: discountType?.value || 'amount',

                manual_discount_value: Number(discountValue?.value || 0),

                tax_percentage: Number(taxValue?.value || 0),

                cash_received: cashReceived,

                items: cart.map(item => ({
                    product_id: Number(item.id),
                    quantity_kg: Number(item.weight)
                }))
            };

            const response = await fetch(window.cashRegisterRoutes.sales, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            const data = await readJsonResponse(response);

            if (!response.ok || !data.success || !data.sale) {

                const errors = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : data.message || 'No fue posible registrar la venta.';

                throw new Error(errors);
            }

            saleRegistered = true;

            console.log('Venta registrada:', data.sale);

            // Limpiar venta registrada.
            cart = [];

            if (customerSelect) customerSelect.value = '0';

            customerDiscount = 0;

            if (customerDiscountLabel) {
                customerDiscountLabel.textContent = 'Sin descuento';
            }

            if (discountValue) discountValue.value = '0';
            if (discountType) discountType.value = 'percent';
            if (taxValue) taxValue.value = '0';
            if (weightInput) weightInput.value = '';
            if (searchInput) searchInput.value = '';

            selectedCategory = 'all';

            document.querySelectorAll('.category-btn').forEach(button => {
                button.classList.toggle(
                    'active',
                    button.dataset.category === 'all'
                );
            });

            renderProducts();
            renderTicket();

            closeCheckout();

            await loadProducts();

            // Imprimir una sola vez.
            try {

                await printSaleTicket(data.sale);

                hideLoader();

                showSuccess(
                    `La venta ${data.sale.folio} se registró y se envió a la impresora.`
                );

            } catch (printError) {

                console.error(
                    'Venta registrada, impresión pendiente:',
                    printError
                );

                hideLoader();

                showError(
                    `La venta ${data.sale.folio} se registró correctamente, pero no se pudo imprimir. Revisa QZ Tray y POS-58. No vuelvas a cobrar esta venta.`
                );
            }

        } catch (error) {

            console.error('Error durante el proceso de venta:', error);

            hideLoader();

            if (saleRegistered) {

                showError(
                    'La venta quedó registrada, pero ocurrió un problema al finalizar. No vuelvas a cobrarla. Verifica el historial de ventas.'
                );

            } else {

                showInlineError(checkoutError, error.message);

                if (btnConfirmSale) {
                    btnConfirmSale.disabled = false;
                    btnConfirmSale.textContent = 'Confirmar y cobrar';
                }
            }

        } finally {

            saleInProgress = false;
        }
    });

    // ==========================================
    // INICIALIZAR POS
    // ==========================================

    renderTicket();

    loadProducts();

    loadCustomers();

    console.log('POS inicializado correctamente.');

});
