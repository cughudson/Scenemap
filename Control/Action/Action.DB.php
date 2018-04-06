<?php
header("Content-Type:text/html;charset=utf-8");
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
include_once dirname(__FILE__, 3) . "/Control/Lib/ConfigManagment.php";
include_once dirname(__FILE__, 3) . "/Lib/DBManagment.php";
$configFile = dirname(__FILE__, 3) . "/Config.ini";

$SceneTableSQL =
    "CREATE TABLE `scene` (" .
    " `id` int(11) NOT NULL AUTO_INCREMENT," .
    " `title` varchar(128) NOT NULL," .
    " `ctime` datetime NOT NULL," .
    " `utime` datetime DEFAULT NULL," .
    " `content` longtext DEFAULT NULL," .
    " `lng` double NOT NULL," .
    " `lat` double NOT NULL," .
    " `address` varchar(512) NOT NULL," .
    " `city` varchar(128) NOT NULL," .
    " `chearup` double DEFAULT 0," .
    " `state` tinyint(4) DEFAULT NULL," .
    " `laststate` tinyint(4) DEFAULT NULL," .
    " `author` varchar(45) NOT NULL," .
    " `surface` varchar(128) DEFAULT NULL," .
    " `viewers` double DEFAULT 0," .
    " `authorid` varchar(256) NOT NULL," .
    "PRIMARY KEY (`id`)" .
    ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

$NormalSceneView = "CREATE VIEW normalscene AS" .
    " SELECT * FROM scene WHERE state =0";
$DraftSceneView = "CREATE VIEW draftscene AS" .
    " SELECT * FROM scene WHERE state =1";
$WasteSceneView = "CREATE VIEW wastescene AS" .
    " SELECT * FROM scene WHERE state =2";

$HotelTableSQL =
    "CREATE TABLE `hotel` (" .
    " `id` int(11) NOT NULL AUTO_INCREMENT," .
    " `title` varchar(128) NOT NULL," .
    " `ctime` datetime NOT NULL," .
    " `utime` datetime DEFAULT NULL," .
    " `content` longtext DEFAULT NULL," .
    " `phone` varchar(128) NOT NULL," .
    " `lng` double NOT NULL," .
    " `lat` double NOT NULL," .
    " `address` varchar(512) NOT NULL," .
    " `city` varchar(128) NOT NULL," .
    " `chearup` double DEFAULT 0," .
    " `state` tinyint(4) DEFAULT NULL," .
    " `laststate` tinyint(4) DEFAULT NULL," .
    " `author` varchar(45) NOT NULL," .
    " `surface` varchar(128) DEFAULT NULL," .
    " `qcode` varchar(128) DEFAULT NULL," .
    " `viewers` DOUBLE DEFAULT 0," .
    " `authorid` varchar(256) NOT NULL," .
    " PRIMARY KEY (`id`)" .
    " ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$NormalHotelView = "CREATE VIEW normalhotel AS" .
    " SELECT * FROM drafthotel WHERE state =0";
$DraftHotelView = "CREATE VIEW normalhotel AS" .
    " SELECT * FROM hotel WHERE state =1";
$WasteHotelView = "CREATE VIEW wastehotel AS" .
    " SELECT * FROM hotel WHERE state =2";

$UserTableSQL =
    "CREATE TABLE user(" .
    " id  int(11) NOT NULL AUTO_INCREMENT," .
    " user  VARCHAR(45) DEFAULT NULL," .
    " psd  VARCHAR(256) DEFAULT NULL," .
    " ctime  DATETIME NOT NULL," .
    " auth VARCHAR(256) DEFAULT NULL," .
    " uuid  VARCHAR(256) DEFAULT NULL," .
    " truename VARCHAR(256)," .
    "PRIMARY KEY( id )" .
    ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
$VisitorTableSQL =
    "CREATE TABLE visitor(" .
    " id  DOUBLE NOT NULL AUTO_INCREMENT," .
    " ip  VARCHAR(256) DEFAULT NULL," .
    " ctime  DATETIME DEFAULT NULL," .
    " isblock tinyint(4) DEFAULT 0," .
    " agent VARCHAR(1024) DEFAULT NULL," .
    " sid DOUBLE DEFAULT NULL," .
    " type VARCHAR(128) NOT NULL," .
    "PRIMARY KEY( id )" .
    ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
$BlockIpView = "CREATE VIEW blockip AS" .
    "  SELECT * FROM visitor WHERE isblock = 1";
$LogTableSQL =
    "CREATE TABLE `log` (" .
    "`id` int(11) NOT NULL AUTO_INCREMENT," .
    "`user` VARCHAR(256) NOT NULL," .
    "`ctime` DATETIME NOT NULL," .
    "`truename` VARCHAR(256) DEFAULT NULL," .
    "`ip` VARCHAR(256) DEFAULT NULL," .
    "`uuid` VARCHAR(128))" .
    "PRIMARY KEY (`id`)" .
    ")ENGINE=InnoDB DEFAULT CHARSET=utf8";

if (($_SERVER['REQUEST_METHOD']) == 'GET') {
    die();
}
$action = $_POST['action'];

switch ($action) {
    //测试数据库
    case "test":
        $data = $_POST["data"];
        $db = $data['database'];
        $port = $data['port'];
        $host = $data['host'];
        $user = $data['user'];
        $psd = $data['psd'];
        $dbname = $data['dbname'];
        $dsn = "$db:host=$host;dbname=$dbname;port=$port";
        try {
            $connect = new PDO($dsn, $user, $psd);
            $respond = array(
                'status' => true,
            );
            echo json_encode($respond);
        } catch (Exception $ex) {
            $respond = array(
                'status' => false,
            );
            echo json_encode($respond);
        }
        break;
    case "save":
        //保存配置信息
        $configClass = new ConfigManagment($configFile);

        $configClass->Write($_POST["data"]);
        $respond = array(
            'status' => true,
        );
        echo json_encode($respond);
        break;
    case "create":
        //先保存然后创建数据库
        //这个到前台实现
        $configClass = new ConfigManagment($configFile);
        $configData = $configClass->Parse();
        $database = $configData['database'];
        $host = $configData['host'];
        $port = $configData['port'];
        $dbName = $configData['dbname'];
        $psd = $configData['psd'];
        $user = $configData['user'];

        $tableName = $_POST['tablename'];
        $dsn = "$database:host=$host;dbname=$dbName;port=$port";
        try {
            $pdo = new PDO($dsn, $user, $psd);
        } catch (Exception $ex) {
            $respond = array("status" => false);
            echo json_encode($respond);
            die();
        }
        switch ($tableName) {
            case "log":
                $status = $pdo->query($LogTableSQL);
                if (!$status) {
                    $respond = array("status" => false);
                } else {
                    $respond = array("status" => true);
                }
                echo json_encode($respond);
                die();
            case "scene":
                //               $pdo->exec("USE $dbName");
                $status = $pdo->query($SceneTableSQL);
                $pdo->query($NormalSceneView);
                $pdo->query($DraftSceneView);
                $pdo->query($WasteSceneView);
                if (!$status) {
                    $respond = array("status" => false);
                } else {
                    $respond = array("status" => true);
                }
                echo json_encode($respond);
                die();
                break;
            case "hotel":
                //             $pdo->exec("USE $dbName");
                $status = $pdo->query($HotelTableSQL);
                $pdo->query($NormalHotelView);
                $pdo->query($WasteHotelView);
                $pdo->query($DraftHotelView);
                if (!$status) {
                    $respond = array("status" => false);
                } else {
                    $respond = array("status" => true);
                }
                echo json_encode($respond);
                die();
                break;
            case "user":
                $status = $pdo->query($UserTableSQL);
                if (!$status) {
                    $respond = array("status" => false);
                } else {
                    $respond = array("status" => true);
                }
                echo json_encode($respond);
                die();
                break;
            case "visitor":
                $status = $pdo->query($VisitorTableSQL);
                $pdo->query($BlockIpView);
                if (!$status) {
                    $respond = array("status" => false);
                } else {
                    $respond = array("status" => true);
                }
                echo json_encode($respond);
                die();
                break;
        }
        break;
    default:
        die();
}
?>