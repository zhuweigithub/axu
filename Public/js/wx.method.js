sss();
function sss(){
	var urlStr  = "/index.php/Home/WxJssdk/getSignPackage";
	var url = encodeURIComponent(location.href.split('#')[0]);
	var back_url = window.location.search.replace("?back=","");
	$.post(urlStr,{url : url,back_url:back_url},function(data){
		data = eval('('+ data +')');
		data.jsApiList = [
			'checkJsApi',
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			'onMenuShareQQ',
			'onMenuShareWeibo',
			'onMenuShareQZone',
			'hideMenuItems',
			'showMenuItems',
			'hideAllNonBaseMenuItem',
			'showAllNonBaseMenuItem',
			'translateVoice',
			'startRecord',
			'stopRecord',
			'onVoiceRecordEnd',
			'playVoice',
			'onVoicePlayEnd',
			'pauseVoice',
			'stopVoice',
			'uploadVoice',
			'downloadVoice',
			'chooseImage',
			'previewImage',
			'uploadImage',
			'downloadImage',
			'getNetworkType',
			'openLocation',
			'getLocation',
			'hideOptionMenu',
			'showOptionMenu',
			'closeWindow',
			'scanQRCode',
			'chooseWXPay',
			'openProductSpecificView',
			'addCard',
			'chooseCard',
			'openCard'
		];
		data.debug = false;
		wx.config(data);
	});
}
$(".uploadImg").on("click",function(){
	wx.ready(function() {
		wx.chooseImage({
			count: 1, // 默认9
			sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
			sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
			success: function (res) {
				var localId = res.localIds[0]; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
				if (localId.indexOf("wxlocalresource") != -1) {
					localId = localId.replace("wxlocalresource", "wxLocalResource");
				}
				wx.uploadImage({
					localId: localId, // 需要上传的图片的本地ID，由chooseImage接口获得
					isShowProgressTips: 1, // 默认为1，显示进度提示
					success: function (res) {
						var serverId = res.serverId; // 返回图片的服务器端ID
						alert(serverId);
					/*	wx.downloadImage({
							serverId: serverId, // 需要下载的图片的服务器端ID，由uploadImage接口获得
							isShowProgressTips: 1, // 默认为1，显示进度提示
							success: function (res) {
								var localId = res.localId; // 返回图片下载后的本地ID
								alert( res.localId);
							}
						});*/
						$.get('/Zoom/downLoadPic/'+serverId,{} , function (data) {
						 alert(data);
						 var data = typeof(data) == 'object'?data:JSON.parse(data);
						 if (data.errorCode != 0) {
						 alert(data.errorMessage.msg);
						 return false;
						 }else{
						 alert(data.errorMessage.image);
						 //$(imgObj).attr("src",data.errorMessage.image);
						 }

						 });

					}
				});
			}
		});

	});
});
