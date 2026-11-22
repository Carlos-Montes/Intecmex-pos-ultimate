document.addEventListener('DOMContentLoaded', function () {

    const typeUser = document.getElementById('type_user');
    const form_new_user = document.getElementById('form-new-user');

    const userFields = {
        access: document.getElementById('access-user'),
        customer: document.getElementById('customer-user'),
        supplier: document.getElementById('supplier-user')
    };

    function changeUserType() {

        const type = typeUser.value;

        // Ocultar todos los formularios
        Object.values(userFields).forEach(container => {
            container.style.display = 'none';

            // Deshabilitar campos ocultos para que no se envíen
            container.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = true;
            });
        });

        // Mostrar los campos correspondientes
        let selectedContainer;

        switch (type) {
            case '0': // Administrador
            case '1': // Vendedor
                selectedContainer = userFields.access;
                break;

            case '2': // Cliente
                selectedContainer = userFields.customer;
                break;

            case '3': // Proveedor
                selectedContainer = userFields.supplier;
                break;
        }

        if (selectedContainer) {
            selectedContainer.style.display = 'block';

            selectedContainer.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = false;
            });
        }
    }

    typeUser.addEventListener('change', changeUserType);

    // Ejecutar al cargar la pantalla
    changeUserType();

    if(form_new_user){
        form_new_user.addEventListener("submit", function (e) {
            e.preventDefault();
            newUser();
        });
    }

});


function newUser(){
    loader_action_status("show");
    url = base + "/api-js/users/add";

    var http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.setRequestHeader("X-CSRF-TOKEN", csrftoken);

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

    http.send(new FormData(document.getElementById("form-new-user")));
}
