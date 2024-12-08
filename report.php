<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webprogramming";
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("資料庫連接失敗: " . $conn->connect_error);
}


$eventsQuery = "SELECT event_id, name FROM events";
$eventsResult = $conn->query($eventsQuery);


$todayDate = date('Y 年 m 月 d 日');


$selectedEventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;

if ($selectedEventId) {

    $budgetQuery = "
        SELECT 
            b.category_id,
            bc.category_name,
            b.budget_amount,
            IFNULL(SUM(t.amount), 0) AS actual_spent
        FROM budgets b
        LEFT JOIN budget_categories bc ON b.category_id = bc.category_id
        LEFT JOIN transactions t ON b.budget_id = t.budget_id
        WHERE b.event_id = ?
        GROUP BY b.category_id, b.budget_amount, bc.category_name
        ORDER BY b.category_id
    ";

    $budgetStmt = $conn->prepare($budgetQuery);
    $budgetStmt->bind_param("i", $selectedEventId);
    $budgetStmt->execute();
    $budgetResult = $budgetStmt->get_result();

  
    $fundingQuery = "
        SELECT funding_name, funding_amount
        FROM fundings
        WHERE event_id = ?
    ";

    $fundingStmt = $conn->prepare($fundingQuery);
    $fundingStmt->bind_param("i", $selectedEventId);
    $fundingStmt->execute();
    $fundingResult = $fundingStmt->get_result();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>活動經費報表</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .report {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        .report th, .report td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .report th {
            background-color: #f2f2f2;
        }
        .form-date {
            text-align: left;
        }
        .dropdown {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>財務報告</h1>
<p class="form-date">填表日期：<?= $todayDate ?></p>


<form method="get" class="dropdown">
    <label for="event_id">選擇活動：</label>
    <select name="event_id" id="event_id">
        <option value="">請選擇活動</option>
        <?php while ($row = $eventsResult->fetch_assoc()): ?>
            <option value="<?= $row['event_id'] ?>" <?= $selectedEventId == $row['event_id'] ? 'selected' : '' ?>>
                <?= $row['name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit">生成報表</button>
</form>

<?php if ($selectedEventId): ?>
  
    <h2>預決算比較</h2>
    <table class="report">
        <thead>
            <tr>
                <th>編號</th>
                <th>項目</th>
                <th>預算支出金額 (元)</th>
                <th>實際支出金額 (元)</th>
                <th>差額 (元)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalBudget = $totalActual = $totalDifference = 0;
            $index = 1;
            while ($row = $budgetResult->fetch_assoc()): 
                $difference = $row['budget_amount'] - $row['actual_spent'];
                $totalBudget += $row['budget_amount'];
                $totalActual += $row['actual_spent'];
                $totalDifference += $difference;
            ?>
                <tr>
                    <td><?= $index++ ?></td>
                    <td><?= $row['category_name'] ?></td>
                    <td><?= number_format($row['budget_amount']) ?></td>
                    <td><?= number_format($row['actual_spent']) ?></td>
                    <td><?= number_format($difference) ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="2">合計</td>
                <td><?= number_format($totalBudget) ?></td>
                <td><?= number_format($totalActual) ?></td>
                <td><?= number_format($totalDifference) ?></td>
            </tr>
        </tbody>
    </table>

   
    <h2>經費來源明細</h2>
    <table class="report">
        <thead>
            <tr>
                <th>編號</th>
                <th>項目</th>
                <th>實際收入金額 (元)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalFunding = 0;
            $index = 1;
            while ($row = $fundingResult->fetch_assoc()): 
                $totalFunding += $row['funding_amount'];
            ?>
                <tr>
                    <td><?= $index++ ?></td>
                    <td><?= $row['funding_name'] ?></td>
                    <td><?= number_format($row['funding_amount']) ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="2">合計</td>
                <td><?= number_format($totalFunding) ?></td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
