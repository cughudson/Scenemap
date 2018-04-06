<?php
header("Content-Type:text/html;charset=utf-8");
$htmlTitle = "登陆";
include_once dirname(__FILE__, 1) . "/Views/Partial/Header.php";
?>
<body class="login">
<iframe width='100%' height='100%' src=<?php echo './Views/Partial/loginframework.php' ?>></iframe>
</body>
</html>