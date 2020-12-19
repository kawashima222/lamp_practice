<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'history.php';

session_start();

//セッションIDの確認
//falseならログイン画面
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//DB接続
$db = get_db_connect();
//user_id, name,password,typeの情報を取得
$user = get_login_user($db);

//typeで管理者とユーザーの判断
//管理者 → type = 1
//ユーザ → type = 2
if ($user['type'] === 1) {
  $history_data = history_admin($db);
} else if ($user['type'] === 2) {
  $history_data = history_user($db ,$user['user_id']);
}

include_once VIEW_PATH . 'history_view.php';