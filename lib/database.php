<?php
  error_reporting(0);
  ini_set('display_errors', 'Off');
  
  $conn;

  function login($username, $password) {
    if (!file_exists(__DIR__ . "/config.php")) {
      return "Ошибка: файл <lib/config.php> не найден, создайте его по шаблону:\n" . "<?php\n  \$ADMIN_USERNAME = \"admin\";\n  \$ADMIN_PASSWORD = \"admin\";\n  $DATABASE = \"zaton\";\n  \$HOSTNAME = \"127.0.0.1:3306\";\n  \$USERNAME = \"root\";\n  \$PASSWORD = \"\";\n?>";
    }
    include (__DIR__ . "/config.php");
    if (!isset($ADMIN_USERNAME) || !isset($ADMIN_PASSWORD))
      return "Ошибка: в файле <lib/config.php> не указаны <\$ADMIN_USERNAME / \$ADMIN_PASSWORD >";
    
    if($username != $ADMIN_USERNAME || $password != $ADMIN_PASSWORD)
      return "Неверный пароль";
    else
      return NULL;
  }

  function init() {
    global $conn;

    // Load credentials
    if (!file_exists(__DIR__ . "/config.php")) {
      return "Ошибка: файл <lib/config.php> не найден, создайте его по шаблону:\n" . "<?php\n  \$ADMIN_USERNAME = \"admin\";\n  \$ADMIN_PASSWORD = \"admin\";\n  $DATABASE = \"zaton\";\n  \$HOSTNAME = \"127.0.0.1:3306\";\n  \$USERNAME = \"root\";\n  \$PASSWORD = \"\";\n?>";
    }
    include (__DIR__ . "/config.php");
    if (!isset($DATABASE) || !isset($HOSTNAME) || !isset($USERNAME) || !isset($PASSWORD))
      return "Ошибка: в файле <lib/config.php> не указаны <\$DATABASE / \$HOSTNAME / \$USERNAME / \$PASSWORD>";

    // Connect to MySQL, handle any errors
    $conn = new mysqli($HOSTNAME, $USERNAME, $PASSWORD);

    if ($conn->connect_error)
      return "Ошибка подключения к базе данных: " . $conn->connect_error;

    if (!$conn->set_charset("utf8mb4"))
      return "Ошибка изменения кодировки: " . $conn->error;

    // Create the database, handle any errors
    $sql = "CREATE DATABASE IF NOT EXISTS $DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if (!$conn->query($sql))
      return "Ошибка создания базы данных: " . $conn->error;

    // Switch to our database, handle any errors
    if (!$conn->select_db($DATABASE))
      return "Ошибка переключения базы данных: " . $conn->error;

    // Create the reviews table, handle any errors
    $sql = "CREATE TABLE IF NOT EXISTS reviews(
      id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
      public TINYINT NOT NULL DEFAULT 0,
      date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      username VARCHAR(64) NOT NULL,
      email VARCHAR(64) NOT NULL DEFAULT \"\",
      phone BIGINT NOT NULL DEFAULT 0,
      review TEXT NOT NULL,
      rating TINYINT UNSIGNED NOT NULL) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if (!$conn->query($sql))
      return "Ошибка создания таблицы: " . $conn->error;
    
    return NULL;
  }

  function get_reviews($show_non_public = false, $results_per_page = 10, $page = 1) {
    global $conn;

    $start_limit = ($page - 1) * $results_per_page;

    if ($show_non_public) {
        $sql = "SELECT * FROM reviews LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt)
          return [ [], "Ошибка подготовки запроса: " . $conn->error ];
        $stmt->bind_param("ii", $start_limit, $results_per_page);
    } else {
        $sql = "SELECT * FROM reviews WHERE public = 1 LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt)
          return [ [], "Ошибка подготовки запроса: " . $conn->error ];
        $stmt->bind_param("ii", $start_limit, $results_per_page);
    }

    if (!$stmt->execute())
      return [ [], "Ошибка выполнения запроса: " . $stmt->error ];
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return [ $result->fetch_all(MYSQLI_ASSOC), null ];
    } else {
        return [ [], null ];
    }
  }

  function add_review($username, $email, $phone, $review, $rating) {
    global $conn;

    $sql = "INSERT INTO reviews (username, email, phone, review, rating) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
      return "Ошибка подготовки запроса: " . $conn->error;
    $stmt->bind_param("ssisi", $username, $email, $phone, $review, $rating);
    if (!$stmt->execute())
      return "Ошибка выполнения запроса: " . $stmt->error;
    $stmt->close();
    return null;
  }

  function edit_review($id, $username, $email, $phone, $review, $rating) {
    global $conn;

    $sql = "UPDATE reviews SET username = ?, email = ?, phone = ?, review = ?, rating = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
      return "Ошибка подготовки запроса: " . $conn->error;
    $stmt->bind_param("ssisii", $username, $email, $phone, $review, $rating, $id);
    if (!$stmt->execute())
      return "Ошибка выполнения запроса: " . $stmt->error;
    $stmt->close();
    return null;
  }

  function set_review_publicity($id, $value) {
    global $conn;

    $sql = "UPDATE reviews SET public = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
      return "Ошибка подготовки запроса: " . $conn->error;
    $stmt->bind_param("ii", $value, $id);
    if (!$stmt->execute())
      return "Ошибка выполнения запроса: " . $stmt->error;
    $stmt->close();
    return null;
  }

  function delete_review($id) {
    global $conn;

    $sql = "DELETE FROM reviews WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
      return "Ошибка подготовки запроса: " . $conn->error;
    $stmt->bind_param("i", $id);
    if (!$stmt->execute())
      return "Ошибка выполнения запроса: " . $stmt->error;
    $stmt->close();
    return null;
  }

?>