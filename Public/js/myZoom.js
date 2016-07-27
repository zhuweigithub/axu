var fz = $(window).width()/640*16;
$("html").css("fontSize",fz);
$(window).resize(function(){
   	var fz = $(window).width()/640*16;
	$("html").css("fontSize",fz);
});
window.addEventListener('load', function(){
	new FastClick(document.body);
}, false);

var Namespace = new Object();
Namespace.register = function(path) {
	var arr = path.split(".");
	var ns = "";
	for ( var i = 0; i < arr.length; i++) {
		if (i > 0)
			ns += ".";
		ns += arr[i];
		eval("if(typeof(" + ns + ") == 'undefined') " + ns + " = new Object();");
	}
}

Namespace.register("AiXiu.Zoom");
AiXiu.Zoom.myZoom = {
	key_www:"Af75L!gu9HywJn*T",
	src:1,
	reqJson:function(){
		var jsonObj = new Object();	
		var src = 1;
		var key_www = "Af75L!gu9HywJn*T";
		var timestamp = $("#timestamp").text();
		var md5 = hex_md5(src+timestamp+key_www);		
		jsonObj.src=src;
		jsonObj.timestamp=timestamp;
		jsonObj.md5=md5;
		return jsonObj;
	},
	init:function(){
		var nowPage = 1;
		Teaman.Tag.sharedTag.getTagDetails(6,nowPage);
		/**************************数据动态加载——开始*************************/   	
		   	var winH = $(window).height(); //页面可视区域高度   	  
		    var scrollHandler = function(){  
		        var pageH = $(document.body).height();  
		        var scrollT = $(window).scrollTop(); //滚动条top   
		        var aa = (pageH - winH - scrollT)/winH;  
		        if(aa < 0.1){//0.1是个参数  
		        	Teaman.Tag.sharedTag.getTagDetails(6,(++nowPage));
		        }       
		    }  
		    //定义鼠标滚动事件  
		    $(window).scroll(scrollHandler); 
		   	
		/**************************数据动态加载——结束*************************/  
		
	},
	getTagDetails:function(num,page){
		var tagId = $("#tagId").val();
		if(tagId!="null"){
			var msg = new Object();	
			msg.tagId=parseInt(tagId);
			msg.num=num;
			msg.page=page;	
			var jsonObj = Teaman.Tag.sharedTag.reqJson();
			jsonObj.msg=msg;
			var reqJson = JSON.stringify(jsonObj);			
			$.ajax({
				type:'post',
				url:'/TeamanSrv/rest/article/qrybytag',
				contentType:'application/json',
				data:reqJson,
				dataType:"json",
				success:function(data){
					if(data.retn==0){
						var info = data.msg.info;
						var tagsBoxHeadHtml = '<p class="tagBar"><a class="tagName" tagId="'+info.tagId
						+'">标签：<strong>'+info.tagName+'</strong></a><a class="tagJoins">'+info.participant+'人参与</a></p>'
						+'<div class="tagIntr">'+info.intro+'</div>';
						$(".tagsBoxHead").html(tagsBoxHeadHtml);
						
						var rows=data.msg.rows;
						for(var i=0;i<rows.length;i++){
							if(rows[i].type==1){//茶语
								var contentLiHtml = '<li class="contentsLi"><div class="userInfo"><img class="userHeadImg" src="'
								+rows[i].avatar+'" /><p class="userName">'+rows[i].nickname+'</p><p class="publishTime">'+rows[i].time
								+'</p></div><div class="articleBody"><div class="articleContent">'+rows[i].summary+'<br/>';
								if(rows[i].imgNum>0){
									var images = rows[i].image;
									for(var n=0;n<images.length;n++){
										contentLiHtml=contentLiHtml+'<img src="'+images[n]+'" />';
									}
								}
								contentLiHtml=contentLiHtml+'</div>';
								//拼接标签字符串
								if(rows[i].tagList!=null&&rows[i].tagList!=""){
									var tagList = rows[i].tagList;
									if(tagList.length>0){
										contentLiHtml=contentLiHtml+'<div class="ArtLabels">';
										for(var n=0;n<tagList.length;n++){
											contentLiHtml=contentLiHtml+'<a href="sharedTag.jsp?tagId='+tagList[n].tagId+'" class="artLab">'+tagList[n].tagName+'</a>';
										}
										contentLiHtml=contentLiHtml+'</div>';
									}
								}	
								contentLiHtml=contentLiHtml+'</div></li>';	
							}
							
							if(rows[i].type==2){//文章
								var contentLiHtml = '<li class="contentsLi"><div class="userInfo"><img class="userHeadImg" src="'
								+rows[i].avatar+'" /><p class="userName">'+rows[i].nickname+'</p><p class="publishTime">'+rows[i].time
								+'</p></div><div class="articleBody"><h4 class="articleTitle">'+rows[i].title
								+'</h4><div class="articleContent">'+rows[i].summary+'</div>';
								contentLiHtml=contentLiHtml+'<a class="readAll" href="sharedArticle.jsp?articleId='+rows[i].articleId+'">阅读全文</a>';
								//拼接标签字符串
								if(rows[i].tagList!=null&&rows[i].tagList!=""){
									var tagList = rows[i].tagList;
									if(tagList.length>0){
										contentLiHtml=contentLiHtml+'<div class="ArtLabels">';
										for(var n=0;n<tagList.length;n++){
											contentLiHtml=contentLiHtml+'<a href="sharedTag.jsp?tagId='+tagList[n].tagId+'" class="artLab">'+tagList[n].tagName+'</a>';
										}
										contentLiHtml=contentLiHtml+'</div>';
									}
								}	
								contentLiHtml=contentLiHtml+'</div></li>';	
							}
							
							if(rows[i].type==3){//视频
								var contentLiHtml = '<li class="contentsLi"><div class="userInfo"><img class="userHeadImg" src="'
								+rows[i].avatar+'" /><p class="userName">'+rows[i].nickname+'</p><p class="publishTime">'+rows[i].time
								+'</p></div><div class="articleBody"><h4 class="articleTitle">'+rows[i].title
								+'</h4><div class="articleContent">';
								if(rows[i].videoUrl!=null&&rows[i].videoUrl!=""){
									contentLiHtml=contentLiHtml+'<video class="artVideo" preload="none" width="98%" height="200" poster="'+rows[i].thumbnail
									+'" controls="controls"><source src="'+rows[i].videoUrl+'" type="video/mp4"/>对不起，视频加载异常！</video><br/>';
								}		
								contentLiHtml=contentLiHtml+rows[i].summary+'</div>';
								contentLiHtml=contentLiHtml+'<a class="readAll" href="sharedArticle.jsp?articleId='+rows[i].articleId+'">阅读全文</a>';
								//拼接标签字符串
								if(rows[i].tagList!=null&&rows[i].tagList!=""){
									var tagList = rows[i].tagList;
									if(tagList.length>0){
										contentLiHtml=contentLiHtml+'<div class="ArtLabels">';
										for(var n=0;n<tagList.length;n++){
											contentLiHtml=contentLiHtml+'<a href="sharedTag.jsp?tagId='+tagList[n].tagId+'" class="artLab">'+tagList[n].tagName+'</a>';
										}
										contentLiHtml=contentLiHtml+'</div>';
									}
								}	
								contentLiHtml=contentLiHtml+'</div></li>';	
							}
							
							$(".contentsUl").append(contentLiHtml);
						}	
							
					}else{
						console.log(data.desc+"  "+data.retn);
					}	
				}
			});		
		}	
	},
	
	closeDL:function(obj){
		$(obj).parent().remove();
	},
	
	isWeiXin:function(){
	    var ua = window.navigator.userAgent.toLowerCase();
	    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
	        return true;
	    }else{
	        return false;
	    }
	},
	/**
	 * [isMobile 判断平台]
	 * @param test: 0:iPhone  1:Android
	*/
	ismobile:function(){
		var rs=0;
	    var ua = navigator.userAgent.toLowerCase();	
		if (/iphone|ipad|ipod/.test(ua)){
		    rs=0;		
		}else if(/android/.test(ua)){
		    rs=1;	
		}
		return rs;
	}
	
}
window.onload = function(){
	$("#loadingBox").hide().next().show();
    $(".teaWordsImgLi").css("height",$(".teaWordsImgLi").css("width"));
	Teaman.Tag.sharedTag.init(); 
	
	//APP下载js
	$(".mask").click(function(){
		$(".Twindow").hide();
		$(".mask").hide();
	});
	if(Teaman.Tag.sharedTag.ismobile()){	
		//安卓		
		$(".downLoadBtn").attr("href","http://www.teamanworld.com/chaqin_2.0.2.apk");
		$(".xz").attr("href","http://www.teamanworld.com/chaqin_2.0.2.apk");
	}else{
		//iPhone手机
		$(".downLoadBtn").attr("href","https://itunes.apple.com/cn/app/cha-qin-cha-ren-she-qu-nin/id1076216421?mt=8");
		$(".xz").attr("href","https://itunes.apple.com/cn/app/cha-qin-cha-ren-she-qu-nin/id1076216421?mt=8");
	}
	    
    $(".xz").click(function(){
    	if(Teaman.Tag.sharedTag.isWeiXin()){
			$(".mask").show();
	    	$(".Twindow").show();
	    	event.preventDefault();
		}
    	event.stopPropagation();
    });
    
    $(".downLoadBtn").click(function(){
    	if(Teaman.Tag.sharedTag.isWeiXin()){
			$(".mask").show();
	    	$(".Twindow").show();
	    	event.preventDefault();
		}
    	event.stopPropagation();
    });	
}