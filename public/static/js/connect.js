document.addEventListener('DOMContentLoaded', function() {
    const msg = sessionStorage.getItem('register_success');

    if (msg) { mdalert({ type: 'success', title: lang['name'] , msg: msg }); sessionStorage.removeItem('register_success'); }

    // Formulario Login
    form_connect_login = document.getElementById('form_connect_login');
    form_connect_two_auth = document.getElementById('form_connect_two_auth');
    form_connect_register = document.getElementById('form_connect_register');
    form_connect_forgot_password = document.getElementById('form_connect_forgot_password');

    if(form_connect_login) {
        form_connect_login.addEventListener('submit', function(event) {
            event.preventDefault();
            connect_user_login();
        });
    }

    if(form_connect_register) {
        form_connect_register.addEventListener('submit', function(event) {
            event.preventDefault();
            connect_user_register();
        });
    }

    if(form_connect_two_auth){
        form_connect_two_auth.addEventListener('submit', function(e){
            e.preventDefault();
            connect_two_auth();
        });
    }

    if(form_connect_forgot_password){
        form_connect_forgot_password.addEventListener('submit', function(e){
            e.preventDefault();
            connect_forgot_password();
        });
    }
});


function connect_user_login(){
    loader_action_status('show');

    url = base+'/api-js/connect/login';
    
    var http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.setRequestHeader('X-CSRF-TOKEN', csrftoken);

    http.onreadystatechange = function(){
        if(this.readyState == "4" && this.status == "200"){
            data = this.responseText;
            data = JSON.parse(data);

            if(data.type == "error"){
                mdalert(data);
            }

            if(data.type == "success"){
                window.location.href = base + '/dashboard';
            }
        }

        if(this.status != "200"){
            mdalert({
                title: lang['name'],
                type: 'error',
                msg: lang['error']
           });
        }
        loader_action_status('hide');
    }

    http.send(new FormData(document.getElementById('form_connect_login')));
}

function connect_two_auth(){
    loader_action_status('show');

    url = base + '/api-js/connect/twofactor';
  
    var http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.setRequestHeader('X-CSRF-TOKEN', csrftoken);
    
    http.onreadystatechange = function(){
        if(this.readyState == "4" && this.status == "200"){
            data = this.responseText;
            data = JSON.parse(data);

            if(data.type == "error"){
                mdalert(data);
            }

            if(data.type == "success"){
                window.location.href = base + '/dashboard/home';
            }
        }

        if(this.status != "200"){
            mdalert({title: lang['name'], type: 'error', msg: lang['error'] });
        }
        loader_action_status('hide');
    }
    http.send(new FormData(document.getElementById('form_connect_two_auth')));
}



function connect_user_register(){
    loader_action_status('show');

    url = base+'/api-js/connect/register';
    
    var http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.setRequestHeader('X-CSRF-TOKEN', csrftoken);

    http.onreadystatechange = function(){
        if(this.readyState == "4" && this.status == "200"){
            data = this.responseText;
            data = JSON.parse(data);

            if(data.type == "error"){
                mdalert(data);
            }

            if(data.type == "success"){
                sessionStorage.setItem('register_success', 'Registro exitoso, ahora puedes iniciar sesión');
                window.location.href = base + '/connect/login';
            }
        }

        if(this.status != "200"){
            mdalert({
                title: lang['name'],
                type: 'error',
                msg: lang['error']
           });
        }
        loader_action_status('hide');
    }

    http.send(new FormData(document.getElementById('form_connect_register')));
}