<?php
  // Load credentials
  if (!file_exists("config.php"))
    die("Error: Create a file <config.php> defining the following variables: <\$HOSTNAME / \$USERNAME / \$PASSWORD>");
  include "config.php";
  if (!isset($HOSTNAME) || !isset($USERNAME) || !isset($PASSWORD))
    die("Error: define <\$HOSTNAME / \$USERNAME / \$PASSWORD> in your <config.php>");
  
  // Connect to MySQL, handle any errors
  $conn = new mysqli($HOSTNAME, $USERNAME, $PASSWORD);
  
  if ($conn->connect_error) {
    die("Error: Connection failed: " . $conn->connect_error);
  }

  if (!$conn->set_charset("utf8mb4")) {
    die("Error: Cannot change charset: " . $conn->error);
  }

  // Create the database, handle any errors
  $sql = "CREATE DATABASE IF NOT EXISTS zaton CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
  if ($conn->query($sql) != TRUE)
    die("Error: Cannot create database: " . $conn->error);
  
  // Switch to our database, handle any errors
  if ($conn->select_db("zaton") != TRUE)
    die("Error: Cannot switch database: " . $conn->error);

  // Create the reviews table, handle any errors
  $sql = "CREATE TABLE IF NOT EXISTS reviews(
    id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    public TINYINT NOT NULL DEFAULT 0,
    date DATETIME NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    username VARCHAR(64) NOT NULL,
    email VARCHAR(64) NOT NULL DEFAULT \"\",
    phone BIGINT NOT NULL DEFAULT 0,
    review TEXT NOT NULL,
    rating TINYINT UNSIGNED NOT NULL) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
  if ($conn->query($sql) != TRUE)
    die("Error: Cannot create table: " . $conn->error);
  
  // Define review management functions
  function get_reviews($show_non_public = false) {
    global $conn;

    $sql = "SELECT * FROM reviews";
    if (!$show_non_public)
      $sql .= " WHERE public = 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0)
      return $result->fetch_all(MYSQLI_ASSOC);
    else
      return array();
  }


  function add_review($username, $email, $phone, $review, $rating) {
    global $conn;

    $sql = "INSERT INTO reviews (username, email, phone, review, rating) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
      die("Error: Cannot prepare statement: " . $conn->error);
    $stmt->bind_param("ssiis", $username, $email, $phone, $review, $rating);
    if (!$stmt->execute())
      die("Error: Cannot execute statement: " . $stmt->error);
    $stmt->close();
  }

  function edit_review($id, $username, $email, $phone, $review, $rating) {
    global $conn;

    $sql = "UPDATE reviews SET username = ?, email = ?, phone = ?, review = ?, rating = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
      die("Error: Cannot prepare statement: " . $conn->error);
    $stmt->bind_param("ssiisi", $username, $email, $phone, $review, $rating, $id);
    if (!$stmt->execute())
      die("Error: Cannot execute statement: " . $stmt->error);
    $stmt->close();
  }

  function set_review_publicity($id, $value) {
    global $conn;

    $sql = "UPDATE reviews SET public = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
      die("Error: Cannot prepare statement: " . $conn->error);
    $stmt->bind_param("ii", $value, $id);
    if (!$stmt->execute())
      die("Error: Cannot execute statement: " . $stmt->error);
    $stmt->close();
  }

?>