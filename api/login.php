<?php
  session_start();
  include (__DIR__ . "/../lib/database.php");

  $json = file_get_contents('php://input');
  $data = json_decode($json, true);

  if(is_null($data)) {
    printf('{ "error": "%s" }', "Неверный формат запроса");
  } else {
    $status = login($data["username"], $data["password"]);
    if(is_null($status)) {
      printf('{ "error": %s }', "null");
      $_SESSION["logged_in"] = TRUE;
    } else {
      printf('{ "error": "%s" }', $status);
    }
  }