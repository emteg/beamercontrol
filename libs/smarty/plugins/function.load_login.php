<?php

function fetch_login() {
   global $session;
   return $session;
}

function smarty_function_load_login($params, $smarty) {
   // call the function
   $login_info = fetch_login();

   // assign template variable
   $smarty->assign($params['assign'], $login_info);
}
?>