var base = location.protocol + '//' + location.host;
const http = new XMLHttpRequest();
const csrfToken = document.getElementsByName('csrf-token')[0].getAttribute('content');

document.addEventListener("DOMContentLoaded", function () {
    var form_weight = document.getElementById("form_live_weight");
    var input_date = document.getElementById("search_pig_res");

    if (form_weight) {
        form_weight.addEventListener("submit", function (e) {
            e.preventDefault();
            live_weight_add();
        });
    }


    if (input_date) {
        input_date.addEventListener("change", function () {
            if (!this.value) {
                return;
            }
            search_res_pig();
        });
    }
});


function live_weight_add() {
    loader_action_status("show");

    url = base + "/api-js/weight/add";

    var http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    http.onreadystatechange = function () {
        if (this.readyState == "4" && this.status == "200") {
            data = this.responseText;
            data = JSON.parse(data);
            mdalert(data);
        }

        if (this.status != "200") {
            mdalert({
                title: lang["name"],
                type: "error",
                msg: lang["error"],
            });
        }
        loader_action_status("hide");
    };

    http.send(new FormData(document.getElementById("form_live_weight")));
}


function search_res_pig() {
    const date = document.getElementById("search_pig_res").value;

    if (!date) {
        mdalert({
            title: lang["name"],
            type: "error",
            msg: lang["error"],
        });
        return;
    }

    const input_pig = document.getElementById("pig-strong");
    const input_res = document.getElementById("res-strong");

    loader_action_status("show");

    const url = base + '/api-js/load/animals/' + date;

    http.open('GET', url, true);
    http.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    http.onreadystatechange = function () {
        if (this.readyState === 4) {
            loader_action_status("hide");

            if (this.status === 200) {
                try {
                    const data = JSON.parse(this.responseText);

                    // Actualizar cantidades
                    input_pig.textContent = data.pig.length;
                    input_res.textContent = data.cattle.length;

                    // Llenar selector y actualizar detalles del animal
                    load_animals_selector(data);
                    render_pending_animals();
                } catch (error) {
                    console.error("Error al procesar la respuesta:", error);
                }
            } else {
                console.error("Error en la petición:", this.status);
            }
        }
    };

    http.send();
}


let animalsData = {
    pig: [],
    cattle: []
};

// Llamar esto cuando recibas la respuesta de Laravel.
function load_animals_selector(data) {
    animalsData = data;

    render_animals();
}

// Filtrar y llenar el selector según el tipo elegido.
function render_animals() {
    const type = document.getElementById("animal-type").value;
    const select = document.getElementById("animal-record");

    const animals = type === "0"
        ? animalsData.pig
        : animalsData.cattle;

    select.innerHTML = "";

    if (!animals || animals.length === 0) {
        select.innerHTML = '<option value="">No hay animales registrados</option>';
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

    update_animal_details(animals[0], type);
}

// Mostrar los datos del animal seleccionado.
function update_animal_details(animal, type) {
    const emoji = document.getElementById("animal-emoji");
    const title = document.getElementById("animal-title");
    const dateLabel = document.getElementById("animal-date");
    const detailDate = document.getElementById("animal-detail-date");
    const weight = document.getElementById("animal-live-weight");
    const calc = document.getElementById("calc-live-weight");

    if (!animal) {
        emoji.textContent = type === "0" ? "🐖" : "🐄";
        title.textContent = "Sin animales disponibles";
        dateLabel.textContent = "";
        detailDate.textContent = "—";
        weight.textContent = "0.00";
        calc.textContent = "0.00";
        return;
    }

    const name = type === "0" ? "Cerdo" : "Res";
    const emojiValue = type === "0" ? "🐖" : "🐄";

    // Convertir YYYY-MM-DD a DD-MM-YYYY sin problemas de zona horaria.
    const parts = animal.registration_date.split("-");
    const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;

    emoji.textContent = emojiValue;
    title.textContent = `${name} #${String(animal.id).padStart(4, "0")}`;
    dateLabel.textContent = `Registrado el ${formattedDate}`;
    detailDate.textContent = formattedDate;
    weight.textContent = Number(animal.weight).toFixed(2);
    calc.textContent = Number(animal.weight).toFixed(2) + ' Kg';
}

// Cambiar entre cerdos y reses.
document.getElementById("animal-type").addEventListener("change", function () {
    render_animals();
});

// Cambiar el animal seleccionado.
document.getElementById("animal-record").addEventListener("change", function () {
    const type = document.getElementById("animal-type").value;
    const animals = type === "0" ? animalsData.pig : animalsData.cattle;

    const animal = animals.find(item => String(item.id) === this.value);

    update_animal_details(animal, type);
});

function render_pending_animals() {
    const tbody = document.getElementById("pending-animals");

    if (!tbody) return;

    tbody.innerHTML = "";

    // Unir cerdos y reses en una sola lista
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

    animals.forEach((animal, index) => {
        const isPig = animal.type === "0";
        const name = isPig ? "Cerdo" : "Res";
        const emoji = isPig ? "🐖" : "🐄";

        const parts = animal.registration_date.split("-");
        const date = `${parts[2]}-${parts[1]}-${parts[0]}`;

        const row = document.createElement("tr");

        // Identificar la fila por el ID del animal.
        row.dataset.animalId = animal.id;

        row.innerHTML = `
        <td>${index + 1}</td>
        <td>${emoji} ${name}</td>
        <td>#${String(animal.id).padStart(4, "0")}</td>
        <td>${date}</td>
        <td class="animal-weight">${Number(animal.weight).toFixed(2)}</td>
        <td>
            <span class="merma-badge pending">Pendiente</span>
        </td>
        <td>
            <div class="opts">
                <button
                    type="button"
                    class="edit btn-edit-weight"
                    title="Editar peso"
                    data-animal-id="${animal.id}"
                    data-animal-type="${animal.type}"
                    data-animal-weight="${animal.weight}">
                    <i class="bi bi-pencil-square"></i>
                </button>
            </div>
        </td>
    `;

        tbody.appendChild(row);
    });
}


document.getElementById("pending-animals").addEventListener("click", function (e) {
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
        .getOrCreateInstance(document.getElementById("modal-edit-weight"))
        .show();
});




document.getElementById("form-edit-weight").addEventListener("submit", function (e) {
    e.preventDefault();

    const form = this;
    const id = document.getElementById("edit-weight-id").value;
    const category = document.getElementById("edit-weight-type").value;
    const weight = document.getElementById("edit-weight-value").value;

    if (!weight || Number(weight) <= 0) {
        mdalert({
            title: lang["name"],
            type: "error",
            msg: "Ingresa un peso válido."
        });
        return;
    }

    loader_action_status("show");

    const button = form.querySelector('[type="submit"]');
    button.disabled = true;

    const request = new XMLHttpRequest();

    request.open("POST", base + "/api-js/weight/update", true);

    request.setRequestHeader(
        "Content-Type",
        "application/x-www-form-urlencoded; charset=UTF-8"
    );
    request.setRequestHeader("X-CSRF-TOKEN", csrfToken);

    request.onreadystatechange = function () {
        if (request.readyState !== 4) return;

        button.disabled = false;
        loader_action_status("hide");

        if (request.status >= 200 && request.status < 300) {
            try {
                const response = JSON.parse(request.responseText);

                if (response.success) {
                    // Cerrar el modal.
                    bootstrap.Modal
                        .getOrCreateInstance(
                            document.getElementById("modal-edit-weight")
                        )
                        .hide();

                    // Actualizar el peso en la tabla, sin recargarla.
                    const row = document.querySelector(
                        `#pending-animals tr[data-animal-id="${id}"]`
                    );

                    if (row) {
                        const weightCell = row.querySelector(".animal-weight");

                        if (weightCell) {
                            weightCell.textContent =
                                Number(response.weight ?? weight).toFixed(2);
                        }
                    }

                    // Actualizar los datos guardados en JavaScript.
                    ["pig", "cattle"].forEach(function (type) {
                        const animal = animalsData[type].find(
                            item => String(item.id) === String(id)
                        );

                        if (animal) {
                            animal.weight = response.weight ?? weight;
                        }
                    });

                    // Actualizar el selector si el animal está seleccionado.
                    const option = document.querySelector(
                        `#animal-record option[value="${id}"]`
                    );

                    if (option) {
                        option.dataset.weight = response.weight ?? weight;

                        const selectedType =
                            document.getElementById("animal-type").value;

                        if (
                            document.getElementById("animal-record").value === String(id)
                        ) {
                            const animals =
                                selectedType === "0"
                                    ? animalsData.pig
                                    : animalsData.cattle;

                            const animal = animals.find(
                                item => String(item.id) === String(id)
                            );

                            if (animal) {
                                update_animal_details(animal, selectedType);
                            }
                        }
                    }

                    mdalert({
                        title: lang["name"],
                        type: "success",
                        msg: response.message ||
                            "Peso actualizado correctamente."
                    });

                } else {
                    mdalert({
                        title: lang["name"],
                        type: "error",
                        msg: response.message ||
                            "No se pudo actualizar el peso."
                    });
                }

            } catch (error) {
                console.error("Error al procesar la respuesta:", error);
                console.error(request.responseText);

                mdalert({
                    title: lang["name"],
                    type: "error",
                    msg: "No se pudo procesar la respuesta del servidor."
                });
            }
        } else {
            console.error(
                "Error al actualizar:",
                request.status,
                request.responseText
            );

            mdalert({
                title: lang["name"],
                type: "error",
                msg: "No se pudo actualizar el peso."
            });
        }
    };

    request.send(new URLSearchParams({
        id: id,
        category: category,
        weight: weight
    }).toString());
});
