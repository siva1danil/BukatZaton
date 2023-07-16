<?php session_start(); ?>

<html>
<head>
  <title>Администратор - Букатовский затон</title>
  <meta charset="utf8">
  <link type="image/x-icon" rel="shortcut icon" href="favicon.ico" />
  <link rel="stylesheet" href="/res/style-admin.css" />
  <script src="/res/script-admin.js"></script>
</head>
<body>
<?php if($_SESSION["logged_in"]) { ?>

  <div class="wrap">
    <div class="content">
      content
    </div>
  </div>

<?php } else { ?>

  <div id="login" class="login-wrap">
    <div class="login-window">
      <p class="login-window-title">Вход</p>
      <input id="field-login" class="login-window-input" type="text" placeholder="Логин">
      <input id="field-password" class="login-window-input" type="password" placeholder="Пароль">
      <div class="login-window-buttonbar">
        <button id="button-login" class="login-window-button">Войти</button>
        <span id="status" class="login-window-status"></span>
      </div>
    </div>
  </div>

<?php } ?>
</body>
</html>