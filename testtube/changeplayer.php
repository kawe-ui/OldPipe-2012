<?php

function changeToggle(int $type = 0){
    $cookie_name = "playertoggled";
    $cookie_value = $type;
    $expiration = time() + (86400 * 30); 

    setcookie($cookie_name, $cookie_value, $expiration, "/");
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
};

if(isset($_COOKIE['playertoggled'])){
    if($_COOKIE['playertoggled'] == 0){
      changeToggle(1);
    }else{
      changeToggle(0);
    }
}else{
    changeToggle(1);
};

