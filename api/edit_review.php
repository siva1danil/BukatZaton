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
        } else {
            $validId = strlen(preg_replace("\D", "", $data["id"])) > 0;
            $validUsername = strlen($data["username"]) <= 64 && preg_match('/^(\s+)?$/', $data["username"]) == FALSE;
            $validEmail = strlen($data["email"]) <= 64 && (preg_match('/^(\s+)?$/', $data["email"]) != FALSE || preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $data["email"]) != FALSE);
            $validPhone = preg_match('/^(\s+)?$/', $data["phone"]) != FALSE || strlen(preg_replace('/\D/', "", $data["phone"])) == 11;
            $validRating = $data["rating"] >= 1 && $data["rating"] <= 5;
            $validReview = preg_match('/^(\s+)?$/', $data["review"]) == FALSE;

            if ($validId && $validUsername && $validEmail && $validPhone && $validRating && $validReview) {
                edit_review(preg_replace("\D", "", $data["id"]),
                $data["username"],
                preg_match('/^(\s+)?$/', $data["email"]) != FALSE ? "" : $data["email"],
                preg_match('/^(\s+)?$/', $data["phone"]) != FALSE ? "0" : preg_replace('/\D/', "", $data["phone"]),
                $data["review"],
                $data["rating"]);
                printf(json_encode([ "error" => NULL ]));
            } else {
                printf(json_encode([ "error" => "Некоторые поля заполнены неверно" ]));
            }
        }
    }
  }
?>