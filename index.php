<?php

/**
 * 微博短链接t.cn生成
 *
 * 功能描述：使用微博私信功能生成t.cn短链接。
 * 本项目仅供大家学习参考。
 *
 * 希望在使用时能够保留导航栏的 Made by Yuan_Tuo 感谢！
 *
 * 此项目 GitHub 地址：https://github.com/yuantuo666/WeiBoShortLink
 *
 * @version 1.0.0
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
session_start();
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}

//保存启动时间
$system_start_time = microtime(true);

define('programVersion', '1.0.0');

define('SUB', ''); // 你的 SUB  请通过浏览器查看cookies获取SUB字段

define('UID', '2028810631'); //定义发送者的UID

define('Footer', ''); // 页脚统计代码放置处

function setCurl(&$ch, array $header)
{ // 批处理 curl
	$a = curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略证书
	$b = curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 不检查证书与域名是否匹配（2为检查）
	$c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 以字符串返回结果而非输出
	$d = curl_setopt($ch, CURLOPT_HTTPHEADER, $header); // 请求头
	return ($a && $b && $c && $d);
}
function post(string $url, $data, array $header)
{ // POST 发送数据
	$ch = curl_init($url);
	setCurl($ch, $header);
	curl_setopt($ch, CURLOPT_POST, true); // POST 方法
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // POST 的数据
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function get(string $url, array $header)
{ // GET 请求数据
	$ch = curl_init($url);
	setCurl($ch, $header);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function error(string $title, string $content, bool $jumptip = false)
{
	echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
	<h5 class="alert-heading">' . $title . '</h5><hr /><p class="card-text">' . $content;
	if ($jumptip) {
		echo '<br>请将相关数据提交issue到<a href="https://github.com/yuantuo666/WeiBoShortLink">github项目</a>。';
	}
	echo '</p></div></div></div>';
	return 0;
}

// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');

error_reporting(0); //关闭错误报告

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="referrer" content="same-origin" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="version" content="<?php echo programVersion; ?>" />
	<meta name="description" content="微博短链接生成" />
	<meta name="keywords" content="微博短链接生成" />
	<title>微博短链接生成</title>
	<link rel="icon" href="resource/logo.png" />
	<link rel="stylesheet" href="static/index.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css" />

</head>

<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="./"><img src="resource/logo.png" class="img-fluid rounded logo-img mr-2" alt="LOGO" />ShortLink</a>
			<button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collpase-bar"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="collpase-bar">
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="./">首页</a></li>
					<li class="nav-item"><a class="nav-link" href="https://imwcr.cn/" target="_blank">Made by Yuan_Tuo</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<?php
		if (isset($_GET["help"])) { // 帮助页 
		?>
			<div class="row justify-content-center">
				<div class="col-md-7 col-sm-8 col-11">
					<div class="alert alert-primary" role="alert">
						<h5 class="alert-heading">提示</h5>
						<hr />
						<div class="page-inner">
							<section class="normal" id="section-">
								<ol>
									<li>本项目仅以学习为目的，不得用于其他用途。</li>
									<li>本项目通过微博网页版私信功能生成t.cn短链接。</li>
									<li>当前项目版本：<?php echo programVersion; ?></li>
									<li><a href="https://github.com/yuantuo666/WeiBoShortLink" target="_blank">Github仓库</a></li>
									<li><a href="https://imwcr.cn/" target="_blank">Made by Yuan_Tuo</a></li>
								</ol>
							</section>
						</div>
						</p>
					</div>
				</div>
			</div>
	</div>
<?php } elseif (isset($_POST["url"])) { // 解析页面
			$url = $_POST["url"];
			//开始获取
			$Message = "text=" . urlencode($url) . "&uid=" . UID . "&extensions=%7B%22clientid%22%3A%223dsmt6xbjdfbulag51gjd8ia91npxk%22%7D&is_encoded=0&decodetime=1&source=209678993";
			$Length = strlen($Message);
			$headerArray = array(
				'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36',
				'Cookie: SUB=' . SUB . ';',
				'Referer: https://api.weibo.com/chat/',
				'Host: api.weibo.com',
				'Content-Length: ' . $Length, //被这个参数坑了，没想到居然要这个（postman真好用
				'Content-Type: application/x-www-form-urlencoded'
			);

			$ReturnJson = post("https://api.weibo.com/webim/2/direct_messages/new.json", $Message, $headerArray);
			$ReturnJson = json_decode($ReturnJson, true);
			$isSucceed = $ReturnJson["url_objects"][0]["info"]["result"];
			if ($isSucceed === true) $ShortLink = $ReturnJson["url_objects"][0]["info"]["url_short"];

			$ErrorCode = $ReturnJson["error_code"];
			if ($ErrorCode == 21301) { //SUB过期
				error("获取错误", "当前配置的SUB过期，请检查设置的SUB");
				exit;
			}
			if ($ShortLink == "") {
				error("获取错误", "未知错误", TRUE);
				var_dump($ReturnJson);
				exit;
			}


?>
	<div class="row justify-content-center">
		<div class="col-md-7 col-sm-8 col-11">
			<div class="alert alert-primary" role="alert">
				<h5 class="alert-heading">获取短链接成功</h5>
				<hr />
				<p class="card-text">生成的短链接： <b><a href="<?php echo $ShortLink; ?>" target="_blank"><?php echo $ShortLink; ?></a></b></p>
			</div>
		</div>
	</div>
<?php
			// 成功！
		} else { // 首页 
?>
	<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
		<div class="card">
			<div class="card-header bg-dark text-light">
				<text>微博短链接生成</text> </div>
			<div class="card-body">
				<form name="form1" method="post">
					<div class="form-group my-2"><input type="text" class="form-control" name="url" placeholder="请输入长链接(含http://)"></div>
					<button type="submit" class="mt-4 mb-3 form-control btn btn-success btn-block">生成</button>
				</form>
			</div>
		</div>
	</div>
<?php
		}
		echo Footer; ?>
</div>

<?php
$system_end_time = microtime(true);
$system_runningtime = $system_end_time - $system_start_time;
echo '<script>console.log("后端计算时间：' . $system_runningtime . '秒");</script>';
?>
</body>

</html>