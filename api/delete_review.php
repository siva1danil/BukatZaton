<?php
  session_start();
  include (__DIR__ . "/../lib/database.php");

  if($_SESSION["logged_in"] != TRUE)
    printf(json_encode([ "error" => "Вы не авторизованы" ]));
  else {
    if (!is_null(init())) {
      printf(json_encode([ "error" => "Ошибка базы данных, обратитесь к администратору" ]));
    } else {
      $json = file_get_contents('php://input');
      $data = json_decode($json, true);
      
      if(is_null($data)) {
        printf(json_encode([ "error" => "Неверный формат запроса" ]));
      } else if (strlen(preg_replace("\D", "", $data["id"])) > 0) {
        delete_review(intval(preg_replace("\D", "", $data["id"])));
        printf(json_encode([ "error" => NULL ]));
      } else {
        printf(json_encode([ "error" => "Неверно указан ID" ]));
      }
    }
  }
?>