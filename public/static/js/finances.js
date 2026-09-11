document.addEventListener("DOMContentLoaded", function () {
    var form_account_add = document.getElementById("form_account_add");

    if (form_account_add) {
        form_account_add.addEventListener("submit", function (e) {
            e.preventDefault();
            finances_add();
        });
    }

    var edit_buttons = document.getElementsByClassName("btn-edit-account");

    if (edit_buttons) {
        Array.from(edit_buttons).forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                edit_account(btn);
            });
        });
    }

    var btn_cancel = document.getElementById("btn_account_cancel");

    if (btn_cancel) {
        btn_cancel.addEventListener("click", function (e) {
            e.preventDefault();
            cancel_account();
        });
    }
});

var account_edit_id = null;

function edit_account(btn) {
    account_edit_id = btn.getAttribute("data-id");

    document.querySelector('#form_account_add input[name="account_number"]').value = btn.getAttribute("data-account_number");
    document.querySelector('#form_account_add input[name="name"]').value = btn.getAttribute("data-name");
    document.querySelector('#form_account_add input[name="balance"]').value = btn.getAttribute("data-balance");
    document.querySelector('#form_account_add select[name="status"]').value = btn.getAttribute("data-status");

    var submit_btn = document.querySelector('#form_account_add button[type="submit"]');
    submit_btn.textContent = "Actualizar";

    document.getElementById("btn_account_cancel").classList.remove("hide");
}

function cancel_account() {
    account_edit_id = null;

    document.getElementById("form_account_add").reset();

    var submit_btn = document.querySelector('#form_account_add button[type="submit"]');
    submit_btn.textContent = "Guardar";

    document.getElementById("btn_account_cancel").classList.add("hide");
}

function finances_add() {
    loader_action_status("show");

    if (account_edit_id) {
        url = base + "/api-js/account/" + account_edit_id + "/edit";
    } else {
        url = base + "/api-js/accounts/add";
    }

    var http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.setRequestHeader("X-CSRF-TOKEN", csrftoken);

    http.onreadystatechange = function () {
        if (this.readyState == "4" && this.status == "200") {
            data = JSON.parse(this.responseText);

            if (data.type == "success") {
                window.location.reload();
            } else {
                mdalert(data);
            }
        }

        if (this.status != "200") {
            mdalert({
                title: lang["app_name"],
                type: "error",
                msg: lang["error"],
            });
        }
        loader_action_status("hide");
    };

    http.send(new FormData(document.getElementById("form_account_add")));
}
