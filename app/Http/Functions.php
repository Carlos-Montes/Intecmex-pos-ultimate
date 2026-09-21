<?php
function kvfj($json, $key){
    if($json == null):
        return null;
    else:
        $json = $json;
        $json = json_decode($json, true);


        if(array_key_exists($key, $json)):
            return $json[$key];
        else:
            return null;
        endif;
    endif;
}

function getCategories($type = null){
    $categories = [
        '0' => 'Añadir Categoría'
    ];

    if(!is_null($type)):
        return $categories[$type];
    else:
        return $categories; 
    endif;
}

function getActive($status = null){ 
    $statu = [
        '0' => 'Inactivo',
        '1' => 'Activo'
    ];
    if(!is_null($status)):
        return $statu[$status];
    else:
        return $statu;
    endif;
}

function getUserRole($type = null){
    $users = [
        '0' => 'Administrador',
        '1' => 'Vendedores',
        '2' => 'Clientes',
        '3' => 'Proveedores'
    ];

    if(!is_null($type)):
        return $users[$type];
    else:
        return $users;
    endif;
}

function getPermissions($json, $key){
    if($json == null):
        return null;
    else:
        $json = $json;
        $json = json_decode($json, true);

        if(array_key_exists('all', $json)):
            if($json['all']):
                return true;
            endif;
        endif;

        if(array_key_exists($key, $json)):
            return $json[$key];
        else:
            return null;
        endif;
    endif;
}

function gender($id = null){
    $a = [
        '0' => 'No especificado',
        '1' => 'Masculino',
        '2' => 'Femenino'
    ];

    if(!is_null($id)):
        return $a[$id];
    else:
        return $a;
    endif;
}

function getFinances($type = null){
    $f = [
        '0' => 'Gastos',
        '1' => 'Cuentas Contables',
    ];
    if(!is_null($type)):
        return $f[$type];
    else:
        return $f;
    endif;
}

function getFileUrl($data, $prefix = null){
    $data = json_decode($data, true);

    if($prefix):
        $url = config('intecmex.cdn').'/uploads/'.$data['path'].'/'.$prefix.'_'.$data['final_name'];
    else:
        $url = config('intecmex.cdn').'/uploads/'.$data['path'].'/'.$data['final_name'];
    endif;
    
    return $url;
}


function generate_avatar($username){
    $initials = "";
    $words = explode(' ', $username);

    if(!empty($words[0])){
        $initials .= strtoupper(substr($words[0], 0, 1));
    }

    if(count($words) > 1 && !empty($words[0])){
        $initials .= strtoupper(substr($words[1], 0, 1));
    }

    return $initials;
}


function getUsersStatus($id = null){
    $status = [
        '0' => 'Activo',
        '1' => 'Inactivo',
        '100' => 'Suspendido',
    ];

    if(!is_null($id)):
        return $status[$id];
    else:
        return $status;
    endif;
}

function getProducts($id = null){
    $product = [
        '1' => 'Unidades',
        '2' => 'Marcas',
        '3' => 'Imprimir etiquetas'
    ];

    if(!is_null($id)):
        return $product[$id];
    else:
        return $product;
    endif;
}
