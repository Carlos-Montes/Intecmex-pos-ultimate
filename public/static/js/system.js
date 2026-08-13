document.addEventListener('DOMContentLoaded', function(){
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    var sidebar_active = localStorage.getItem('sidebar_active');

    if(sub_route){
        sub_route_active = document.getElementById('side_lk_'+sub_route);
        if(sub_route_active){
            sub_route_active.getElementsByTagName('i')[0].setAttribute('class', 'bi bi-record-fill');
            sub_route_active.getElementsByTagName('i')[0].classList.add('pulse-element');
            sub_route_active.getElementsByTagName('i')[0].style.display = "inline-block";
        }
    }

    // SIDEBAR
    var sidebar = document.getElementById('sidebar');
    var sidebar_accordion = document.getElementsByClassName('sidebar_accordion');
    var accordion_ul = document.getElementsByClassName('accordin_ul');
    var accordion_arrows = document.getElementsByClassName('row-icon');

    if(sidebar_accordion){
        Array.from(sidebar_accordion).forEach(function(element){
            element.addEventListener('click', function(e){
                e.preventDefault();

                var to_show = document.getElementById(element.getAttribute('data-target'));
                var isSubMenuVisible = to_show.style.display === 'block';

                if(accordion_arrows){
                    Array.from(accordion_arrows).forEach(function(sp){
                        sp.style.transform = 'rotate(0deg)';
                    })
                }


                arrow = document.getElementById('row_icon_'+element.getAttribute('data-target'));
                var currentTransform = window.getComputedStyle(arrow, null).getPropertyValue('transform');

                if(currentTransform === 'matrix(0, 1, -1, 0, 0, 0)'){
                    arrow.style.transform = 'rotate(0deg)';
                }else{
                    arrow.style.transform = 'rotate(90deg)';
                }
                

                if(accordion_ul){
                    Array.from(accordion_ul).forEach(function(ul){
                        ul.style.height = '0px';
                        ul.style.display = 'none';
                    });
                }

                if(!isSubMenuVisible){
                    to_show.style.display = 'block';
                    finalHeight = to_show.scrollHeight;
                    to_show.style.height = '0px';
                    to_show.offsetHeight;
                    to_show.style.height = finalHeight+'px';
                }

                if(sidebar_active == element.getAttribute('data-target')){
                    localStorage.setItem('sidebar_active', '');
                    sidebar_active = "";
                }else{
                    localStorage.setItem('sidebar_active', element.getAttribute('data-target'));
                    sidebar_active = element.getAttribute('data-target');
                }

            });
        });
    }

    if(sidebar_active != ""){
        accordion_ul_active = document.getElementById(sidebar_active);
        if(accordion_ul_active){
            accordion_ul_active.style.display = 'block';
            active_arrow = document.querySelector('[data-target="'+sidebar_active+'"]').getElementsByClassName('row-icon')[0];
            active_arrow.style.transition = 'all 0.1s ease';
            active_arrow.style.transition = 'rotate(90deg)';
        }
    }
    // END SIDEBAR

    var trigger_sidebar = document.getElementById('trigger_sidebar');

    if(trigger_sidebar){
        trigger_sidebar.addEventListener('click', function(e){
            e.preventDefault();
            sidebar.style.left = '0px';
        })
    }

    var trigger_sidebar_close = document.getElementById('trigger_sidebar_close');

    if(trigger_sidebar_close){
        trigger_sidebar_close.addEventListener('click', function(e){
            e.preventDefault();
            sidebar.style.left = '-270px';
        })
    }
    
    var file_uploader_trigger = document.getElementsByClassName('file_uploader_trigger');

    if(file_uploader_trigger){
       Array.from(file_uploader_trigger).forEach(function (element){
            element.addEventListener('click', function(e){
                e.preventDefault();
                document.getElementById(this.getAttribute('data-target')).click();
            })
       }) 
    }

    var image_prew = document.getElementsByClassName('image_prew');
    if(image_prew){
        Array.from(image_prew).forEach(function (field){
            field.addEventListener('change', function(e){
               imageprew(this, this.getAttribute('data-to-prew'));
            })
       }) 
    }


    btn_deleted = document.getElementsByClassName("btn-deleted");
    for (i = 0; i < btn_deleted.length; i++) {
        btn_deleted[i].addEventListener("click", delete_object);
    }


});

function imageprew(input, toprew){
    if(input.files && input.files[0]){
        var render = new FileReader();
        render.onload = function(e){
            document.getElementById(toprew).setAttribute('src', e.target.result);
        }
    }
    render.readAsDataURL(input.files[0]);
}


function delete_object(e) {
    e.preventDefault();
    var object = this.getAttribute("data-object");
    var action = this.getAttribute("data-action");
    var path = this.getAttribute("data-path");
    var url = base + "/" + path + "/" + object + "/" + action;

    if (action == "delete") {
        mdalert({
            title: "¿Estas seguro de eliminar este objecto?",
            type: "delete",
            msg: "Recuerda que esta acción enviara este elemento a la papelera o lo eliminara de forma definitiva.",
            actions: JSON.stringify([
                { url: url, name: "Si, Eliminar", type: "danger" },
            ]),
        });
    }
}
