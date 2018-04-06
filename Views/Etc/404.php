<?php
header("Content-Type:text/html;charset=utf-8");
include_once dirname(__FILE__, 2) . "/Partial/Header.php";
?>
<body>
<header></header>
<main class="notfound">
    <div class='image-wrapper'>
        <img src= <?php echo BaseUrl . "/Assert/image/error.jpg" ?>>
    </div>
    <div class='content'>
        <h2>404</h2>
        <p>抱歉，您要查找的内容暂时找不到了</p>
        <a class='ui primary button' href='/'>返回首页</a>
    </div>
</main>
</body>
</html>