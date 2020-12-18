<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
  <?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>
  <p>注文番号：<?php print $order_id ?></p>
  <p>購入日時：<?php print $created ?></p>
  <p>合計金額：<?php print $total ?></p>

  <div class="container">
    <h1>購入明細</h1>

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <table class="table table-bordered text-center">
      <thead class="thead-light">
        <tr>
          <th>商品名</th>
          <th>商品の価格</th>
          <th>購入数</th>
          <th>小計</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($details_data as $value){ ?>
        <tr>
          <td><?php print $value['name'] ?></td>
          <td><?php print $value['price']; ?></td>
          <td><?php print $value['amount']; ?></td>
          <td><?php print $value['price']*$value['amount']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>