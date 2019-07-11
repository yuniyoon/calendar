<?php
define("PHP_SELF", $_SERVER['PHP_SELF']);
error_reporting(error_reporting() & ~E_NOTICE);
if (!isset($cellh))
    $cellh = 70; // date cell height
if (!isset($tablew))
    $tablew = 650; //table width
$cellw = 130;
//---- 오늘 날짜
$thisyear = date('Y'); // 4자리 연도
$thismonth = date('n'); // 0을 포함하지 않는 월
$today = date('j'); // 0을 포함하지 않는 일

// $year, $month 값이 없으면 현재 날짜
$year = isset($_GET['year']) ? $_GET['year'] : $thisyear;
$month = isset($_GET['month']) ? $_GET['month'] : $thismonth;
$day = isset($_GET['day']) ? $_GET['day'] : $today;

//------ 날짜의 범위 체크
if (($year > 2038) or ($year < 1900))
    ErrorMsg("연도는 1900 ~ 2038년만 가능합니다.");

$last_day = date('t', mktime(0, 0, 0, $month, 1, $year)); // 해당월의 총일수 구하기

$prevmonth = $month - 1;
$nextmonth = $month + 1;
$prevyear = $nextyear = $year;
if ($month == 1) {
    $prevmonth = 12;
    $prevyear = $year - 1;
} elseif ($month == 12) {
    $nextmonth = 1;
    $nextyear = $year + 1;
}
$pre_year = $year - 1;
$next_year = $year + 1;


include_once 'dbconnect.php'; // DB 연결
include_once 'lun2sol.php';  //양력.음력 변환 인클루드

/****************** lunar_date ************************/
$predate = date("Y-m-d", mktime(0, 0, 0, $month - 1, 1, $year));
$nextdate = date("Y-m-d", mktime(0, 0, 0, $month + 1, 1, $year));

$sql = "SELECT solar_date,ganji,lunar_date,yun FROM lunar_data where solar_date between '$predate' and '$nextdate' ";
$result = mysqli_query($dbconn, $sql) or die(mysqli_error($dbconn));
while ($R = mysqli_fetch_array($result)) {
    $lunarData[] = array(0 => date("n-j", strtotime($R['solar_date'])), 1 => $R['ganji'], 2 => date("n.j", strtotime($R['lunar_date'])), 3 => date("j", strtotime($R['lunar_date'])), 4 => $R['yun']);
}
//echo '<pre>';print_r($lunarData);echo '</pre>';
/****************** lunar_date ************************/

/****************** 기념일 데이터 ************************/
// 음력 기념일을 생성하면 시작년도부터 해당 양력일자를 자동으로 생성 처리??
$sql = "SELECT category,subject,memorial_date,dateType FROM memorials";
$result = mysqli_query($dbconn, $sql);
while ($R = mysqli_fetch_array($result)) {
	if($R['dateType'] == 1){ // 음력
		//$tmp = lun2sol($year . $R['memorial_date']); // 정확도가 맞는지 확인 필요
		$lunar_temp = $year ."-".substr($R['memorial_date'],0,2)."-".substr($R['memorial_date'],2,2);
		$rsql = "SELECT solar_date,num,yun,lunar_date FROM lunar_data where lunar_date='".$lunar_temp."'";
		$rs = mysqli_query($dbconn, $rsql);
		$RS = mysqli_fetch_array($rs);
		$tmp = strtotime($RS[0]);
		if(substr($RS[0],0,4) !== $year){ // 음력 기념일이 같은 해가 아니면
			$sql = "SELECT count(relatedid) FROM memorial_data where relatedid=".$RS[1]." and solar_date='".$RS[0]."' and yun=".$RS[2]." and subject='".$R['subject']."' ";
			$rt = mysqli_query($dbconn, $sql);
// 			$RT = mysqli_fetch_array($rt);
// 			if($RT[0] == 0){ // 일치하는 데이터가 없으면
// 				$sql ="INSERT INTO memorial_data (relatedid,solar_date,lunar_date,yun,subject,category) ";
// 				$sql.="VALUES (".$RS[1].",'".$RS[0]."','".$RS[3]."','".$RS[2]."','".$R['subject']."','".$R['category']."')";
// 				mysqli_query($dbconn, $sql);
// 			}
		}
	} else { // 양력
		$tmp = strtotime($year ."-".substr($R['memorial_date'],0,2)."-".substr($R['memorial_date'],2,2));
	}
	$memorialData[] = array(0 => date("n-j",$tmp), 1 => $R['category'], 2 => $R['subject']);
}

// 음력 기념일이 같은 해에 없는 경우 테이블에 생성된 데이터를 조회 및 추가
$sql = "SELECT solar_date,subject,category FROM memorial_data where solar_date between '$predate' and '$nextdate' ";
$result = mysqli_query($dbconn, $sql);
while ($R = mysqli_fetch_array($result)) {
	$tmp = strtotime($R[0]);
	$memorialData[] = array(0 => date("n-j",$tmp), 1 => $R['category'], 2 => $R['subject']);
}

/****************** 휴일 정의 ************************/
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

//$tmp = lun2sol($year . "0101");  //설날
$tmp = strtotime(Lun2SolDate($year."-01-01"));
$Holidays[] = array(0 => date("n-j", ($tmp - (3600 * 24))), 1 => '');
$Holidays[] = array(0 => date("n-j", $tmp), 1 => '설날');
$Holidays[] = array(0 => date("n-j", ($tmp + (3600 * 24))), 1 => '');

//$tmp = lun2sol($year . "0408");  //석가탄신일
$tmp = strtotime(Lun2SolDate($year."-04-08"));
$Holidays[] = array(0 => date("n-j", $tmp), 1 => '석탄일');

//$tmp = lun2sol($year . "0815"); //추석
$tmp = strtotime(Lun2SolDate($year."-08-15"));
$Holidays[] = array(0 => date("n-j", ($tmp - (3600 * 24))), 1 => '');
$Holidays[] = array(0 => date("n-j", $tmp), 1 => '추석');
$Holidays[] = array(0 => date("n-j", ($tmp + (3600 * 24))), 1 => '');

// 어린이날 대체공휴일 검사 : 어린이날은 토요일, 일요일인 경우 그 다음 평일을 대체공유일로 지정
$childdayChk = isWeekend($year."-05-05");
if($childdayChk == 0) $Holidays[] = array(0 => date("n-j", strtotime($year."-05-06")), 1 => '대체공휴일');
if($childdayChk == 6) $Holidays[] = array(0 => date("n-j", strtotime($year."-05-07")), 1 => '대체공휴일');

// 설날 대체공휴일 검사
if(isWeekend(Lun2SolDate($year."-01-01")) == 0)
	$Holidays[] = array(0 => date("n-j", strtotime(Lun2SolDate($year."-01-03"))), 1 => '');
if(isWeekend(Lun2SolDate($year."-01-01")) == 1)
	$Holidays[] = array(0 => date("n-j", strtotime(Lun2SolDate($year."-01-03"))), 1 => '');
if(isWeekend(Lun2SolDate($year."-01-02")) == 0)
	$Holidays[] = array(0 => date("n-j", strtotime(Lun2SolDate($year."-01-03"))), 1 => '');


// 추석 대체공휴일 검사
if(isWeekend(Lun2SolDate($year."-08-14")) == 0)
	$Holidays[] = array(0 => date("n-j", strtotime(Lun2SolDate($year."-08-17"))), 1 => '');
if(isWeekend(Lun2SolDate($year."-08-15")) == 0)
	$Holidays[] = array(0 => date("n-j", strtotime(Lun2SolDate($year."-08-17"))), 1 => '');
if(isWeekend(Lun2SolDate($year."-08-16")) ==0)
	$Holidays[] = array(0 => date("n-j", strtotime(Lun2SolDate($year."-08-17"))), 1 => '');

/****************** 휴일 정의 ************************/

/****************** schedule ************************/
// 스케줄 테이블 구조는 재설계를 해야 함.
$sql = "SELECT * FROM tbl_events where start between '$predate' and '$nextdate' ";
$result = mysqli_query($dbconn, $sql);
while ($R = mysqli_fetch_array($result)) {
    $schedule[] = array(0 => date("n-j", strtotime($R['start'])), 1 =>date("n-j", strtotime($R['end'])) ,2 => $R['title'],3 => $R['id']);
}
//echo '<pre>';print_r($schedule);echo '</pre>';
/****************** schedule ************************/

?>

<!DOCTYPE html>
<html lang="ko">
<head>
<title>PHP Calendar</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<style>
	body{margin-top: 20px; }
	.all {border-width: 1;border-color: #cccccc;border-style: solid;}
	font {font-family: 굴림체;font-size: 12px;color: #505050;	}
	font.title {font-family: 굴림체;font-size: 12px;font-weight: bold;color: #2579CF;	}
	font.week {font-family: 돋움,돋움체;color: #ffffff;font-size: 8pt;	letter-spacing: -1;}
	font.holy {font-family: tahoma;font-size: 22px;color: #FF6C21;}
    font.blue {font-family: tahoma;font-size: 22px;color: #0000FF;}
    font.black {font-family: tahoma;font-size: 22px;color: #000000;}
	font.lunar {font-family: tahoma;font-size: 14px;color: #0000bb;}
	font.gangi {font-family: tahoma;font-size: 14px;color: #424242;}
	font.sblue {font-family: tahoma;font-size: 14px;color: blue;	}
	font.green {font-family: tahoma;font-size: 14px;color: green;	}
	font.red {font-family: tahoma;font-size: 14px;color: red;}
	font.num {font-family: tahoma;font-size: 14px;background-color: #DBA901;}
	font.gray {font-family: tahoma;font-size: 14px;color: #bbbbbb;}
	.main {float: left;width: 70%;border: 5px solid #ccc;background-color: #fff;m }
	.right {float: right;width: 20%;background-color: #fff;border: 5px solid #eee;}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
	$(".calcell").click(function(){
		var val=$(this).attr("id");
		var date = val.split('-');
		var year = date[0];
		var month = date[1];
		var day = date[2];
		var title = prompt('Event Title:');
		$.ajax({
			url : 'add-event.php',
			type : 'POST',
			data :{year:date[0],month:date[1],day:date[2],title:title},
			success : function(data){
				if(data == 1){
					location.reload();
				} else if(data == 0) {
					alert('등록에 실패했습니다.');
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert("arjax error : " + textStatus + "\n" + errorThrown);
			}
		});
	});

	$(".num").click(function(e){
		var val=$(this).attr("uid");
		var deleteMsg = confirm("정말 삭제하시겠습니까?");
		if(deleteMsg){
			$.ajax({
				url : 'delete-event.php',
				type : 'POST',
				data :{id:val},
				success: function (data) {
					if(data == 1){
						location.reload();
					} else if(data == 0) {
						alert('삭제에 실패했습니다.');
					}
				}
			});
		}
		return false;
	});
});
</script>
</head>
<body>
<div class="container">
<table class="table table-bordered table-responsive">
  <tr align="center" >
    <td>
        <a href=<?php echo 'calendar.php?year='.$pre_year.'&month='.$month . '&day=1'; ?>>◀◀</a>
    </td>
    <td>
        <a href=<?php echo 'calendar.php?year='.$prevyear.'&month='.$prevmonth . '&day=1'; ?>>◀</a>
    </td>
    <td height="50" bgcolor="#FFFFFF" colspan="3">
        <a href=<?php echo 'calendar.php?year=' . $thisyear . '&month=' . $thismonth . '&day=1'; ?>>
        <?php echo "&nbsp;&nbsp;" . $year . '年 ' . $month . '月 ' . "&nbsp;&nbsp;"; ?></a>
    </td>
    <td>
        <a href=<?php echo 'calendar.php?year='.$nextyear.'&month='.$nextmonth.'&day=1'; ?>>▶</a>
    </td>
    <td>
        <a href=<?php echo 'calendar.php?year='.$next_year.'&month='.$month.'&day=1'; ?>>▶▶</a>
    </td>
  </tr>
  <tr class="info">
    <th style="width:14%;text-align:center;">日</th>
    <th style="width:14%;text-align:center;">月</th>
    <th style="width:14%;text-align:center;">火</th>
    <th style="width:14%;text-align:center;">水</th>
    <th style="width:14%;text-align:center;">木</th>
    <th style="width:14%;text-align:center;">金</th>
    <th style="width:14%;text-align:center;">土</th>
  </tr>
  <tr height=<?php echo $cellh;?>>

<?php
    $date = 1;
    $offset = 0;
    $ck_row = 0;
    //프레임 사이즈 조절을 위한 체크인자
    $R = array();

    while ($date <= $last_day) {
        $mday = $date;

        if ($date == '1') {
			// 시작 요일 구하기 : date("w", strtotime($year."-".$month."-01"));
            $offset = date('w', mktime(0, 0, 0, $month, $date, $year)); // 0: 일요일, 6: 토요일
            SkipOffset($offset, mktime(0, 0, 0, $month, $date, $year));
        }
        if ($offset == 0)
            $style = "holy"; // 일요일 빨간색으로 표기
		else if($offset == 6)
			$style = "holy"; // 토요일 빨간색 또는 파란색
        else
            $style = "black";

        // 법적 공휴일
		for ($i = 0; $i < count($Holidays); $i++) {
            if ($Holidays[$i][0] == "$month-$date") {
                $style = "holy";
                $mday = "$date";
                $holidata = $Holidays[$i][1];
                break;
            }
        }

        // 음력 일자 및 간지 데이터
// 		for ($i = 0; $i < count($lunarData); $i++) {
//             if ($lunarData[$i][0] == "$month-$date") {
// 				if($lunarData[$i][4] == 1){ // 윤달이면
// 					$lunarday = '(閏)'.$lunarData[$i][2];
// 				} else {
// 					$lunarday = $lunarData[$i][2];
// 				}
// 				$gaingi_text = $lunarData[$i][1];
//             }
//         }

        // 기념일 : 결혼, 생일, 제사, 기타 등
		for ($i = 0; $i < count($memorialData); $i++) {
            if ($memorialData[$i][0] == "$month-$date") {
                $memorialdata = $memorialData[$i][2];
                break;
            }
        }

        // 사용자 일정 데이터
		$dType1 = array();
		if(isset($schedule)) {
    		for ($i = 0; $i < count($schedule); $i++) {
                if ($schedule[$i][0] == "$month-$date") {
                    $dType1[] = array(0=>$schedule[$i][2],1=>$schedule[$i][3]);
                }
            }

		}

        if (!$offset == 0) {
            $R[] = array(0 => $date, 1 => $holy_text, 2 => $dType1, 3 => $dType2, 4 => $dType3, 5 => $dType4);
        }

        if ($date == $today && $year == $thisyear && $month == $thismonth) { // 오늘 날짜
            echo "<td valign=top bgcolor=#99FFFF class='calcell' id='".$year."-".$month."-".$mday."'>";
        } else {
            echo "<td valign=top class='calcell' id='".$year."-".$month."-".$mday."'>";
		}
			CalendarPrint($style,$mday,$lunarday,$gaingi_text,$holidata,$memorialdata,$dType1);
			echo "</td>\n";

		// 출력후 값 초기화
        $holidata = "";
		$memorialdata ="";

        $date++; // 날짜 증가
        $offset++;
        if ($offset == 7) {
            echo "</tr>";
            if ($date <= $last_day) {
                echo "<tr height=$cellh>";
                $ck_row++;
            }
            $offset = 0;
        }

    }// end of while111

    if ($offset != 0) {
        SkipOffset((7 - $offset), '', mktime(0, 0, 0, $month + 1, 1, $year));
        echo "</tr>\n";
    }
    echo("</td>\n");

	function ErrorMsg($msg) {
		echo " <script>window.alert('$msg');history.go(-1);</script>";
		exit;
	}

	function CalendarPrint($style,$mday,$lunarday,$gaingi,$holidata='',$memorialdata='',$dType1=''){
		echo "<font class=".$style.">$mday</font><br/>";
		echo "<font class=lunar>$lunarday</font><br/>";
		echo "<font class=gangi>$gaingi</font><br/>";
		if(strlen($holidata)>0) echo "<font class=red>$holidata</font><br/>";
		if(strlen($memorialdata)>0) echo "<font class=sblue>$memorialdata</font><br/>";
		if(count($dType1)>0) { // 배열 출력
			for ($i = 0; $i < count($dType1); $i++) {
				echo "<font class=num uid=".$dType1[$i][1].">".$dType1[$i][0]."</font><br/>";
			}
		}
	}

	function SkipOffset($no, $sdate = '', $edate = '') {
		for ($i = 1; $i <= $no; $i++) {
			$ck = $no - $i + 1;
			if ($sdate)
				$num = date('n.j', $sdate - (3600 * 24) * $ck);
			if ($edate)
				$num = date('n.j', $edate + (3600 * 24) * ($i - 1));

			echo "<td valign=top><font class=gray>$num</font></td>";
		}
	}

	function Lun2SolDate($date){
		global $dbconn;
		$sql = "SELECT solar_date FROM lunar_data where lunar_date='".$date."'";
		$result = mysqli_query($dbconn, $sql);
		$R = mysqli_fetch_array($result);
		return $R[0];
	}

	function isWeekend($date){
		// 앙력 날짜의 요일을 리턴
		// 일요일 0 토요일 6
		return date("w", strtotime($date));
	}

?>
    </tr>
</table>
</div>
</body>
</html>

