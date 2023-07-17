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
      } else {
        $validId = strlen(preg_replace("/\D/", "", $data["id"])) > 0;
        $validPublicity = $data["public"] == TRUE || $data["public"] == FALSE;

        if ($validId && $validPublicity) {
          $result = set_review_publicity(preg_replace("/\D/", "", $data["id"]), $data["public"]);
          printf(json_encode([ "error" => $result ]));
        } else {
          printf(json_encode([ "error" => "Параметры указаны неверно" ]));
        }
      }
    }
  }
?>