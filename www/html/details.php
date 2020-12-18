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

//注文履歴
$order_id = $_POST['order_id'];
$created = $_POST['created'];
$total = $_POST['total'];

//詳細画面のデータを取得
$details_data = details($db ,$order_id);
$details_data = entity_assoc_array($details_data);

include_once VIEW_PATH . 'details_view.php';