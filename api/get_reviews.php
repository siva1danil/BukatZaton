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
      $page = isset($_GET["page"]) ? intval(preg_replace('/\D/', "", $_GET["page"])) : 1;
      $page = $page > 1 ? $page : 1;
      $reviews = get_reviews(TRUE, 100, $page);
      printf(json_encode([ "error" => $reviews[1], "data" => $reviews[0] ]));
    }
  }
?>