<?php
// ---- 現在の年月日を取得
$thisyear = date('Y'); // 四下駄年度
$thismonth = date('n'); // 0を含まない月
$today = date('j'); // 0を含まない日

// ------ $year, $month のデータがなければ現在の日にち
$year = isset($_GET['year']) ? $_GET['year'] : $thisyear;
$month = isset($_GET['month']) ? $_GET['month'] : $thismonth;
$day = isset($_GET['day']) ? $_GET['day'] : $today;

$prev_month = $month - 1;
$next_month = $month + 1;
$prev_year = $next_year = $year;
if ($month == 1) {
    $prev_month = 12;
    $prev_year = $year - 1;
} else if ($month == 12) {
    $next_month = 1;
    $next_year = $year + 1;
}
$preyear = $year - 1;
$nextyear = $year + 1;

$predate = date("Y-m-d", mktime(0, 0, 0, $month - 1, 1, $year));
$nextdate = date("Y-m-d", mktime(0, 0, 0, $month + 1, 1, $year));

// 全ての日数
$max_day = date('t', mktime(0, 0, 0, $month, 1, $year)); // 当月の末日
// echo '全ての日数'.$max_day.'<br />';

// 始まる曜日
$start_week = date("w", mktime(0, 0, 0, $month, 1, $year)); // 日曜日 0, 土曜日 6

// 全ての週
$total_week = ceil(($max_day + $start_week) / 7);

// 最後の曜日
$last_week = date('w', mktime(0, 0, 0, $month, $max_day, $year));

// 休日
$Holidays = Array();
$Holidays[] = array(0 => '1-1', 1 => '元日');
$Holidays[] = array(0 => '1-14', 1 => '成人の日');
$Holidays[] = array(0 => '2-11', 1 => '建国記念の日');
$Holidays[] = array(0 => '3-21', 1 => '春分の日');
$Holidays[] = array(0 => '4-29', 1 => '昭和の日');
$Holidays[] = array(0 => '4-30', 1 => '国民の休日');
$Holidays[] = array(0 => '5-1', 1 => '天皇の即位の日');
$Holidays[] = array(0 => '5-2', 1 => '国民の休日');
$Holidays[] = array(0 => '5-3', 1 => '憲法記念日');
$Holidays[] = array(0 => '5-4', 1 => 'みどりの日');
$Holidays[] = array(0 => '5-5', 1 => 'こどもの日');
$Holidays[] = array(0 => '5-6', 1 => '振替休日');
$Holidays[] = array(0 => '7-15', 1 => '海の日');
$Holidays[] = array(0 => '8-11', 1 => '山の日');
$Holidays[] = array(0 => '8-12', 1 => '振替休日');
$Holidays[] = array(0 => '9-16', 1 => '敬老の日');
$Holidays[] = array(0 => '9-23', 1 => '秋分の日');
$Holidays[] = array(0 => '10-14', 1 => '体育の日');
$Holidays[] = array(0 => '10-22', 1 => '即位礼正殿の儀の行われる日');
$Holidays[] = array(0 => '11-3', 1 => '文化の日');
$Holidays[] = array(0 => '11-4', 1 => '振替休日');
$Holidays[] = array(0 => '11-23', 1 => '勤労感謝の日');
?>