<?php
require 'common.php';
require 'db.php';
require_logined_session();

if(filter_input(INPUT_SERVER,'REQUEST_METHOD') === 'POST'){
  $name = filter_input(INPUT_POST, 'name');
  $mail = filter_input(INPUT_POST, 'mail');
  $pass = filter_input(INPUT_POST, 'pass');
  $token = filter_input(INPUT_POST, 'token');

  $vdt = new Validation;
  $vdt->check_max($name,'名前',10);
  $vdt->check_type($mail,'メールアドレス');
  $vdt->check_type($pass,'パスワード');
  $vdt->check_type($token,'トークン');
  $error = $vdt->get_error();

  if(empty($error)){

    $db = new DB;
    $sql = 'SELECT * FROM users_registrations WHERE name=? AND mail=?';
    $params[] = $name;
    $params[] = $mail;
    $rows = $db->select($sql,$params);
    foreach ($rows as $row) {
      $hash = $row['pass'];
      $name = $row['name'];
    }
    if(password_verify($pass,$hash)){
      unset($_SESSION['name']);
      session_regenerate_id(true);
      $_SESSION['name'] = $name;
      header('Location:boards.php');
      exit;
    }else{
      $error[] = 'パスワードが一致しません。';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="utf-8">
    <title>ログイン</title>
  </head>
  <body>
    <h1>掲示板にログイン!</h1>
    <p>掲示板のアカウントを持っていない場合は<a href="index.php">新規登録</a>から。</p>
    <?php if(isset($error)): ?>
      <?php foreach($error as $e): ?>
        <p class="error"><?php echo h($e); ?></p>
      <?php endforeach; ?>
    <?php endif; ?>
    <form action="" method="post" autocomplete="off">
      <div class="cp_iptxt">
      	<label class="ef">
      	<input type="text" name="name" placeholder="名前">
      	</label>
      </div>
      <div class="cp_iptxt">
      	<label class="ef">
      	<input type="email" name="mail" placeholder="メールアドレス">
      	</label>
      </div>
      <div class="cp_iptxt">
      	<label class="ef">
      	<input type="password" name="pass" placeholder="パスワード">
      	</label>
      </div>
      <input type="hidden" name="token" value="<?php echo h(generate_token()); ?>">
      <button type="submit">ログイン</button>
    </form>
  </body>
</html>
