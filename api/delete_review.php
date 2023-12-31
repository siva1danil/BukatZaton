<?php
  session_start();
  include (__DIR__ . "/../lib/database.php");

  if($_SESSION["logged_in"] != TRUE)
    printf(json_encode([ "error" => "Вы не авторизованы" ]));
  else {
    $init = init();
    if (!is_null($init)) {
      printf(json_encode([ "error" => $init ]));
    } else {
      $json = file_get_contents('php://input');
      $data = json_decode($json, true);
      
      if(is_null($data)) {
        printf(json_encode([ "error" => "Неверный формат запроса" ]));
      } else if (strlen(preg_replace("/\D/", "", $data["id"])) > 0) {
        $result = delete_review(intval(preg_replace("/\D/", "", $data["id"])));
        printf(json_encode([ "error" => $result ]));
      } else {
        printf(json_encode([ "error" => "Неверно указан ID" ]));
      }
    }
  }
?>