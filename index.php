<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>ダンス教室予約ページ</title>
<style type="text/css">
table {
    width: 100%;
}
table th {
    background: #EEEEEE;
}
table th,
table td {
    border: 1px solid #CCCCCC;
    text-align: center;
    padding: 5px;
}

</style>
</head>
<body>
<?php include('calendar.php') ?>
<?php echo $year; ?>年<?php echo $month; ?>月のカレンダー
<br>
<br>
<table>
    <tr>
        <th>日</th>
        <th>月</th>
        <th>火</th>
        <th>水</th>
        <th>木</th>
        <th>金</th>
        <th>土</th>
    </tr>

    <tr>
    <?php $cnt = 0; ?>
    <?php foreach ($calendar as $key => $value): ?>

        <td>
        <?php $cnt++; ?>
        <?php echo $value['day']; ?>
        </td>

    <?php if ($cnt == 7): ?>
    </tr>
    <tr>
    <?php $cnt = 0; ?>
    <?php endif; ?>

    <?php endforeach; ?>
    </tr>
</table>
</body>
</html>