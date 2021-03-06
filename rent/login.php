<?php require_once('Connections/cralwer.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($cralwer, $theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
    {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

      $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($cralwer, $theValue) : mysqli_escape_string($cralwer, $theValue);

      switch ($theType) {
        case "text":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
          break;    
        case "long":
        case "int":
          $theValue = ($theValue != "") ? intval($theValue) : "NULL";
          break;
        case "double":
          $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
          break;
        case "date":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
          break;
        case "defined":
          $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
          break;
      }
      return $theValue;
    }
}

mysqli_select_db($cralwer,$database_cralwer);
$query_user = "SELECT * FROM `user`";
$user = mysqli_query($cralwer,$query_user );
$row_user = mysqli_fetch_assoc($user);
$totalRows_user = mysqli_num_rows($user);

// *** Validate request to login to this site.
if (!isset($_SESSION)) {
    session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}


include 'encrypt.php';

if (isset($_POST['account'])) {
    $loginUsername = $_POST['account'];
    $Pass_query = "SELECT account,password from `user` where account='$loginUsername'";
    $Pass_Select = mysqli_query($cralwer,$Pass_query);
    $row_pass = mysqli_fetch_assoc($Pass_Select);
    if ($_POST['password'] == decryptthis($row_pass['password'], $key)) {
        $password = $row_pass['password'];
    }

    $MM_fldUserAuthorization = "";
    $MM_redirectLoginSuccess = "index.php";
    $MM_redirectLoginFailed = "login.php?check=err";
    $MM_redirecttoReferrer = false;
    mysqli_select_db($cralwer,$database_cralwer);

    $LoginRS__query = sprintf(
        "SELECT account, password FROM `user` WHERE account=%s AND password=%s",
        GetSQLValueString($cralwer, $loginUsername, "text"),
        GetSQLValueString($cralwer, $password, "text")
    );

    $LoginRS = mysqli_query($cralwer,$LoginRS__query);
    $loginFoundUser = mysqli_num_rows($LoginRS);
    if ($loginFoundUser) {
        $loginStrGroup = "";

        //declare two session variables and assign them
        $_SESSION['MM_Username'] = $loginUsername;
        $_SESSION['MM_UserGroup'] = $loginStrGroup;

        if (isset($_SESSION['PrevUrl']) && false) {
            $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
        }
        header("Location: " . $MM_redirectLoginSuccess);
    } else {
        header("Location: " . $MM_redirectLoginFailed);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>作伙</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="google-signin-client_id" content="106996317158-4im0d5hkld50a5ucueqqodvptgpuu6km.apps.googleusercontent.com">
    <link rel="icon" href="images/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="src/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <style>
        body {
            font-family: 微軟正黑體;
            background-image: url('images/LoginBackground.jpg');
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }
    </style>
</head>

<body>

    <!-- navbar -->
    <nav class="navbar navbar-expand-md navbar-dark myHeader">
        <a class="navbar-brand" href="index.php">
            <img src="images/WhiteIcon.png" width="28" class="d-inline-block align-top">
            作伙
        </a>
    </nav>

    <!-- container -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-4">
                <form class="formContainer LoginFormMargin" action="<?php echo $loginFormAction; ?>" name="login" method="POST">

                    <!-- 驗證帳號密碼是否正確 -->
                    <?php $_GET['check'] = isset($_GET['check']) ? $_GET['check'] : "";
                    if ($_GET['check'] == 'err') { ?>
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            帳號或密碼錯誤
                        </div>
                    <?php } ?>

                    <section class="title">
                        <img src="images/BrownIcon.png" alt="logo" class="HomeIcon"> | 登入尋找喜歡的房！
                    </section>

                    <div class="form-group">
                        <label for="EmailAddress"><b>電子郵件&nbsp;|&nbsp;Email&nbsp;Address</b></label>
                        <input class="form-control" type="email" name="account" id="email" placeholder="xxxxx@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="Password"><b>密碼&nbsp;|&nbsp;Password</b></label>
                        <input class="form-control" type="password" name="password" id="pwd" placeholder="輸入密碼" required>
                    </div>

                    <a href="" style="float:right; font-size: 10px; color: red;">忘記密碼&nbsp;|&nbsp;Forget Password?</a>

                    <div class="form-group">
                        <label for="CAPTCHA"><b>驗證碼&nbsp;|&nbsp;CAPTCHA</b></label>
                        <table>
                            <tr>
                                <td width="70%"><input class="form-control" id="captcha" type="text" placeholder="輸入驗證碼" required></td>
                                <td width="30%" style="padding-left:5px"><canvas id="canvas" width="125" height="40"></canvas></td>
                            </tr>
                        </table>
                    </div>

                    <div>
                        <table style="width:100%">
                            <tr>
                                <td class="w-50">
                                    <button type="button" class="btn btn-block btn-outline-secondary" onclick="GoogleLogin();">
                                        <img src="images/Google_Logo.png" width="16.5px" class="googleLogo">&nbsp; Google登入
                                    </button>
                                </td>
                                <!-- <td class="w-50">
                                    <div class="g-signin2"></div>
                                </td> -->
                                <td class="w-50"><button type="submit" id="btnLogin" class="btn btn-block btnGo">登入！</button></td>
                            </tr>
                        </table>
                    </div>

                    <small class="form-text text-muted">還沒加入作伙嗎？<b><a href="register.php">註冊</a></b></small>
                </form>
            </div>
        </div>
    </div>

    <script src="src/captcha.js"></script>
    <!--Google登入-->
    <script async defer src="https://apis.google.com/js/api.js" onload="this.onload=function(){};HandleGoogleApiLibrary()" onreadystatechange="if (this.readyState === 'complete') this.onload()"></script>
    <script type="text/javascript">
        //進入 https://console.developers.google.com/，找「憑證」頁籤(記得先選對專案)，即可找到用戶端ID
        let Google_appId = "106996317158-4im0d5hkld50a5ucueqqodvptgpuu6km.apps.googleusercontent.com";


        //參考文章：http://usefulangle.com/post/55/google-login-javascript-api 

        // Called when Google Javascript API Javascript is loaded
        function HandleGoogleApiLibrary() {
            // Load "client" & "auth2" libraries
            gapi.load('client:auth2', {
                callback: function() {
                    // Initialize client & auth libraries
                    gapi.client.init({
                        clientId: Google_appId,
                        scope: 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me'
                    }).then(
                        function(success) {
                            // Google Libraries are initialized successfully
                            // You can now make API calls 
                            console.log("Google Libraries are initialized successfully");
                        },
                        function(error) {
                            // Error occurred
                            console.log(error); // to find the reason 
                        }
                    );
                },
                onerror: function() {
                    // Failed to load libraries
                    console.log("Failed to load libraries");
                }
            });
        }

        function GoogleLogin() {
            // API call for Google login  
            gapi.auth2.getAuthInstance().signIn().then(
                function(success) {
                    // Login API call is successful 
                    // console.log(success.getBasicProfile().getName());
                    // console.log(success.getBasicProfile().getEmail());
                    // console.log(success.getBasicProfile().getImageUrl());

                    if (success.getBasicProfile().getEmail() != '') {
                        $(document).ready(function() {
                            $.ajax({
                                type: "POST",
                                url: "get_data.php",
                                data: {
                                    'Action': 'register',
                                    'UserName': success.getBasicProfile().getName(),
                                    'UserAccount': success.getBasicProfile().getEmail(),
                                    'Image': success.getBasicProfile().getImageUrl(),
                                    'UserPwd': success.getId()
                                },
                                contentType: "application/x-www-form-urlencoded; charset=utf-8",
                                success: function(data) {
                                    document.getElementById('email').value = success.getBasicProfile().getEmail();
                                    document.getElementById('pwd').value = success.getId();
                                    document.getElementById("captcha").value = code;
                                    document.getElementById("btnLogin").click();
                                },
                                error: function(e) {
                                    console.log('error', e)
                                }
                            });
                        });
                    }
                },
                function(error) {
                    // Error occurred
                    // console.log(error) to find the reason
                    console.log(error);
                    alert('登入失敗，訊息=>' + error.error)
                }
            );
        }
    </script>
</body>

</html>
<?php
mysqli_free_result($user);
?>