document.addEventListener("DOMContentLoaded", function () {
    var form_profile_edit = document.getElementById("form_profile_edit");

     if (form_profile_edit) {
        form_profile_edit.addEventListener("submit", function (e) {
            e.preventDefault();
            profile_edit();
        });
    }
});


function profile_edit() {
    loader_action_status("show");

    url = base + "/api-js/account/update";

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

    http.send(new FormData(document.getElementById("form_profile_edit")));
}
