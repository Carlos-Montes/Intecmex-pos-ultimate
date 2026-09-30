document.addEventListener("DOMContentLoaded", function () {
    var form_brand_add = document.getElementById("form_brand_add");

     if (form_brand_add) {
        form_brand_add.addEventListener("submit", function (e) {
            e.preventDefault();
            brands_add();
        });
    }
});

function brands_add(){
    loader_action_status("show");

    url = base + "/api-js/brands/add";

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

    http.send(new FormData(document.getElementById("form_brand_add")));
}
