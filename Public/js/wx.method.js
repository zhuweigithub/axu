
wxApiList:[
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
],
getSignPackage();
function getSignPackage(){
	var urlStr  = "/WxJssdk/getSignPackage";
	var params = {};
	params.url = encodeURIComponent(location.href.split('#')[0]);
	params.back_url = window.location.search.replace("?back=","");
	dataObj.back_url = params.back_url;

	$.post(urlStr,params,function(data){
		var signpack = JSON.parse(data);
		signpack.jsApiList = dataObj.wxApiList;
		signpack.debug = false;


		wx.config({
			debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			appId: '', // 必填，企业号的唯一标识，此处填写企业号corpid
			timestamp: , // 必填，生成签名的时间戳
			nonceStr: '', // 必填，生成签名的随机串
			signature: '',// 必填，签名，见附录1
			jsApiList: [] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
		});

	});
}
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
					$.get('/tool/downLoadPic/'+serverId,{} , function (data) {
						var data = typeof(data) == 'object'?data:JSON.parse(data);
						if (data.errorCode != 0) {
							alert(data.errorMessage.msg);
							return false;
						}else{
							// alert(data.errorMessage.image);
							$(imgObj).attr("src",data.errorMessage.image);
						}

					});

				}
			});
		}
	});

});