<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//購入履歴
//購入明細

function history_user($db ,$user_id) {
  $sql = "SELECT"

  . "      H.order_id,"

  . "      H.created,"

  . "      SUM(D.amount*I.price) AS total"

  . "    FROM"

  . "      history as H"

  . "    INNER JOIN details as D"

  . "      ON H.order_id = D.order_id"

  . "    INNER JOIN items as I"

  . "      ON D.item_id = I.item_id"

  . "    WHERE"

  . "      user_id = ?"

  . "     GROUP BY H.order_id";;
  $params = [
    ['value'=>$user_id,'type'=>PDO::PARAM_INT]
  ];
  return fetch_query_bind($db ,$sql ,$params);
}

function details($db,$order_id) {
  $sql = "
  SELECT
    D.amount,
    I.name,
    I.price
  FROM
    history AS H
  INNER JOIN details AS D
    ON H.order_id = D.order_id
  INNER JOIN items AS I
    ON D.item_id = I.item_id
  WHERE
    H.order_id = ?
  ";
  $params = [
    ['value'=>$order_id,'type'=>PDO::PARAM_INT]
  ];
  return fetch_query_bind($db ,$sql ,$params);
}

function history_admin($db) {
  
  $sql = "SELECT"

  . "      H.order_id,"

  . "      H.created,"

  . "      SUM(D.amount*I.price) AS total"

  . "    FROM"

  . "      history as H"

  . "    INNER JOIN details as D"

  . "      ON H.order_id = D.order_id"

  . "    INNER JOIN items as I"

  . "      ON D.item_id = I.item_id"

  . "     GROUP BY H.order_id";;
  // SELECT
  //   H.order_id,
  //   H.created,
  //   D.amount,
  //   I.name,
  //   I.price
  // FROM
  //   history AS H
  // INNER JOIN details AS D
  //   ON H.order_id = D.order_id
  // INNER JOIN items AS I
  //   ON D.item_id = I.item_id
  // ";
  return fetch_query_bind($db ,$sql);
}

// function details_admin($db ,$order_id) {
//   $sql = "
//   SELECT
//     H.order_id,
//     H.created,
//     D.amount,
//     I.name,
//     I.price
//   FROM
//     history AS H
//   INNER JOIN details AS D
//     ON H.order_id = D.order_id
//   INNER JOIN items AS I
//     ON D.item_id = I.item_id
//   WHERE
//     H.order_id = ?
//   ";
//   $params = [
//     ['value'=>$order_id,'type'=>PDO::PARAM_INT]
//   ];
  
//   return fetch_query_bind($db ,$sql ,$params);
// }
