var base = location.protocol + '//' + location.host;
const http = new XMLHttpRequest();
const csrfToken = document.getElementsByName('csrf-token')[0].getAttribute('content');

document.addEventListener("DOMContentLoaded", function () {

    var code_product = document.getElementById("code_products");
    var product_name = document.getElementById("products_name");
    var no_labels = document.getElementById("no_labels");
    var btn_preview = document.getElementById("btn_preview");
    var btn_print = document.getElementById("print-labels");

    if (code_product) {
        code_product.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                var code = this.value.trim();
                if (!code) {
                    return;
                }
                search_code_to_product_name(code);
            }
        });
    }

    if (btn_preview) {
        btn_preview.addEventListener("click", function () {
            var code = code_product.value.trim();
            var product = product_name.value.trim();
            var quantity = parseInt(no_labels.value);
            if (!code) {
                alert("Ingresa o escanea el código del producto.");
                code_product.focus();
                return;
            }

            if (!product) {
                alert("Ingresa el nombre del producto.");
                product_name.focus();
                return;
            }

            if (!quantity || quantity <= 0) {
                alert("Ingresa el número de etiquetas.");
                no_labels.focus();
                return;
            }
            generate_labels_preview(code, product, quantity);
        });
    }

    if (btn_print) {

        btn_print.addEventListener("click", function () {

            var labels_page = document.getElementById("labelsPagePreview");
            var labels = labels_page.querySelectorAll(".label-preview");

            if (labels.length === 0) {

                alert("Primero genera la vista previa de las etiquetas.");

                return;
            }

            /*
             * Verificar códigos de barras
             */
            var images = Array.from(labels_page.querySelectorAll("img"));

            Promise.all(
                images.map(function (img) {

                    if (img.complete && img.naturalWidth > 0) {
                        return Promise.resolve();
                    }

                    return new Promise(function (resolve) {

                        img.addEventListener("load", resolve, { once: true });

                        img.addEventListener("error", resolve, { once: true });

                    });

                })
            ).then(function () {

                /*
                 * Verificar que todos los códigos cargaron
                 */
                var failed = images.some(function (img) {

                    return img.naturalWidth === 0;

                });

                if (failed) {

                    alert("No se pudieron cargar uno o más códigos de barras.");

                    return;
                }


                /*
                 * Crear ventana de impresión
                 */
                var print_window = window.open("", "_blank");

                if (!print_window) {

                    alert("El navegador bloqueó la ventana de impresión. Permite las ventanas emergentes.");

                    return;
                }


                /*
                 * HTML de impresión
                 */
                print_window.document.open();

                print_window.document.write(`
                <!DOCTYPE html>

                <html>

                <head>

                    <meta charset="UTF-8">

                    <title>Impresión de etiquetas</title>

                    <style>

                        @page {
                            size: A4;
                            margin: 0;
                        }

                        * {
                            box-sizing: border-box;
                        }

                        html,
                        body {
                            margin: 0;
                            padding: 0;
                            background: white;
                        }

                        .labels-page-preview {
                            width: 210mm;
                            min-height: 297mm;
                            padding: 12mm;

                            display: flex;
                            flex-wrap: wrap;
                            align-content: flex-start;

                            gap: 5mm;

                            background: white;
                        }

                        .label-preview {
                            width: 50mm;
                            height: 30mm;

                            padding: 3mm;

                            box-sizing: border-box;

                            background: white;

                            border: 1px dashed #b8bec8;

                            display: flex;
                            flex-direction: column;

                            justify-content: center;
                            align-items: center;

                            text-align: center;

                            overflow: hidden;

                            break-inside: avoid;
                            page-break-inside: avoid;
                        }

                        .label-company {
                            width: 100%;

                            font-size: 10px;

                            font-weight: 700;

                            text-transform: uppercase;

                            margin-bottom: 2mm;
                        }

                        .label-product {
                            width: 100%;

                            font-size: 11px;

                            font-weight: 600;

                            text-transform: uppercase;

                            line-height: 1.2;

                            white-space: nowrap;

                            overflow: hidden;

                            text-overflow: ellipsis;
                        }

                        .label-barcode {
                            width: 100%;

                            margin-top: 2mm;

                            display: flex;

                            justify-content: center;

                            align-items: center;
                        }

                        .barcode-real {
                            width: 90%;

                            max-width: 45mm;

                            height: 12mm;

                            display: block;

                            object-fit: contain;
                        }

                    </style>

                </head>

                <body>

                    ${labels_page.outerHTML}

                </body>

                </html>
            `);

                print_window.document.close();


                /*
                 * Esperar a que carguen los códigos
                 */
                print_window.onload = function () {

                    setTimeout(function () {

                        print_window.focus();

                        print_window.print();

                    }, 500);

                };


                /*
                 * Cerrar ventana después de imprimir
                 */
                print_window.onafterprint = function () {
                    print_window.close();
                };
            });
        });
    }

});


function search_code_to_product_name(code) {
    var product_name = document.getElementById('products_name');
    product_name.value = '';
    var url = base + '/api-js/load/product-by-code/' + code;
    http.open('GET', url, true);
    http.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
                var data = JSON.parse(this.responseText);
                product_name.value = data.name;
            } else if (this.status == 404) {
                product_name.value = 'Producto no encontrado';
            }
        }
    }
}


function generate_labels_preview(code, product, quantity) {
    var labels_page = document.getElementById("labelsPagePreview");

    // Limpiar la hoja
    labels_page.innerHTML = "";

    // Generar cada etiqueta
    for (var i = 0; i < quantity; i++) {

        var label = document.createElement("div");
        label.className = "label-preview";

        // Empresa
        var company = document.createElement("div");
        company.className = "label-company";
        company.innerText = labels_page.dataset.company;

        // Producto
        var product_element = document.createElement("div");
        product_element.className = "label-product";
        product_element.innerText = product;

        // Contenedor del código de barras
        var barcode = document.createElement("div");
        barcode.className = "label-barcode";

        // SVG real generado por Laravel
        var barcode_img = document.createElement("img");
        barcode_img.className = "barcode-real";
        barcode_img.alt = "Código de barras " + code;
        barcode_img.src = base + "/api-js/barcode/" + encodeURIComponent(code);

        // Armar etiqueta
        barcode.appendChild(barcode_img);

        label.appendChild(company);
        label.appendChild(product_element);
        label.appendChild(barcode);

        // Agregar a la hoja
        labels_page.appendChild(label);
    }
}