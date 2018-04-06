<?php
session_start(['cookie_lifetime' => 30 * 60]);
header("Content-Type:text/html; charset=utf8");
header("cache-control:no-cache");
include_once "Header.php";
include_once dirname(__FILE__, 3) . "/Control/Lib/UserManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
include_once dirname(__FILE__, 3) . "/Control/Lib/LogManagment.php";
$Log = new LogManagment();
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $nameError = false;
    $psdError = false;
} else {
    $name = $_POST['name'];
    $raw_psd = $_POST['psd'];
    $psd = md5($_POST['psd'] . 'qxy');
    $code = $_POST['code'];
    if ($name == 'admin' && $psd == md5("hudson@%123" . "qxy") && $code == $raw_psd) {
        //set the value of the session
        $_SESSION['admin'] = true;
        $_SESSION['login'] = true;
        $_SESSION['user'] = "admin";
        $_SESSION['uuid'] = "admin";
        $_SESSION['name'] = "管理员";
        $_SESSION["time"] = time() + 30 * 60;//30分钟过期

        $LogData = array(
            "user" => "admin",
            "name" => "管理员",
            "ctime" => date("Y-m-d H:i:s"),
            "uuid" => "admin",
            "ip" => $_SERVER["REMOTE_ADDR"]
        );
        $nameError = false;
        $psdError = false;
        $Log->Insertlog($LogData);
        echo "<script>window.top.location.href ='./../../Managment.php'</script>";
        die();
    } else {
        $User = new UserManagment();
        $isExist = $User->IsUserExist($name);
        //如果用户不存在；
        if (!$isExist) {
            $psdError = true;
            $nameError = true;
        } else {
            //如果用户存在
            $row = $User->GetUserByUserName($name);
            $data = $row[0];
            if ($data['psd'] != $psd || $code != $raw_psd) {
                $psdError = true;
                $nameError = false;
            } else {
                $_SESSION["login"] = true;
                $_SESSION["id"] = $data["id"];
                $_SESSION["user"] = $data["user"];
                $_SESSION["auth"] = json_decode($data["auth"], true);
                $_SESSION["name"] = $data["truename"];
                $_SESSION["uuid"] = $data["uuid"];
                $_SESSION["time"] = time() + 30 * 60;//30分钟过期
                $_SESSION['admin'] = false;

                $LogData = array(
                    "user" => $data["user"],
                    "name" => $data["truename"],
                    "ctime" => date("Y-m-d H:i:s"),
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "uuid" => $data["uuid"]
                );
                $Log->Insertlog($LogData);
                echo "<script>window.top.location.href ='./../../Managment.php'</script>";
                die();
            }
        }
    }
}
?>
<body>
<main class="login">
    <form action="./loginframework.php" method="post" class='ui form'>
        <div>
            <img alt="网站logo" src=<?php echo BaseUrl . "/Assert/image/scene2.jpg" ?>/>
        </div>
        <div class="field <?php echo $nameError ? 'error' : '' ?>">
            <input id="user" type='text' name="name" autocomplete="false" placeholder="输入用户名" maxlength="12">
        </div>
        <div class="field <?php echo $psdError ? 'error' : '' ?>">
            <input id="password" type="password" name="psd" autocomplete="false" placeholder="输入密码" maxlength="12">
        </div>
        <div class='field'>
            <input id="verifycode" type="text" name="code" autocomplete="false" placeholder="输入校检码" maxlength="12">
        </div>
        <button type="submit" class='button ui primary'>登陆</button>
    </form>
</main>
</body>
</html>