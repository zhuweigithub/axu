<!DOCTYPE html>
<html>
<head>
<title>我发布的内容</title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0">
<meta name="apple-touch-fullscreen" content="yes">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache"> 
<meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<meta HTTP-EQUIV="expires" CONTENT="0"> 
<meta content="yes" name="apple-mobile-web-app-capable">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="stylesheet" href="font/iconfont.css" />
<link rel="stylesheet" href="css/fontStyle.css" />
<style>
.mySecretsTop{
	height: 50px;
	border-bottom:1px solid #EBEBEB;
	border-top:1px solid #EBEBEB;
	font-size: 14px;
	line-height: 50px;
	padding: 0 8px;
	color: #222;
	background: #f8f8f8;
}
.mySecretsTop font{
	color: red;
	font-size: 20px;
	padding: 0 8px 0 0;
}
.myFootPrintUl{
	background: #EBEBEB;
}
.myFootPrintLi{
	background: #fff;
	margin-bottom: 20px;
}
.myFootPrintLiTop{
	font-size: 16px;
	padding: 5px 8px;
}
.friendName{
	float: right;
}
.footPrintInfo{
	position: relative;
	padding: 5px 8px;
	background: #f8f8f8;
}
.footPrintInfo .secretImg{
	width: 50px;
	height: 50px;
	
}
.secretName{
	max-width: 150px;
	max-height: 32px;
	position: absolute;
	top: 14px;
	left: 70px;
	font-size: 12px;
}
.state0,.state1{
	max-width: 80px;
	color: green;
	float: right;
	margin-top: 14px;
	font-size: 12px;
}
.state1{
	color: #888;
}
.payTime{
	text-align: right;
	font-size: 11px;
	color: #777;
	padding-right: 8px;
}
.toukui{
	color: #888;
	font-size: 14px;
}

.moreTip{
	text-align: center;
	font-size: 14px;
	height: 50px;
	color: #666;
}



</style>
</head>
<body>
<?php include 'myZoomHead.php';?>
<p class="mySecretsTop"><i class="icon iconfont"></i>wo偷看过的：<font>16</font>篇</p>
<ul class="myFootPrintUl">
	<li class="myFootPrintLi">
		<div class="myFootPrintLiTop">			
			<a class="toukui">偷窥了</a>
			<a class="friendName" href="friendsZoom.php?userId=128"><i class="icon iconfont"></i>风筝孩子王（<font>wx43256519</font>）</a>			
		</div>
		<div class="footPrintInfo">
			<a href="publishedSecretImg.php"><img class="secretImg" src="img/riji.jpg"></a>
			<a href="publishedSecretImg.php" class="secretName">我的日记，你想看吗？嘻嘻嘻嘻嘻嘻嘻嘻嘻嘻嘻嘻</a>
			<p class="state0">已偷窥！</p>
		</div>
		<p class="payTime">2016-07-05 22:10:56</p>
	</li>
	<li class="myFootPrintLi">
		<div class="myFootPrintLiTop">			
			<a class="toukui">观望了...</a>
			<a class="friendName" href="friendsZoom.php?userId=128"><i class="icon iconfont"></i>风筝孩子王（<font>wx43256519</font>）</a>			
		</div>
		<div class="footPrintInfo">
			<a href="sharedSecretImg.php"><img class="secretImg" src="img/riji.jpg"></a>
			<a href="sharedSecretImg.php" class="secretName">我的日记，你想看吗？嘻嘻嘻嘻嘻嘻嘻嘻嘻嘻嘻嘻</a>
			<p class="state1">待偷窥...</p>
		</div>
		<p class="payTime">2016-07-05 22:10:56</p>
	</li>
	
</ul>

<p class="moreTip">更多内容</p>

</body>
<script>

	
</script>


</html>
