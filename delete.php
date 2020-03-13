<?php
require 'common.php';
require 'db.php';

$boards_id = filter_input(INPUT_GET, 'boards_id');
$threads_id = filter_input(INPUT_GET, 'threads_id');
$board_id = filter_input(INPUT_GET, 'board_id');
$title = filter_input(INPUT_GET, 'title');
$comment = filter_input(INPUT_GET, 'comment');

if(filter_input(INPUT_SERVER,'REQUEST_METHOD') === 'POST'){
	$delete_pass = filter_input(INPUT_POST, 'delete_pass');
	$token = filter_input(INPUT_POST, 'token');

	$vdt = new Validation;
  $vdt->check_type($delete_pass,'パスワード');
	$vdt->check_type($token,'トークン');
  $error = $vdt->get_error();

	$db = new DB;
	$sql = 'SELECT pass FROM users_registrations';
	$rows = $db->select($sql);
	foreach ($rows as $row) {
		$user_pass = $row['pass'];
	}

	if(empty($error)){
		if(isset($boards_id) && password_verify($delete_pass,$user_pass)){
			$sql = 'DELETE FROM boards WHERE boards_id=?';
			$params[] = $boards_id;
			$success_delete_boards = $db->delete($sql,$params);
		}elseif(isset($threads_id) && password_verify($delete_pass,$user_pass)){
			$sql = 'DELETE FROM threads WHERE threads_id=?';
			$params[] = $threads_id;
			$success_delete_threads = $db->delete($sql,$params);
		}else{
			$error[] = 'パスワードが一致しません。';
		}
	}
}
?>

<!DOCTYPE html>
<html>
  <head>
		<link rel="stylesheet" href="/css/styles.css">
    <meta charset="utf-8">
    <title>削除ページ</title>
  </head>
  <body>
		<?php if(isset($success_delete_boards) && $success_delete_boards): ?>
			<p>パスワードが一致し、投稿を削除しました。</p>
			<p><a href="boards.php">掲示板に戻る。</a></p>
		<?php elseif(isset($success_delete_threads) && $success_delete_threads): ?>
			<p>パスワードが一致し、投稿を削除しました。</p>
			<p><a href="threads.php?boards_id=<?php echo h($board_id); ?>">コメント欄に戻る</a></p>
    <?php else: ?>
			<?php if(isset($board_id)): ?>
				<p>投稿内容を削除します。宜しければパスワードを入力してください。</p>
				<p>消さない場合は<a href="threads.php?boards_id=<?php echo h($board_id); ?>">こちら</a>から。</p>
				<p class="strong">『<?php echo h($comment); ?>』</p>
			<?php else: ?>
				<p>投稿内容を削除します。宜しければパスワードを入力してください。</p>
				<p>消さない場合は<a href="boards.php">こちら</a>から。</p>
				<p class="strong">『<?php echo h($title); ?>』</p>
			<?php endif; ?>
			<?php if(isset($error) > 0): ?>
        <?php foreach($error as $e): ?>
          <p class="error"><?php echo h($e); ?></p>
        <?php endforeach; ?>
      <?php endif; ?>
	    <form action="" method="post" autocomplete="off">
				<div class="cp_iptxt">
					<label class="ef">
					<input type="password" name="delete_pass" placeholder="パスワードを入力してください。">
					</label>
				</div>
				<input type="hidden" name="token" value="<?php echo h(generate_token()); ?>">
				<button type="submit">削除</button>
	    </form>
		<?php endif; ?>
  </body>
</html>
