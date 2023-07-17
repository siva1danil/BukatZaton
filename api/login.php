<?php
  session_start();
  include (__DIR__ . "/../lib/database.php");

  $json = file_get_contents('php://input');
  $data = json_decode($json, true);

  if(is_null($data)) {
    printf(json_encode([ "error" => "Неверный формат запроса" ]));
  } else {
    $status = login($data["username"], $data["password"]);
    $_SESSION["logged_in"] = is_null($status);
    printf(json_encode([ "error" => $status ]));
  }