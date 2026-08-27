document.addEventListener("DOMContentLoaded", function () {
    var form_category_add = document.getElementById("form_category_add");

     if (form_category_add) {
        form_category_add.addEventListener("submit", function (e) {
            e.preventDefault();
            categories_add();
        });
    }
});


function categories_add(){
    loader_action_status("show");

    url = base + "/api-js/categories/add";

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

    http.send(new FormData(document.getElementById("form_category_add")));
}