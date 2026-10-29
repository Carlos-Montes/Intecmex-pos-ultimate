var base = location.protocol + '//' + location.host;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

document.addEventListener("DOMContentLoaded", function () {
    const animal = document.getElementById("md-animal");
    const category = document.getElementById("md-category");
    const search = document.getElementById("md-search");
    const grid = document.getElementById("md-product-grid");
    const form = document.getElementById("form-meat-distribution");
    const saveButton = document.getElementById("md-save-distribution");

    if (!animal || !category || !search || !grid || !form || !saveButton) return;

    animal.addEventListener("change", function () {
        category.value = this.value;
        filtrarProductos();
        search_merma_animal();
    });

    category.addEventListener("change", filtrarProductos);
    search.addEventListener("input", filtrarProductos);

    search.addEventListener("keydown", function (event) {
        if (event.key !== "Enter") return;

        event.preventDefault();

        const codigo = search.value.trim().toLowerCase();
        if (!codigo) return;

        grid.querySelectorAll(".md-product-card").forEach(function (tarjeta) {
            const codigoProducto =
                (tarjeta.dataset.code || "").trim().toLowerCase();

            if (codigoProducto === codigo && tarjeta.style.display !== "none") {
                const cantidad = tarjeta.querySelector(".md-quantity");

                if (cantidad) {
                    cantidad.focus();
                    cantidad.select();
                }
            }
        });
    });

    grid.addEventListener("input", function (event) {
        if (event.target.classList.contains("md-quantity")) {
            calcularTotalOperacion();
        }
    });

    function filtrarProductos() {
        const texto = search.value.trim().toLowerCase();
        const categoriaSeleccionada = category.value;

        // Selector: Cerdo = 0, Res = 1.
        // products.category_id: Cerdo = 1, Res = 2.
        const categoriaBD = categoriaSeleccionada === "" ? "" : String(Number(categoriaSeleccionada) + 1);

        let encontrados = 0;

        grid.querySelectorAll(".md-product-card").forEach(function (tarjeta) {
            const nombre = (tarjeta.dataset.name || "").toLowerCase();
            const codigo = (tarjeta.dataset.code || "").toLowerCase();
            const categoriaProducto = String(tarjeta.dataset.categoryId || "");

            const coincideBusqueda = nombre.includes(texto) || codigo.includes(texto);
            const coincideCategoria = categoriaSeleccionada === "" || categoriaProducto === categoriaBD;
            const mostrar = coincideBusqueda && coincideCategoria;

            tarjeta.style.display = mostrar ? "" : "none";

            if (mostrar) encontrados++;
        });

        const mensajeVacio = document.getElementById("md-no-products");

        if (mensajeVacio) {
            mensajeVacio.style.display = encontrados === 0 ? "block" : "none";
        }

        calcularTotalOperacion();
    }

    // Enviar la distribución al controlador.
    saveButton.addEventListener("click", async function () {
        const categoryValue = document.getElementById("md-form-category").value;
        const channelWeight = parseFloat(document.getElementById("md-form-channel-weight").value);
        const availableBefore = parseFloat(document.getElementById("md-form-available-before").value);
        const distributedBefore = parseFloat(document.getElementById("md-form-distributed-before").value);
        const totalAssigned = parseFloat(document.getElementById("md-form-total-assigned").value) || 0;
        const availableAfter = parseFloat(document.getElementById("md-form-available-after").value);

        if (categoryValue === "" || !Number.isFinite(channelWeight) || !Number.isFinite(availableBefore) || !Number.isFinite(distributedBefore)) {
            mdalert({
                title: lang["name"],
                type: "error",
                msg: "Selecciona un animal y espera a que se consulten los kilos."
            });
            return;
        }

        if (totalAssigned <= 0) {
            mdalert({
                title: lang["name"],
                type: "error",
                msg: "Ingresa los kilos que deseas distribuir."
            });
            return;
        }

        if (totalAssigned > availableBefore) {
            mdalert({
                title: lang["name"],
                type: "error",
                msg: "No puedes distribuir más kilos de los disponibles."
            });
            return;
        }

        const products = [];

        // Recoger todos los productos con kilos mayores a cero.
        grid.querySelectorAll(".md-product-card").forEach(function (tarjeta) {
            const input = tarjeta.querySelector(".md-quantity");
            if (!input) return;

            const quantity = parseFloat(input.value) || 0;

            if (quantity > 0) {
                products.push({
                    product_id: Number(input.dataset.productId),
                    quantity_kg: quantity
                });
            }
        });

        if (!products.length) {
            mdalert({
                title: lang["name"],
                type: "error",
                msg: "Agrega al menos un producto a la distribución."
            });
            return;
        }

        // Información que se enviará por POST.
        const payload = {
            category: Number(categoryValue),
            channel_weight: channelWeight,
            available_before: availableBefore,
            distributed_before: distributedBefore,
            total_assigned: totalAssigned,
            available_after: availableAfter,
            products: products
        };

        console.log("Datos enviados al controlador:", payload);

        saveButton.disabled = true;

        if (typeof loader_action_status === "function") {
            loader_action_status("show");
        }

        try {
            const response = await fetch(
                base + "/api-js/save/meat-distribution",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify(payload)
                }
            );

            // Leer la respuesta incluso si Laravel devuelve un error no JSON.
            const responseText = await response.text();
            let data = {};

            try {
                data = responseText ? JSON.parse(responseText) : {};
            } catch (error) {
                console.error("Respuesta no JSON del servidor:", responseText);
                throw new Error(
                    "El servidor no devolvió JSON. Revisa la ruta y el controlador."
                );
            }

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    const errores = Object.values(data.errors).flat().join("\n");
                    throw new Error(errores || data.message || "Verifica los datos enviados.");
                }

                throw new Error(
                    data.message || "No se pudo guardar la distribución. HTTP " + response.status
                );
            }

            mdalert({
                title: lang["name"],
                type: "success",
                msg: data.message || "Distribución guardada correctamente."
            });

            if (typeof loader_action_status === "function") {
                loader_action_status("show");
            }

            setTimeout(function () {
                loader_action_status("show");
                window.location.reload();
            }, 1500);;

        } catch (error) {
            console.error("Error al guardar distribución:", error);

            mdalert({
                title: lang["name"],
                type: "error",
                msg: error.message || "Ocurrió un error al guardar la distribución."
            });

        } finally {
            saveButton.disabled = false;

            if (typeof loader_action_status === "function") {
                loader_action_status("hide");
            }
        }
    });

    filtrarProductos();
});

function search_merma_animal() {
    const animal = document.getElementById("md-animal");
    const valueAnimal = animal.value;

    const channelWeight = document.getElementById("channel_weight");
    const kilosAvailable = document.getElementById("kilos_available");
    const kilosDistributed = document.getElementById("kilos_distributed");

    const formCategory = document.getElementById("md-form-category");
    const formChannelWeight = document.getElementById("md-form-channel-weight");
    const formAvailableBefore = document.getElementById("md-form-available-before");
    const formDistributedBefore = document.getElementById("md-form-distributed-before");
    const formTotalAssigned = document.getElementById("md-form-total-assigned");
    const formAvailableAfter = document.getElementById("md-form-available-after");

    channelWeight.innerHTML = '0.00 <small>kg</small>';
    kilosAvailable.innerHTML = '0.00 <small>kg</small>';
    kilosDistributed.innerHTML = '0.00 <small>kg</small>';

    formCategory.value = valueAnimal;
    formChannelWeight.value = "";
    formAvailableBefore.value = "";
    formDistributedBefore.value = "";
    formTotalAssigned.value = "0.00";
    formAvailableAfter.value = "";

    if (valueAnimal === "") {
        calcularTotalOperacion();
        return;
    }

    if (typeof loader_action_status === "function") {
        loader_action_status("show");
    }

    const http = new XMLHttpRequest();
    const url = base + "/api-js/load/weight/" + encodeURIComponent(valueAnimal);

    http.open("GET", url, true);

    if (csrfToken) {
        http.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    }

    http.onreadystatechange = function () {
        if (http.readyState !== 4) return;

        if (typeof loader_action_status === "function") {
            loader_action_status("hide");
        }

        if (http.status >= 200 && http.status < 300) {
            try {
                const data = JSON.parse(http.responseText);

                const canal = Number(data.kilos_canal || 0);
                const distribuidos = Number(data.kilos_distribuidos || 0);
                const disponibles = Number(data.kilos_restantes || 0);

                channelWeight.innerHTML =
                    canal.toFixed(2) + ' <small>kg</small>';

                kilosAvailable.innerHTML =
                    disponibles.toFixed(2) + ' <small>kg</small>';

                kilosDistributed.innerHTML =
                    distribuidos.toFixed(2) + ' <small>kg</small>';

                formCategory.value = valueAnimal;
                formChannelWeight.value = canal.toFixed(2);
                formAvailableBefore.value = disponibles.toFixed(2);
                formDistributedBefore.value = distribuidos.toFixed(2);

                calcularTotalOperacion();

                console.log("Datos actuales de la canal:", {
                    category: formCategory.value,
                    channel_weight: formChannelWeight.value,
                    available_before: formAvailableBefore.value,
                    distributed_before: formDistributedBefore.value,
                    total_assigned: formTotalAssigned.value,
                    available_after: formAvailableAfter.value
                });

            } catch (error) {
                console.error("Error al interpretar la respuesta:", error);
            }
        } else {
            console.error(
                "Error al consultar los kilos:",
                http.status,
                http.responseText
            );
        }
    };

    http.onerror = function () {
        if (typeof loader_action_status === "function") {
            loader_action_status("hide");
        }

        console.error("No se pudo conectar con el servidor.");
    };

    http.send();
}

function calcularTotalOperacion() {
    const grid = document.getElementById("md-product-grid");
    if (!grid) return;

    let total = 0;

    // Sumar todos los productos, incluso los ocultos por el filtro.
    grid.querySelectorAll(".md-quantity").forEach(function (input) {
        total += Math.max(0, parseFloat(input.value) || 0);
    });

    const totalSelected = document.getElementById("md-total-selected");
    const footerTotal = document.getElementById("md-footer-total");

    if (totalSelected) {
        totalSelected.innerHTML = total.toFixed(2) + ' <small>kg</small>';
    }

    if (footerTotal) {
        footerTotal.textContent = total.toFixed(2) + " kg";
    }

    actualizarDatosDistribucion(total);
}

function actualizarDatosDistribucion(total) {
    const availableInput = document.getElementById("md-form-available-before");
    const totalInput = document.getElementById("md-form-total-assigned");
    const afterInput = document.getElementById("md-form-available-after");
    const remaining = document.getElementById("md-remaining");
    const disponibles = parseFloat(availableInput.value);

    totalInput.value = total.toFixed(2);

    if (availableInput.value === "" || !Number.isFinite(disponibles)) {
        afterInput.value = "";

        if (remaining) {
            remaining.textContent =
                "Selecciona un animal para consultar el saldo.";
        }

        return;
    }

    const saldo = disponibles - total;
    afterInput.value = saldo.toFixed(2);

    if (remaining) {
        remaining.textContent = saldo < 0 ? "Excedes los kilos disponibles por " + Math.abs(saldo).toFixed(2) + " kg." : "Saldo después de distribuir: " + saldo.toFixed(2) + " kg.";
    }
}
