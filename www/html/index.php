<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//トークンの生成
//セッションに保存
get_csrf_token();
$db = get_db_connect();
$user = get_login_user($db);
//ステータスが公開の商品数のカウント用
$items_num = get_open_items($db);


//全部で商品が何件あるか
$items_num = count($items_num);

// //トータルページ数(小数点切り捨て)
// ITEM_PAGE(8)で全商品分を割る
$total_page = ceil($items_num / ITEM_PAGE);

//getでURLに渡されたpege番号を取得する
//設定されてなければ1にする
if (preg_match('/^[1-9][0-9]*$/', $_GET['page'])) {
  $page = (int)$_GET['page'];
} else {
  $page = 1;
};
//配列の何番目から取得すればいい_
//offset = (現在のページ - 1)×表示する件数
$offset = ($page - 1)*ITEM_PAGE;

//ページネーション用
//8件表示用
$items = get_limit_items($db ,$offset);

$itmes_entity = entity_assoc_array($items);
include_once VIEW_PATH . 'index_view.php';