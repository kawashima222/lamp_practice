<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
  <?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <div class="container">
    <h1>購入履歴</h1>

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <table class="table table-bordered text-center">
      <thead class="thead-light">
        <tr>
          <th>注文番号</th>
          <th>購入日時</th>
          <th>注文の合計金額</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <!-- <?php if (count($history_data) > 0) { ?> -->
        <!-- <?php foreach($history_data as $value){ ?> -->
        <tr>
          <td><?php print $value['order_id'] ?></td>
          <td><?php print $value['created']; ?></td>
          <td><?php print $value['total']; ?>円</td>
          <form method="post" action="details.php">
            <td>
              <input type="hidden" name="order_id" value="<?php print $value['order_id'] ?>">
              <input type="hidden" name="created" value="<?php print $value['created'] ?>">
              <input type="hidden" name="total" value="<?php print $value['total'] ?>">
              <input type="submit" value="詳細画面へ">
            </td>
          </form>
        </tr>
        <!-- <?php } ?> -->
      </tbody>
    </table>
    <?php } else { ?>
      <p>商品はありません。</p>
    <?php } ?> 
  </div>
</body>
</html>