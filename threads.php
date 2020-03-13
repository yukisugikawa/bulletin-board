<?php
require 'common.php';
require 'db.php';
require_unlogined_session();

$name = $_SESSION['name'];
$board_id = filter_input(INPUT_GET, 'boards_id');

if(filter_input(INPUT_SERVER,'REQUEST_METHOD') === 'POST'){
  $comment = filter_input(INPUT_POST, 'comment');
  $token = filter_input(INPUT_POST, 'token');
  date_default_timezone_set('Asia/Tokyo');
  $commented_at = date("Y-m-d H:i:s");

  $vdt = new Validation;
  $vdt->check_max($comment,'コメント',50);
  $vdt->check_type($token,'トークン');
  $error = $vdt->get_error();

  if(empty($error)){
    $db = new DB;
    $sql = 'INSERT INTO threads (threads_id,board_id,comment,commented_at,name) VALUES (null,?,?,?,?)';
    $params[] = $board_id;
    $params[] = $comment;
    $params[] = $commented_at;
    $params[] = $name;
    $rows = $db->insert($sql,$params);

    header("Location:threads.php?boards_id=$board_id");
    exit;
  }
}

$db = new DB;
$sql = 'SELECT * FROM boards
   INNER JOIN threads ON boards.boards_id = threads.board_id
   WHERE board_id=?
   ORDER BY threads.threads_id DESC';
$params = [];
$params[] = $board_id;
$rows = $db->select($sql,$params);
$count = count($rows);
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <link rel="stylesheet" href="/css/styles.css">
    <meta charset="utf-8">
    <title>コメント欄</title>
  </head>
  <body>
    <h1>掲示板へようこそ</h1>
    <h2>新規投稿</h2>
    <?php if(isset($error)): ?>
      <?php foreach($error as $e): ?>
        <p class="error"><?php echo h($e); ?></p>
      <?php endforeach; ?>
    <?php endif; ?>
    <form action="" method="post" autocomplete="off">
      <div class="cp_iptxt">
        <label class="ef">
          <textarea name="comment" rows="5" cols="80" placeholder="コメントを入力してください"></textarea><br>
        </label>
      </div>
      <input type="hidden" name="token" value="<?php echo h(generate_token()); ?>">
      <button type="submit">送信</button>
    </form>
    <h2>投稿一覧</h2>
    <p>現在の投稿は<?php echo h($count); ?>件</p>
    <?php if(isset($rows)): ?>
    <?php foreach($rows as $row): ?>
      <div class="flexbox">
        <div class="posts">
          <p>
            <?php echo h($row['name']); ?>
            <?php echo h($row['commented_at']); ?><br>
            <?php echo h($row['comment']); ?>
          </p>
        </div>
        <div class="button">
          <form action="change.php" method="get">
            <button type="submit" name="threads_id" value="<?php echo h($row['threads_id']); ?>">編集</button>
            <input type="hidden" name="board_id" value="<?php echo h($board_id); ?>">
            <input type="hidden" name="comment" value="<?php echo h($row['comment']); ?>">
          </form>
          <form action="delete.php" method="get">
            <button type="submit" name="threads_id" value="<?php echo h($row['threads_id']); ?>">削除</button>
            <input type="hidden" name="board_id" value="<?php echo h($board_id); ?>">
            <input type="hidden" name="comment" value="<?php echo h($row['comment']); ?>">
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    <?php else: ?>
      <p>投稿はまだありません。</p>
    <?php endif; ?>
  </body>
</html>
