<!DOCTYPE html>
<html>
<head>
<title>我的爱秀空间</title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0">
<meta name="apple-touch-fullscreen" content="yes">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache"> 
<meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<meta HTTP-EQUIV="expires" CONTENT="0"> 
<meta content="yes" name="apple-mobile-web-app-capable">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<style>
*{
	padding: 0;
	margin: 0; 
	font-family: "PingFang SC",Helvetica,Arial,"微软雅黑","黑体";
	-webkit-box-sizing: border-box;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
    -webkit-font-smoothing: antialiased;
    text-shadow: none;
    -webkit-tap-highlight-color: rgba(0,0,0,0);
}
a{text-decoration: none; color: #222;}
img{border: 0;}
body{
	padding: 5px;
}
@font-face {font-family: 'iconfont';
    src: url('iconfont.eot'); /* IE9*/
    src: url('iconfont.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
    url('iconfont.woff') format('woff'), /* chrome、firefox */
    url('iconfont.ttf') format('truetype'), /* chrome、firefox、opera、Safari, Android, iOS 4.2+*/
    url('iconfont.svg#iconfont') format('svg'); /* iOS 4.1- */
}
.iconfont{
    font-family:"iconfont" !important;
    font-size:16px;font-style:normal;
    -webkit-font-smoothing: antialiased;
    -webkit-text-stroke-width: 0.2px;
    -moz-osx-font-smoothing: grayscale;
}
header{
	background: #e4e4e4;
}
.zoomHeadDiv{
	height: 100px;
	position: relative;
}
.headImg{
	position: absolute;
	width: 70px;
	height: 70px;
	display: block;
	border: 1px solid #ccc;
	top: 18px;
	left: 5px;
	
}
.niceName{
	max-width: 8em;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
	position: absolute;
	left: 82px;
	top: 28px;
	font-size: 14px;
}
.motto{
	max-width: 12em;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;

	position: absolute;
	left: 82px;
	top: 60px;
	font-size: 12px;
}
.signIn{
	width: 50px;
	border: 1px solid #666;
	bottom: 10px;
	right: 5px;
	text-align: center;
	line-height: 20px;
	font-size: 14px;
	position: absolute;
	padding: 2px 8px;
	border-radius: 3px;
	background: #0088BB;
	color: #fff;
}
.jifen{
	position: absolute;
	right: 10px;
	top: 15px;
	font-size: 12px;
}
.jifen font{
	font-weight: bold;
}

</style>
</head>
<body>
<header>
	<div class="zoomHeadDiv">
		<a href="myZoom.php?userId=12"><img class="headImg" src="img/20151102181644_QMNxw.thumb.224_0.jpeg"/></a>
		<p class="niceName">风筝孩子王风筝孩子王风筝孩子王</p>
		<p class="motto">欢迎来到爱秀网，嘻嘻嘻~</p>
		<p class="signIn">签到</p>
		<p class="jifen"><font>积分：</font>168分</p>
	</div>
</header>
	
	


</body>
<script type="text/javascript" src="js/jquery-1.8.2.min.js" ></script>
<script type="text/javascript" src="js/fastclick.js" ></script>
<!--<script type="text/javascript" src="js/myZoom.js" ></script>-->
<script>
var fz = $(window).width()/640*16;
$("html").css("fontSize",fz);
$(window).resize(function(){
   	var fz = $(window).width()/640*16;
	$("html").css("fontSize",fz);
});
window.addEventListener('load', function(){
	new FastClick(document.body);
}, false);

	
</script>


</html>
