$(function(){
	$('.log_title .control').hover(
		function () {
			$(this).addClass('on').siblings().removeClass('on');
			$('.abcb').eq($(this).index()).addClass('on').siblings().removeClass('on');
		},
		function () {
			
		}
	);

	/*账号登录*/
	$('.acount_login').click(function(){
		var action_url = $('.acount_login_form').attr('action');
		var username = $('.phonenumber').val();
		var password = $('.usercode').val();
		$.ajax({
			url:action_url,
			dataType:'JSON',
			type:'post',
			data:{username:username,password:password},
			success:function(data){
				if(data.status > 0){
					alert(data.message);
					location.href = data.url;
				}else{
                    alert(data.message);
                    return;
				}
			}
		})
	})

});