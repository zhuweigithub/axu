window.addEventListener('load', function(){
	new FastClick(document.body);
}, false);

function sign(e){
	$.post('/axu/index.php/Home/Zoom/signRest',
		function(data){
			if(data.retn==1){
				var jifen=$('.jifen font').text();
				anp(e,jifen);			
			}else{
				alert("签到失败！");
			}
		},
	"json");
}

//增加积分时的渐隐效果
function anp(e,jifen){
	var n=5;
	var x=e.pageX,y=e.pageY;
	$("body").append('<i class="bubble" style="position: absolute;color: #E94F06; top: '+(y-20)+'px; left:'+x+'px;">+'+n+'</i>');
	$('.jifen font').text(parseInt(jifen)+5);
	$('.bubble').animate({top:y-180,opacity:0,"font-size":"1.4em"},1500,function(){
		$('.bubble').remove();	
	});
	e.stopPropagation();
}