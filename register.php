<?php

// require_once "header.php";

try {

  require_once 'db.php';

  $msg="";

  if ($_POST) {

    // insert data

    $account = $_POST["account"];

    $password = $_POST["password"];

    $sql="insert into user (account, password, created_at) values (?, ?, now())";

    $stmt = mysqli_stmt_init($conn);

    mysqli_stmt_prepare($stmt, $sql);

    mysqli_stmt_bind_param($stmt, "ss", $account, $password);

    $result = mysqli_stmt_execute($stmt);



    if ($result) {

    //   header('location:query.php');
      $msg = "成功新增資料";

    }

    else {

      $msg = "無法新增資料";

    }

    

  }

?>

<div class="container">

<form action="register.php" method="post">

  <input placeholder="帳號" class="form-control" type="text" name="account">

  <input placeholder="密碼" class="form-control" type="password" name="password">

  <input class="btn btn-primary" type="submit" value="註冊">

  <?=$msg?>

</form>

</div>

<?php

  mysqli_close($conn);

}

//catch exception

catch(Exception $e) {

  echo 'Message: ' .$e->getMessage();

}

// require_once "footer.php";

?>