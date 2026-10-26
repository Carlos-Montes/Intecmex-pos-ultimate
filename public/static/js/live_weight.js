var base = location.protocol + '//' + location.host;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    || document.querySelector('[name="csrf-token"]')?.getAttribute('content')
    || '';

document.addEventListener("DOMContentLoaded", function () {
    const form_weight = document.getElementById("form_live_weight");
    const input_date = document.getElementById("search_pig_res");

    if (form_weight) {
        form_weight.addEventListener("submit", function (e) {
            e.preventDefault();
            live_weight_add();
        });
    }

    if (input_date) {
        input_date.addEventListener("change", function () {
            if (!this.value) return;
            search_res_pig();
        });
    }

    const animalType = document.getElementById("animal-type");
    const animalRecord = document.getElementById("animal-record");
    const percentageInput = document.getElementById("merma-percentage");
    const pendingAnimals = document.getElementById("pending-animals");
    const editForm = document.getElementById("form-edit-weight");
    const saveButton = document.getElementById("btn-save-merma");

    if (animalType) {
        animalType.addEventListener("change", function () {
            render_animals();
            calculate_merma();
        });
    }

    if (animalRecord) {
        animalRecord.addEventListener("change", function () {
            const type = animalType.value;
            const animals = type === "0" ? animalsData.pig : animalsData.cattle;

            const animal = animals.find(
                item => String(item.id) === String(this.value)
            );

            update_animal_details(animal || null, type);
            calculate_merma();
        });
    }

    if (percentageInput) {
        percentageInput.addEventListener("input", calculate_merma);
    }

    if (pendingAnimals) {
        pendingAnimals.addEventListener("click", function (e) {
            const button = e.target.closest(".btn-edit-weight");

            if (!button) return;

            const id = button.dataset.animalId;
            const type = button.dataset.animalType;
            const weight = button.dataset.animalWeight;

            document.getElementById("edit-weight-id").value = id;
            document.getElementById("edit-weight-type").value = type;
            document.getElementById("edit-weight-value").value = weight;

            document.getElementById("edit-weight-animal").value =
                (type === "0" ? "Cerdo" : "Res") +
                " #" + String(id).padStart(4, "0");

            bootstrap.Modal
                .getOrCreateInstance(
                    document.getElementById("modal-edit-weight")
                )
                .show();
        });
    }

    if (editForm) {
        editForm.addEventListener("submit", function (e) {
            guardar_cambio_peso(e, this);
        });
    }

    if (saveButton) {
        saveButton.addEventListener("click", guardar_merma);
    }
});


// ======================================================
// AGREGAR PESO VIVO
// ======================================================

function live_weight_add() {
    const form = document.getElementById("form_live_weight");

    if (!form) return;

    const request = new XMLHttpRequest();

    loader_action_status("show");

    request.open("POST", base + "/api-js/weight/add", true);
    request.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    request.setRequestHeader("Accept", "application/json");

    request.onreadystatechange = function () {
        if (request.readyState !== 4) return;

        loader_action_status("hide");

        if (request.status >= 200 && request.status < 300) {
            try {
                const data = JSON.parse(request.responseText);
                mdalert(data);
            } catch (error) {
                console.error(request.responseText);

                mdalert({
                    title: lang["name"],
                    type: "error",
                    msg: "No se pudo interpretar la respuesta del servidor."
                });
            }
        } else {
            mdalert({
                title: lang["name"],
                type: "error",
                msg: lang["error"]
            });
        }
    };

    request.onerror = function () {
        loader_action_status("hide");

        mdalert({
            title: lang["name"],
            type: "error",
            msg: "No se pudo conectar con el servidor."
        });
    };

    request.send(new FormData(form));
}


// ======================================================
// CONSULTAR ANIMALES POR FECHA
// ======================================================

function search_res_pig() {
    const dateInput = document.getElementById("search_pig_res");
    const date = dateInput ? dateInput.value : "";

    if (!date) {
        mdalert({
            title: lang["name"],
            type: "error",
            msg: lang["error"]
        });
        return;
    }

    const input_pig = document.getElementById("pig-strong");
    const input_res = document.getElementById("res-strong");

    const request = new XMLHttpRequest();

    loader_action_status("show");

    request.open(
        "GET",
        base + "/api-js/load/animals/" + encodeURIComponent(date),
        true
    );

    request.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    request.setRequestHeader("Accept", "application/json");

    request.onreadystatechange = function () {
        if (request.readyState !== 4) return;

        loader_action_status("hide");

        if (request.status >= 200 && request.status < 300) {
            try {
                const data = JSON.parse(request.responseText);

                animalsData = {
                    pig: Array.isArray(data.pig) ? data.pig : [],
                    cattle: Array.isArray(data.cattle) ? data.cattle : []
                };

                if (input_pig) {
                    input_pig.textContent = animalsData.pig.length;
                }

                if (input_res) {
                    input_res.textContent = animalsData.cattle.length;
                }

                render_animals();
                render_pending_animals();
                calculate_merma();

            } catch (error) {
                console.error("Error al procesar la respuesta:", error);

                mdalert({
                    title: lang["name"],
                    type: "error",
                    msg: "No se pudieron cargar los animales."
                });
            }
        } else {
            console.error(
                "Error en la petición:",
                request.status,
                request.responseText
            );

            mdalert({
                title: lang["name"],
                type: "error",
                msg: "No se pudieron consultar los animales."
            });
        }
    };

    request.onerror = function () {
        loader_action_status("hide");

        mdalert({
            title: lang["name"],
            type: "error",
            msg: "No se pudo conectar con el servidor."
        });
    };

    request.send();
}


// ======================================================
// DATOS DE ANIMALES
// ======================================================

let animalsData = {
    pig: [],
    cattle: []
};


// ======================================================
// CARGAR SELECTOR DE ANIMALES
// ======================================================

function load_animals_selector(data) {
    animalsData = {
        pig: Array.isArray(data.pig) ? data.pig : [],
        cattle: Array.isArray(data.cattle) ? data.cattle : []
    };

    render_animals();
}


function render_animals() {
    const typeInput = document.getElementById("animal-type");
    const select = document.getElementById("animal-record");

    if (!typeInput || !select) return;

    const type = typeInput.value;
    const animals = type === "0" ? animalsData.pig : animalsData.cattle;

    select.innerHTML = "";

    if (!animals || animals.length === 0) {
        select.innerHTML =
            '<option value="">No hay animales registrados</option>';

        update_animal_details(null, type);
        return;
    }

    animals.forEach(function (animal) {
        const option = document.createElement("option");

        option.value = animal.id;
        option.textContent =
            (type === "0" ? "Cerdo" : "Res") +
            " #" + String(animal.id).padStart(4, "0");

        option.dataset.weight = animal.weight;
        option.dataset.date = animal.registration_date;
        option.dataset.type = type;

        select.appendChild(option);
    });

    select.selectedIndex = 0;

    update_animal_details(animals[0], type);
}


// ======================================================
// MOSTRAR DETALLES DEL ANIMAL SELECCIONADO
// ======================================================

function update_animal_details(animal, type) {
    const emoji = document.getElementById("animal-emoji");
    const title = document.getElementById("animal-title");
    const dateLabel = document.getElementById("animal-date");
    const detailDate = document.getElementById("animal-detail-date");
    const weight = document.getElementById("animal-live-weight");
    const calc = document.getElementById("calc-live-weight");

    if (!emoji || !title || !dateLabel || !detailDate || !weight || !calc) {
        return;
    }

    if (!animal) {
        emoji.textContent = type === "0" ? "🐖" : "🐄";
        title.textContent = "Sin animales disponibles";
        dateLabel.textContent = "";
        detailDate.textContent = "—";
        weight.textContent = "0.00";
        calc.textContent = "0.00 Kg";
        return;
    }

    const name = type === "0" ? "Cerdo" : "Res";
    const emojiValue = type === "0" ? "🐖" : "🐄";

    const parts = String(animal.registration_date || "").split("-");
    const formattedDate = parts.length === 3
        ? `${parts[2]}-${parts[1]}-${parts[0]}`
        : animal.registration_date || "—";

    emoji.textContent = emojiValue;
    title.textContent =
        `${name} #${String(animal.id).padStart(4, "0")}`;

    dateLabel.textContent = `Registrado el ${formattedDate}`;
    detailDate.textContent = formattedDate;
    weight.textContent = Number(animal.weight).toFixed(2);
    calc.textContent = Number(animal.weight).toFixed(2) + " Kg";
}


// ======================================================
// TABLA DE ANIMALES PENDIENTES
// ======================================================

function render_pending_animals() {
    const tbody = document.getElementById("pending-animals");

    if (!tbody) return;

    tbody.innerHTML = "";

    const animals = [
        ...animalsData.pig.map(animal => ({
            ...animal,
            type: "0"
        })),
        ...animalsData.cattle.map(animal => ({
            ...animal,
            type: "1"
        }))
    ];

    if (animals.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">No hay animales pendientes de merma.</td>
            </tr>
        `;
        return;
    }

    animals.forEach(function (animal, index) {
        const isPig = animal.type === "0";
        const name = isPig ? "Cerdo" : "Res";
        const emoji = isPig ? "🐖" : "🐄";

        const parts = String(animal.registration_date || "").split("-");
        const date = parts.length === 3
            ? `${parts[2]}-${parts[1]}-${parts[0]}`
            : animal.registration_date || "—";

        const row = document.createElement("tr");

        row.dataset.animalId = animal.id;
        row.dataset.animalType = animal.type;

        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${emoji} ${name}</td>
            <td>#${String(animal.id).padStart(4, "0")}</td>
            <td>${date}</td>
            <td class="animal-weight">
                ${Number(animal.weight).toFixed(2)} Kg
            </td>
            <td>
                <span class="merma-badge pending">Pendiente</span>
            </td>
            <td>
                <div class="opts">
                    <a
                        href="#"
                        class="edit btn-edit-weight"
                        title="Editar peso"
                        data-animal-id="${animal.id}"
                        data-animal-type="${animal.type}"
                        data-animal-weight="${animal.weight}">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </div>
            </td>
        `;

        tbody.appendChild(row);
    });
}


// ======================================================
// EDITAR PESO DEL ANIMAL
// ======================================================

function guardar_cambio_peso(e, form) {
    e.preventDefault();

    const id = document.getElementById("edit-weight-id").value;
    const category = document.getElementById("edit-weight-type").value;
    const weight = document.getElementById("edit-weight-value").value.trim();

    if (!weight || !Number.isFinite(Number(weight)) || Number(weight) <= 0) {
        mdalert({
            title: lang["name"],
            type: "error",
            msg: "Ingresa un peso válido."
        });
        return;
    }

    const button = form.querySelector('[type="submit"]');

    if (button && button.disabled) return;

    if (button) button.disabled = true;

    loader_action_status("show");

    const request = new XMLHttpRequest();

    request.open("POST", base + "/api-js/weight/update", true);

    request.setRequestHeader(
        "Content-Type",
        "application/x-www-form-urlencoded; charset=UTF-8"
    );

    request.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    request.setRequestHeader("Accept", "application/json");

    request.onreadystatechange = function () {
        if (request.readyState !== 4) return;

        if (button) button.disabled = false;

        loader_action_status("hide");

        let response;

        try {
            response = JSON.parse(request.responseText);
        } catch (error) {
            console.error(request.responseText);

            mdalert({
                title: lang["name"],
                type: "error",
                msg: "No se pudo procesar la respuesta del servidor."
            });
            return;
        }

        if (
            request.status < 200 ||
            request.status >= 300 ||
            !response.success
        ) {
            mdalert({
                title: lang["name"],
                type: "error",
                msg: response.message || "No se pudo actualizar el peso."
            });
            return;
        }

        const updatedWeight = Number(response.weight ?? weight);

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById("modal-edit-weight"))
            .hide();

        const row = document.querySelector(
            `#pending-animals tr[data-animal-id="${id}"][data-animal-type="${category}"]`
        );

        if (row) {
            const weightCell = row.querySelector(".animal-weight");
            const editButton = row.querySelector(".btn-edit-weight");

            if (weightCell) {
                weightCell.textContent = updatedWeight.toFixed(2);
            }

            if (editButton) {
                editButton.dataset.animalWeight = updatedWeight;
            }
        }

        const animals = category === "0"
            ? animalsData.pig
            : animalsData.cattle;

        const animal = animals.find(
            item => String(item.id) === String(id)
        );

        if (animal) {
            animal.weight = updatedWeight;
        }

        const option = document.querySelector(
            `#animal-record option[value="${id}"]`
        );

        if (option) {
            option.dataset.weight = updatedWeight;
        }

        if (
            document.getElementById("animal-type").value === category &&
            document.getElementById("animal-record").value === String(id)
        ) {
            update_animal_details(animal || null, category);
            calculate_merma();
        }

        mdalert({
            title: lang["name"],
            type: "success",
            msg: response.message || "Peso actualizado correctamente."
        });
    };

    request.onerror = function () {
        if (button) button.disabled = false;

        loader_action_status("hide");

        mdalert({
            title: lang["name"],
            type: "error",
            msg: "No se pudo conectar con el servidor."
        });
    };

    request.send(new URLSearchParams({
        id: id,
        category: category,
        weight: weight
    }).toString());
}


// ======================================================
// CALCULAR MERMA
// ======================================================


function calculate_merma() {
    const typeInput = document.getElementById("animal-type");
    const recordInput = document.getElementById("animal-record");
    const percentageInput = document.getElementById("merma-percentage");

    if (!typeInput || !recordInput || !percentageInput) {
        return;
    }

    const type = typeInput.value;
    const animalId = recordInput.value;

    const animals = type === "0"
        ? animalsData.pig
        : animalsData.cattle;

    const animal = animals.find(
        item => String(item.id) === String(animalId)
    );

    const liveWeight = animal && Number.isFinite(Number(animal.weight))
        ? Math.max(0, Number(animal.weight))
        : 0;

    const rawPercentage = percentageInput.value.trim() === ""
        ? 0
        : Number(percentageInput.value);

    const percentage = Number.isFinite(rawPercentage)
        ? Math.min(100, Math.max(0, rawPercentage))
        : 0;

    const lossWeight = liveWeight * percentage / 100;
    const finalWeight = liveWeight - lossWeight;
    const finalPercentage = 100 - percentage;

    const formatKg = value => value.toFixed(2) + " Kg";

    // Actualizar indicadores del cálculo.
    document.getElementById("calc-live-weight").textContent =
        formatKg(liveWeight);

    document.getElementById("calc-loss-weight").textContent =
        formatKg(lossWeight);

    document.getElementById("calc-final-weight").textContent =
        formatKg(finalWeight);

    // Actualizar representación visual.
    document.getElementById("visual-live-weight").textContent =
        formatKg(liveWeight);

    document.getElementById("visual-loss-weight").textContent =
        formatKg(lossWeight);

    document.getElementById("visual-final-weight").textContent =
        formatKg(finalWeight);

    document.getElementById("visual-loss-percent").textContent =
        percentage.toFixed(1) + "%";

    document.getElementById("visual-final-percent").textContent =
        finalPercentage.toFixed(1) + "%";

    // Actualizar barras.
    document.getElementById("bar-live").style.width =
        (liveWeight > 0 ? 100 : 0) + "%";

    document.getElementById("bar-loss").style.width =
        percentage + "%";

    document.getElementById("bar-final").style.width =
        finalPercentage + "%";
}



// ======================================================
// GUARDAR MERMA: CONFIRMAR ANTES DE ENVIAR
// ======================================================


function guardar_merma() {
    const button = document.getElementById("btn-save-merma");

    if (!button || button.disabled) return;

    const animalId = document.getElementById("animal-record").value;
    const percentage = document.getElementById("merma-percentage").value.trim();
    const type = document.getElementById("animal-type").value;

    const animals = type === "0" ? animalsData.pig : animalsData.cattle;

    const animal = animals.find(
        item => String(item.id) === String(animalId)
    );

    if (!animal) {
        mdalert({
            title: lang["name"],
            type: "error",
            msg: "Selecciona un animal antes de guardar la merma."
        });
        return;
    }

    const mermaPercentage = Number(percentage);

    if (
        percentage === "" ||
        !Number.isFinite(mermaPercentage) ||
        mermaPercentage < 0 ||
        mermaPercentage > 100
    ) {
        mdalert({
            title: lang["name"],
            type: "error",
            msg: "Ingresa un porcentaje de merma entre 0 y 100."
        });
        return;
    }

    const pesoVivo = Number(animal.weight);

    if (!Number.isFinite(pesoVivo) || pesoVivo <= 0) {
        mdalert({
            title: lang["name"],
            type: "error",
            msg: "El peso vivo del animal no es válido."
        });
        return;
    }

    const pesoMerma = pesoVivo * mermaPercentage / 100;
    const pesoCanal = pesoVivo - pesoMerma;

    button.disabled = true;
    loader_action_status("show");

    const request = new XMLHttpRequest();

    request.open("POST", base + "/api-js/merma/save", true);
    request.setRequestHeader(
        "Content-Type",
        "application/x-www-form-urlencoded; charset=UTF-8"
    );
    request.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    request.setRequestHeader("Accept", "application/json");

    request.onreadystatechange = function () {
        if (request.readyState !== 4) return;

        loader_action_status("hide");

        let response;

        try {
            response = JSON.parse(request.responseText);
        } catch (error) {
            button.disabled = false;
            console.error(request.responseText);

            mdalert({
                title: lang["name"],
                type: "error",
                msg: "No se pudo interpretar la respuesta del servidor."
            });
            return;
        }

        if (
            request.status < 200 ||
            request.status >= 300 ||
            response.type !== "success"
        ) {
            button.disabled = false;

            let message = response.msg || "No se pudo guardar la merma.";

            if (response.msgs) {
                try {
                    const errors = typeof response.msgs === "string"
                        ? JSON.parse(response.msgs)
                        : response.msgs;

                    if (Array.isArray(errors)) {
                        message = errors.join("\n");
                    }
                } catch (error) {
                    // Conservar el mensaje original.
                }
            }

            mdalert({
                title: response.title || lang["name"],
                type: "error",
                msg: message
            });
            return;
        }

        animal.merma_processed = true;
        animal.merma_id = response.data?.id;

        const row = document.querySelector(
            '#pending-animals tr[data-animal-id="' +
            animal.id +
            '"][data-animal-type="' + type + '"]'
        );

        if (row) {
            const badge = row.querySelector(".merma-badge");

            if (badge) {
                badge.textContent = "Procesado";
                badge.classList.remove("pending");
                badge.classList.add("processed");
            }

            const editButton = row.querySelector(".btn-edit-weight");

            if (editButton) editButton.disabled = true;
        }

        const status = document.getElementById("animal-status");

        if (status) {
            status.textContent = "Merma procesada";
            status.classList.remove("pending");
            status.classList.add("processed");
        }

        const option = document.querySelector(
            '#animal-record option[value="' + animal.id + '"]'
        );

        if (option) {
            option.disabled = true;

            if (!option.textContent.includes("Procesado")) {
                option.textContent += " — Procesado";
            }
        }

        const counter = document.getElementById(
            type === "0" ? "pig-strong" : "res-strong"
        );

        if (counter) {
            counter.textContent = Math.max(
                0,
                (Number(counter.textContent.trim()) || 0) - 1
            );
        }

        const carcassWeight = Number(
            response.data?.carcass_weight ?? pesoCanal
        );

        button.disabled = false;

        mdalert({
            title: response.title || lang["name"],
            type: "success",
            msg:
                (response.msg || "Merma guardada correctamente.") +
                "\nPeso canal calculado: " +
                carcassWeight.toFixed(2) + " kg."
        });
    };

    request.onerror = function () {
        loader_action_status("hide");
        button.disabled = false;

        mdalert({
            title: lang["name"],
            type: "error",
            msg: "No se pudo conectar con el servidor."
        });
    };

    request.send(new URLSearchParams({
        live_weight_id: animal.id,
        merma_percentage: mermaPercentage
    }).toString());
}
