var base = location.protocol+'//'+location.host;
const http = new XMLHttpRequest();
const csrfToken = document.getElementsByName('csrf-token')[0].getAttribute('content');
document.addEventListener("DOMContentLoaded", function () {
    var btn_search = document.getElementById('btn_search');

    var category = document.getElementById('category');

    if(btn_search){
        btn_search.addEventListener('click', function(e){
            e.preventDefault();
            if(form_search.style.display === 'block'){
                form_search.style.display = 'none';
            }else{
                form_search.style.display = 'block';
            }
        })
    }

    if(route == 'product_add'){
        setSubCategoriesToProducts();
    }

    if(route == "product_edit"){
		setSubCategoriesToProducts();
		var btn_product_file_image = document.getElementById('btn_product_file_image');
		var product_file_image = document.getElementById('product_file_image');
		btn_product_file_image.addEventListener('click', function(){
			product_file_image.click();
		}, false);

		product_file_image.addEventListener('change', function(){
			document.getElementById('form_product_gallery').submit();
		});
	}

    if(category){
        category.addEventListener('change', setSubCategoriesToProducts);
    }

});

function setSubCategoriesToProducts(){    
	var parent_id = category.value;
	var subcategory_actual = document.getElementById('subcategory_actual').value;
	select = document.getElementById('subcategory');
	select.innerHTML = "";
	var url = base + '/api-js/load/subcategories/'+parent_id;
	http.open('GET', url, true);
	http.setRequestHeader('X-CSRF-TOKEN', csrfToken);
	http.send();
	http.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			var data = this.responseText;
			data = JSON.parse(data);
			data.forEach( function(element, index) {
				if(subcategory_actual == element.id){
					select.innerHTML += "<option value=\""+element.id+"\" selected>"+element.name+"</option>";
				}else{
					select.innerHTML += "<option value=\""+element.id+"\">"+element.name+"</option>";
				}
			});
		}
	}
}