<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//カートの操作

//fechAll
//ユーザーがカートに入れている商品を一覧で表示する用
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  $params = [
    ['value'=>$user_id,'type'=>PDO::PARAM_INT]
  ];
  return fetch_query_bind($db, $sql ,$params);
}

//fech
//カート内に商品があるか確認する用
//cartテーブル内で、ユーザーIDと商品IDが一致する物
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";
  $params = [
    ['value'=>$user_id,'type'=>PDO::PARAM_INT],
    ['value'=>$item_id,'type'=>PDO::PARAM_INT]
  ];

  return fetch_query($db ,$sql ,$params);

}
//fech
function add_cart($db, $user_id, $item_id ) {
  //カートに商品があればupdate、なければinsert
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//変更済み
//カートに商品追加
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";
  $params = [
    ['value'=>$item_id,'type'=>PDO::PARAM_INT],
    ['value'=>$user_id,'type'=>PDO::PARAM_INT],
    ['value'=>$amount,'type'=>PDO::PARAM_INT]
  ];
  return execute_query_bind($db ,$params ,$sql);
}

//変更済み
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  $params = [
    ['value'=>$amount,'type'=>PDO::PARAM_INT],
    ['value'=>$cart_id,'type'=>PDO::PARAM_INT]
  ];
  return execute_query_bind($db ,$params ,$sql);
}

//変更済み
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  $params = [
    ['value'=>$cart_id,'type'=>PDO::PARAM_INT]
  ];
  return execute_query_bind($db ,$params ,$sql);
}

//商品購入時の処理(在庫数、カートの中身)
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  //商品数に応じてforeachを回す
  foreach($carts as $cart){
    //在庫数の更新
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  //ユーザーのカートの中身を空にする
  delete_user_carts($db, $carts[0]['user_id']);
}

//商品購入時の処理(履歴の書き込み)
// function purchase_history($db ,$user_id ,$carts) {
//   try{
//     $db->beginTransaction(); 
//     //historyテーブル(購入履歴)に書き込む 
//     //historyテーブルのIDを取得する
//     $history_id = insert_history($db ,$user_id);
//     //商品の数だけDBに登録
//     //history_idは同一
//     foreach ($carts as $cart) {
//       // var_dump($cart);
//       //history_idを元に購入明細を作る
//       insert_details($db ,$history_id ,$cart['item_id'] ,$cart['amount']);
//     }
//     $db->commit();
//   }catch (Exception $e) {
//     $db->rollBack();
//   }
// }

//historyとdetailsへ書き込み
function purchase_history($db ,$user_id ,$carts) {
  
  //historyテーブル(購入履歴)に書き込む 
  //historyテーブルのIDを取得する
  insert_history_details($db ,$user_id ,$carts);
}

//変更済み
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";
  $params = [
    ['value'=>$user_id,'type'=>PDO::PARAM_INT]
  ];
  execute_query_bind($db ,$params ,$sql);
}

//isert_historyとinsert_detailsを一つの関数にしたもの
//質問する
function insert_history_details($db ,$user_id ,$carts) {
  try {
    $db->beginTransaction(); 
    //history書き込み
    $sql1 = "
      INSERT INTO
        history(
          user_id,
          created
        )
      VALUES(?, ?)
    ";
    $params = [
      ['value'=>$user_id,'type'=>PDO::PARAM_INT],
      ['value'=>date,'type'=>PDO::PARAM_STR]
    ];
    $history_id = execute_query_bind_lastinsertid($db ,$params ,$sql1);
    //details書き込み
    //商品数分書き込み
    foreach ($carts as $cart) {
      $sql2 = "
      INSERT INTO
        details(
          order_id,
          item_id,
          amount
        )
      VALUE(?,?,?)
      ";
      $params = [
        ['value'=>$history_id,'type'=>PDO::PARAM_INT],
        ['value'=>$cart['item_id'],'type'=>PDO::PARAM_INT],
        ['value'=>$cart['amount'],'type'=>PDO::PARAM_INT]
      ];
      execute_query_bind($db ,$params ,$sql2);
    }
    $db->commit();
  }catch(Exception $e) {
    $db->rollBack();
  }
}

//historyテーブル(購入履歴)に書き込む
// function insert_history($db ,$user_id) {
//   $sql = "
//     INSERT INTO
//       history(
//         user_id,
//         created
//       )
//     VALUES(?, ?)
//   ";
//   $params = [
//     ['value'=>$user_id,'type'=>PDO::PARAM_INT],
//     ['value'=>date,'type'=>PDO::PARAM_STR]
//   ];
//   return execute_query_bind_lastinsertid($db ,$params ,$sql);
// }

// //detailsテーブル(購入明細)に書き込む
// function insert_details($db ,$history_id ,$item_id ,$amount) {
//   $sql = "
//     INSERT INTO
//       details(
//         order_id,
//         item_id,
//         amount
//       )
//     VALUE(?,?,?)
//   ";
//   $params = [
//     ['value'=>$history_id,'type'=>PDO::PARAM_INT],
//     ['value'=>$item_id,'type'=>PDO::PARAM_INT],
//     ['value'=>$amount,'type'=>PDO::PARAM_INT]
//   ];
//   execute_query_bind($db ,$params ,$sql);
// }

//カートの商品の数だけforeachを回す
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}


function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

