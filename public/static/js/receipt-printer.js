
(function () {
    let qzConfigured = false;
    let qzConnectionPromise = null;

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content ||
            document.querySelector('[name="_token"]')?.value || '';
    }

    function formatMoney(value) {
        return '$' + Number(value || 0).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

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
                headers: { 'Accept': 'text/plain' }
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
                    body: new URLSearchParams({ request: toSign })
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
    }

    async function connectQZ() {
        if (typeof qz === 'undefined') {
            throw new Error('QZ Tray no está cargado en el navegador.');
        }

        configureQZ();

        if (qz.websocket.isActive()) return true;

        if (!qzConnectionPromise) {
            qzConnectionPromise = qz.websocket.connect()
                .then(() => true)
                .catch(error => {
                    qzConnectionPromise = null;
                    throw error;
                });
        }

        return qzConnectionPromise;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function buildReceiptHtml(sale) {
        const details = Array.isArray(sale.details) ? sale.details : [];

        const customerName = sale.customer?.name || 'Público General';
        const sellerName = sale.user?.name || 'Vendedor';

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

    async function printSaleTicket(sale) {
        if (!sale || !sale.folio) {
            throw new Error('No se recibieron los datos de la venta.');
        }

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

        await qz.print(config, [{
            type: 'pixel',
            format: 'html',
            flavor: 'plain',
            data: buildReceiptHtml(sale)
        }]);

        console.log('Ticket enviado a POS-58:', sale.folio);

        return true;
    }

    window.printSaleTicket = printSaleTicket;

})();
