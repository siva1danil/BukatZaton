<?php
  // error_reporting(0);
  // ini_set('display_errors', 'Off');
  
  $conn;

  function login($username, $password) {
    if (!file_exists(__DIR__ . "/config.php")) {
      return "Error: <lib/config.php> not found, please create a new one using the following template:\n" . "<?php\n  \$ADMIN_USERNAME = \"admin\";\n  \$ADMIN_PASSWORD = \"admin\";\n  \$HOSTNAME = \"127.0.0.1:3306\";\n  \$USERNAME = \"root\";\n  \$PASSWORD = \"\";\n?>";
    }
    include (__DIR__ . "/config.php");
    if (!isset($ADMIN_USERNAME) || !isset($ADMIN_PASSWORD))
      return "Error: define <\$ADMIN_USERNAME / \$ADMIN_PASSWORD > in your <lib/config.php>";
    
    if($username != $ADMIN_USERNAME || $password != $ADMIN_PASSWORD)
      return "Wrong password";
    else
      return NULL;
  }

  function init() {
    global $conn;

    // Load credentials
    if (!file_exists(__DIR__ . "/config.php")) {
      return "Error: <lib/config.php> not found, please create a new one using the following template:\n" . "<?php\n  \$ADMIN_USERNAME = \"admin\";\n  \$ADMIN_PASSWORD = \"admin\";\n  \$HOSTNAME = \"127.0.0.1:3306\";\n  \$USERNAME = \"root\";\n  \$PASSWORD = \"\";\n?>";
    }
    include (__DIR__ . "/config.php");
    if (!isset($HOSTNAME) || !isset($USERNAME) || !isset($PASSWORD))
      return "Error: define <\$HOSTNAME / \$USERNAME / \$PASSWORD> in your <lib/config.php>";

    // Connect to MySQL, handle any errors
    $conn = new mysqli($HOSTNAME, $USERNAME, $PASSWORD);

    if ($conn->connect_error)
      return "Error: Connection failed: " . $conn->connect_error;

    if (!$conn->set_charset("utf8mb4"))
      return "Error: Cannot change charset: " . $conn->error;

    // Create the database, handle any errors
    $sql = "CREATE DATABASE IF NOT EXISTS zaton CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if (!$conn->query($sql))
      return "Error: Cannot create database: " . $conn->error;

    // Switch to our database, handle any errors
    if (!$conn->select_db("zaton"))
      return "Error: Cannot switch database: " . $conn->error;

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
    if (!$conn->query($sql))
      return "Error: Cannot create table: " . $conn->error;
    
    return NULL;
  }

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