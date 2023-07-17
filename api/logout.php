<?php
  session_start();

  $json = file_get_contents('php://input');
  $data = json_decode($json, true);

  $_SESSION["logged_in"] = FALSE;
  printf(json_encode([ "error" => null ]));