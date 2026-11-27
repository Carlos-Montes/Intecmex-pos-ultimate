
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('sale-detail-modal');
    const loading = document.getElementById('sale-detail-loading');
    const content = document.getElementById('sale-detail-content');
    const error = document.getElementById('sale-detail-error');

    const closeButtons = [
        document.getElementById('btn-close-sale-detail'),
        document.getElementById('btn-close-sale-detail-footer')
    ];

    const money = value => {
        return window.salesCurrency + Number(value || 0).toLocaleString(
            'es-MX',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    };

    const setText = (id, value) => {
        document.getElementById(id).textContent = value ?? '—';
    };

    function formatDate(value) {
        if (!value) return '—';

        const date = new Date(value);

        if (isNaN(date.getTime())) return value;

        return date.toLocaleString('es-MX');
    }

    async function getSale(url) {

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('No fue posible consultar la venta.');
        }

        const result = await response.json();

        if (!result.success || !result.sale) {
            throw new Error('No se encontró la venta.');
        }

        return result.sale;
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    closeButtons.forEach(button => {
        if (button) {
            button.addEventListener('click', closeModal);
        }
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // VER DETALLE DE VENTA
    document.addEventListener('click', async function (event) {

        const button = event.target.closest('.btn-sales-view');

        if (!button) return;

        modal.classList.add('active');
        loading.style.display = 'flex';
        content.style.display = 'none';
        error.style.display = 'none';

        try {

            const sale = await getSale(button.dataset.url);

            setText('detail-folio', 'Folio: ' + sale.folio);
            setText('detail-date', formatDate(sale.sold_at));
            setText('detail-cashier', sale.cashier || sale.user?.name);
            setText(
                'detail-customer',
                sale.customer?.name || sale.customer_name || sale.customer
            );
            setText('detail-payment', sale.payment_method);
            setText('detail-status', sale.status);

            setText('detail-subtotal', money(sale.subtotal));
            setText('detail-discount', money(sale.discount_amount));
            setText('detail-tax', money(sale.tax_amount));
            setText('detail-total', money(sale.total));
            setText('detail-received', money(sale.cash_received));
            setText('detail-change', money(sale.change_amount));

            const products = document.getElementById('detail-products');
            products.replaceChildren();

            sale.details.forEach(product => {

                const row = document.createElement('tr');

                const nameCell = document.createElement('td');
                const name = document.createElement('strong');
                const code = document.createElement('small');

                name.textContent = product.product_name;
                code.textContent = product.product_code || '';

                nameCell.append(name, document.createElement('br'), code);

                const kgCell = document.createElement('td');
                kgCell.textContent =
                    Number(product.quantity_kg).toFixed(3) + ' kg';

                const priceCell = document.createElement('td');
                priceCell.textContent = money(product.unit_price);

                const subtotalCell = document.createElement('td');
                subtotalCell.textContent = money(product.subtotal);

                row.append(nameCell, kgCell, priceCell, subtotalCell);
                products.appendChild(row);
            });

            loading.style.display = 'none';
            content.style.display = 'block';

        } catch (exception) {

            console.error(exception);

            loading.style.display = 'none';
            error.style.display = 'block';

        }

    });

    // REIMPRIMIR TICKET
    document.addEventListener('click', async function (event) {

        const button = event.target.closest('.btn-sales-print');

        if (!button) return;

        if (typeof window.printSaleTicket !== 'function') {
            alert('No se encontró el módulo de impresión.');
            return;
        }

        const originalContent = button.innerHTML;

        try {

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const sale = await getSale(button.dataset.url);

            await window.printSaleTicket(sale);

        } catch (exception) {

            console.error('Error al reimprimir ticket:', exception);

            alert(
                'No fue posible reimprimir el ticket. Verifica la conexión con la impresora.'
            );

        } finally {

            button.disabled = false;
            button.innerHTML = originalContent;

        }

    });

});
