<!DOCTYPE html>
<html lang="en">
<head>
  <title>カレンダー</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <style>
    font.holy {font-family: tahoma;font-size: 20px;color: #FF6C21;}
    font.blue {font-family: tahoma;font-size: 20px;color: #0000FF;}
    font.black {font-family: tahoma;font-size: 20px;color: #000000;}
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<?php include('calendarsouse.php') ?>
<div class="container">
<table class="table table-bordered table-responsive">
  <tr align="center" >
    <td>
        <a href=<?php echo 'calrendar.php?year='.$preyear.'&month='.$month . '&day=1'; ?>>◀◀</a>
    </td>
    <td>
        <a href=<?php echo 'calrendar.php?year='.$prev_year.'&month='.$prev_month . '&day=1'; ?>>◀</a>
    </td>
    <td height="50" bgcolor="#FFFFFF" colspan="3">
        <a href=<?php echo 'calrendar.php?year=' . $thisyear . '&month=' . $thismonth . '&day=1'; ?>>
        <?php echo "&nbsp;&nbsp;" . $year . '年 ' . $month . '月 ' . "&nbsp;&nbsp;"; ?></a>
    </td>
    <td>
        <a href=<?php echo 'calrendar.php?year='.$next_year.'&month='.$next_month.'&day=1'; ?>>▶</a>
    </td>
    <td>
        <a href=<?php echo 'calrendar.php?year='.$nextyear.'&month='.$month.'&day=1'; ?>>▶▶</a>
    </td>
  </tr>
  <tr class="info">
    <th hight="30">日</td>
    <th>月</th>
    <th>火</th>
    <th>水</th>
    <th>木</th>
    <th>金</th>
    <th>土</th>
  </tr>

  <?php
    // 画面に表示するデフォルトを1に設定
    $day=1;

    //  週の数に合わせて縦線生成
    for($i=1; $i <= $total_week; $i++){?>
  <tr>
    <?php
    //  よこ生成
    for ($j = 0; $j < 7; $j++) {
        // 始まる週で始まる曜日より$jが小さいか、最後の週で$jが最後の曜日より大きいなら表示しない
        echo '<td height="50" valign="top">';
        if (!(($i == 1 && $j < $start_week) || ($i == $total_week && $j > $last_week))) {

            if ($j == 0) {
                // $jが 0日曜日なので赤文字
                $style = "holy";
            } else if ($j == 6) {
                // $jが0だったら土曜日なので青文字
                $style = "blue";
            } else {
                // その以外の平日黒文字
                $style = "black";
            }

            // 今日だったら太文字
            if ($year == $thisyear && $month == $thismonth && $day == date("j")) {
                // 日出力
                echo '<font class='.$style.'>';
                echo $day;
                echo '</font>';
            } else {
                echo '<font class='.$style.'>';
                echo $day;
                echo '</font>';
            }
            // 日ぞうか
            $day++;
        }
        echo '</td>';
    }
 ?>
  </tr>
  <?php } ?>
</table>
</div>

</body>
</html>