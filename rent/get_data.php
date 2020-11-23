<script src="src/get_data.js"></script>
<?php
function Query($offset, $limit, $WebName, $search, $moneyS, $moneyE, $orderby, $dict, $userid, $city, $town)
{
	header('Access-Control-Allow_Origin: *');
    require_once('Connections/cralwer.php');
	mysqli_select_db($cralwer , $database_cralwer);

    $SqlWhere = "";
    if (isset($WebName) && $WebName != "") {
        $SqlWhere .= " AND `WebName` = '{$WebName}'";
    }

    if (isset($search) && $search != "") {
        $SqlWhere .= " AND (`house` like '%{$search}%' OR `adress` like '%{$search}%')";
    }

    if (isset($moneyS) && $moneyS != "") {
        $SqlWhere .= " AND `money` >= '{$moneyS}'";
    }

    if (isset($moneyE) && $moneyE != "") {
        $SqlWhere .= " AND `money` <= '{$moneyE}'";
    }

	if (isset($city) && $city != "") {
		switch ($city){
			case '臺北市':
				$SqlWhere .= " AND (`adress` Like '臺北%' OR `adress` Like '台北%')";
			break;
			case '臺中市':
				$SqlWhere .= " AND (`adress` Like '臺中%' OR `adress` Like '台中%')";
			break;
			case '臺南市':
				$SqlWhere .= " AND (`adress` Like '臺南}%' OR `adress` Like '台南%')";
			break;
			case '臺東縣':
				$SqlWhere .= " AND (`adress` Like '臺東%' OR `adress` Like '台東%')";
			break;
			default:
				$SqlWhere .= " AND `adress` Like '{$city}%'";
			break;
		}
	}
	
	if (isset($town) && $town != "") {
		switch($town){
			case '臺東市':
				$SqlWhere .= " AND (`adress` Like '%臺東市%' or `adress` Like '%台東市%')";
			break;
			default:
				$SqlWhere .= " AND `adress` Like '%{$town}%'";
			break;	
		}
        $SqlWhere .= " AND `adress` Like '%{$town}%'";
	}
	


	$query = "SELECT * FROM `page_data` where (1=1) {$SqlWhere} ORDER BY `{$orderby}` {$dict} LIMIT {$limit} OFFSET {$offset}";
	$data = mysqli_query($cralwer,$query);
	$row = mysqli_fetch_assoc($data);
	do{
		$query_subscribe = "SELECT COUNT(*) countSubscribe FROM `subscription` WHERE `userid` = '{$userid}' AND `Link` = '{$row['Link']}'";
		$subscribeCount = mysqli_query($cralwer,$query_subscribe);
		$row_subscribeCount=mysqli_fetch_assoc($subscribeCount);
		$selectedFav = '<img class="favorite" id="' . $row["Link"] . '" src="images/selectedFav.png" width="20px" onClick="Favorate(this,' . $userid . ')">';
		$favorite = '<img class="favorite" id="' . $row["Link"] . '" src="images/Favorite.png" width="20px" onClick="Favorate(this,' . $userid . ')">';
		$mystr = $row_subscribeCount['countSubscribe']>="1" ? $selectedFav : $favorite;
		$Is_Delete=$row['Is_Delete']=='Y'?"<span class=\"badge badge-danger\" >已下架</span>":"";
		if (isset($userid) AND $userid!="") {
            echo '
			<div class="row justify-content-center">
				<div class="col-12 col-sm-10 col-md-8 col-lg-6">
					<table id="qDTable" class="table table-sm initialism table-borderless bg-white card">
						<tr>
							<td rowspan="4" width="30%" class="text-center align-middle">
								<img class="imageSize" src="' . $row['images'] . '">	
							</td>
							<th colspan="2" width="50%" class="houseName">' .$Is_Delete. $row['house'] . '</th>
							<td rowspan="4" width="2%" class="text-center align-top">'.
							// ($subscribeCount>=1 ? '<img class="favorite" id="' . $row["Link"] . '" src="images/selectedFav.png" width="20px" onClick="Favorate(this,' . $userid . ')">' : '<img class="favorite" id="' . $row["Link"] . '" src="images/favorite.png" width="20px" onClick="Favorate(this,' . $userid . ')">')
							$mystr
							.'</td>
							<td width="18%" class="text-center align-middle houseInfo">來自：' . $row['WebName'] . '</td>
						</tr>
	
						<tr>
							<td colspan="2">' . $row['adress'] . '</td>
							<td rowspan="2" id="Price" class="text-center align-middle housePrice">' . number_format($row['money']) . '</td>
						</tr>
	
						<tr>
							<td class="align-middle houseInfo">坪數：' . $row['square_meters'] . '</td>
							<td class="align-middle houseInfo">形式：' . $row['pattern'] . '</td>
						</tr>
	
						<tr>
							<td class="align-middle houseInfo">樓層：' . $row['floor'] . '</td>
							<td class="align-middle houseInfo">類型：' . $row['house_type'] . '</td>
							<td>
								<a class="btn btn-block btn-sm btnGo" target="_blank" href="' . $row['Link'] . '">查看更多</a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		';
        } else {
            echo '
			<div class="row justify-content-center">
				<div class="col-12 col-sm-10 col-md-8 col-lg-6">
					<table id="qDTable" class="table table-sm initialism table-borderless bg-white card">
						<tr>
							<td rowspan="4" width="30%" class="text-center align-middle"><img class="imageSize" src="' . $row['images'] . '"></td>
							<th colspan="2" width="50%" class="houseName">' .$Is_Delete. $row['house'] . '</th>
							<td rowspan="4" width="2%" class="text-center align-top"></td>
							<td width="18%" class="text-center align-middle houseInfo">來自：' . $row['WebName'] . '</td>
						</tr>
	
						<tr>
							<td colspan="2">' . $row['adress'] . '</td>
							<td rowspan="2" id="Price" class="text-center align-middle housePrice">' . number_format($row['money']) . '</td>
						</tr>
	
						<tr>
							<td class="align-middle houseInfo">坪數：' . $row['square_meters'] . '</td>
							<td class="align-middle houseInfo">形式：' . $row['pattern'] . '</td>
						</tr>
	
						<tr>
							<td class="align-middle houseInfo">樓層：' . $row['floor'] . '</td>
							<td class="align-middle houseInfo">類型：' . $row['house_type'] . '</td>
							<td>
								<a class="btn btn-block btn-sm btnGo" target="_blank" href="' . $row['Link'] . '">查看更多</a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		';
        }
    }while($row=mysqli_fetch_assoc($data));
}
if (isset($_POST['offset']) and isset($_POST['limit'])) {
    /*設定參數*/
    Query($_POST['offset'], $_POST['limit'], $_POST['WebName'], $_POST['search'], $_POST['moneyS'], $_POST['moneyE'], $_POST['orderby'], $_POST['dict'], $_POST['userid'],$_POST['city'],$_POST['town']);
}

function Favorate($Link, $userid)
{
	header('Access-Control-Allow_Origin: *');
    require_once('Connections/cralwer.php');
    mysqli_select_db($cralwer,$database_cralwer);

    $query = "SELECT * FROM `subscription` where (1=1) AND `userid`='{$userid}' AND `Link`='{$Link}'";
    $data = mysqli_query( $cralwer,$query);
    $totalRows = mysqli_num_rows($data);
    if ($totalRows == 0) {
        $insert = "INSERT `subscription` VALUES('{$userid}', '{$Link}')";
        mysqli_query( $cralwer,$insert);
		echo 'Insert';
    } else {
        $delete = "DELETE FROM `subscription` WHERE `subscription`.`userid` = '{$userid}' AND `subscription`.`Link` = '{$Link}'";
        mysqli_query($cralwer,$delete);
		echo 'Delete';
    }
}

function register($UserName, $UserAccount, $Image, $UserPwd){
	header('Access-Control-Allow_Origin: *');
	require_once('Connections/cralwer.php');
    mysqli_select_db($cralwer,$database_cralwer);
	$query="SELECT * FROM `user` where (1=1) AND `account`='{$UserAccount}'";
    $data = mysqli_query($cralwer,$query);
	$totalRows = mysqli_num_rows($data);
	if ($totalRows == 0) {	
		include 'encrypt.php'; //加解密檔
		$mypwd=encryptthis($UserPwd, $key);
		$myUserName=encryptthis($UserName, $key);
		$myImage=encryptthis($Image, $key);
		$insert="INSERT INTO `user` (
			`account` ,
			`password` ,
			`name` ,
			`image` 
			)
			VALUES (
				'{$UserAccount}', '{$mypwd}', '{$myUserName}', '{$myImage}'
			);
			";
        mysqli_query($cralwer,$insert);
		echo 'Register'; 
    } else {
        echo 'Login';
    }
}

function Login($myaccount, $mypassword){
	// require_once('Connections/cralwer.php');
    // mysql_select_db($database_cralwer, $cralwer);
	// mysql_query("SET NAMES 'utf8'"); //修正中文亂碼問題
	// include 'encrypt.php';

	// if (isset($myaccount) {
	// 	$Pass_query = "SELECT account,password from `user` where account='$myaccount'";
	// 	$Pass_Select = mysql_query($Pass_query, $cralwer) or die(mysql_error());
	// 	$row_pass = mysql_fetch_assoc($Pass_Select);
	// 	if ($mypassword == decryptthis($row_pass['password'], $key)) {
	// 		$password = $row_pass['password'];
	// 	}

	// 	$MM_fldUserAuthorization = "";
	// 	$MM_redirectLoginSuccess = "home.php";
	// 	$MM_redirectLoginFailed = "login.php?check=err";
	// 	$MM_redirecttoReferrer = false;
	// 	mysql_select_db($database_cralwer, $cralwer);

	// 	$LoginRS__query = sprintf(
	// 		"SELECT account, password FROM `user` WHERE account=%s AND password=%s",
	// 		GetSQLValueString($myaccount, "text"),
	// 		GetSQLValueString($password, "text")
	// 	);

	// 	$LoginRS = mysql_query($LoginRS__query, $cralwer) or die(mysql_error());
	// 	$loginFoundUser = mysql_num_rows($LoginRS);
	// 	if ($loginFoundUser) {
	// 		$loginStrGroup = "";

	// 		//declare two session variables and assign them
	// 		$_SESSION['MM_Username'] = $loginUsername;
	// 		$_SESSION['MM_UserGroup'] = $loginStrGroup;

	// 		if (isset($_SESSION['PrevUrl']) && false) {
	// 			$MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
	// 		}
	// 		header("Location: " . $MM_redirectLoginSuccess);
	// 	} else {
	// 		header("Location: " . $MM_redirectLoginFailed);
	// 	}
	// }

}

if (isset($_POST['Action'])) {
    switch ($_POST['Action']) {
        case "Favorate":
            Favorate($_POST['Link'], $_POST['userid']);
            break;

		case "register":
			register($_POST['UserName'],$_POST['UserAccount'],$_POST['Image'],$_POST['UserPwd']);
			break;
		
		case "Login":
			Login($_POST['account'],$_POST['password']);
		break;
	}
}

?>
