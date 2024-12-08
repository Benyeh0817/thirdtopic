<?php

$msg = $_GET["msg"] ?? "";

?>

<div class="container">
    <form action="login_process.php" method="post">
        帳號: <input placeholder="帳號" class="form-control" type="text" name="account"><br>
        密碼: <input placeholder="密碼" class="form-control" type="password" name="password"><br>
        <input class="btn btn-primary" type="submit" value="登入">
    </form>
    <!-- <a href="register.php" class="btn btn-primary position-fixed bottom-0 end-0">+</a> -->
    <?php if ($msg): ?>
        <p style="color: red;"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
</div>