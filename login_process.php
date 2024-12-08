<?php

session_start();

//檢查是否取得POST內容

$account = $_POST['account'] ?? "N/A";

//因為db.php裡有$password

$_password = $_POST['password'] ?? "N/A";

try {

    require_once 'db.php';

    // $sql = "select * from user where account = '$account'";

    // $result = mysqli_query($conn, $sql);
    /* create a prepared statement */

    $sql = "select * from user where account = ?";

    $stmt = mysqli_stmt_init($conn);

    mysqli_stmt_prepare($stmt, $sql);

    /* bind parameters for markers */

    mysqli_stmt_bind_param($stmt, "s", $account);

    /* execute query */

    mysqli_stmt_execute($stmt);

    /* get results */

    $result = mysqli_stmt_get_result($stmt);


    if ($row = mysqli_fetch_assoc($result)) {

        if ($row['password'] == $_password) {

            echo "登入成功";

            $_SESSION["account"] = $account;

            header("Location: index.php?page=firstpage");
        } else {

            echo "登入失敗";

            header("Location: index.php?page=login&msg=帳密錯誤");
        }
    } else {

        echo "登入失敗";

        header("Location: index.php?page=login&msg=帳密錯誤");
    }

    // $conn = null;
    mysqli_stmt_close($stmt);

    mysqli_close($conn);
    
}  //catch exception

catch (Exception $e) {

    echo 'Message: ' . $e->getMessage();
}
