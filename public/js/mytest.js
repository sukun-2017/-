$(function(){

    /*表单处理*/
   $('.ajax-submit').on('click',function(){
       var action_url = $('.ajax-form').attr('action');
       $('.ajax-form').ajaxSubmit({
           url:action_url,
           dataType:'JSON',
           type:'post',
           success:function(data){
               if(data.status > 0){
                   alert(data.message);
                   location.href = data.url;
               }else{
                   alert(data.message);
                   location.href = data.url;
               }
           }
       })
   })

    var flag = true;  /*设置flag，确保第二次点击input框时不会出现乱码*/
    /*快速修改*/
    $('.quick_modify').click(function(){
        var html_old_text = $(this).html();
        var action_url = $(this).attr('action_url');
        var filedName = $(this).attr('name');
        var id = $(this).attr('getId');
        if(flag){
            flag = false;
            var input = "<input calss='input_quick_modify' type='text' value='"+html_old_text+"'>";
            $(this).html(input);
            $(this).children('input').focus();
            $(this).children('input').blur(function(){
                var html_new_text = $(this).val();
                if(html_new_text != html_old_text){
                    if(confirm('您确定要修改此内容吗？')){
                        $.ajax({
                            url:action_url,
                            dataType:'JSON',
                            type:'post',
                            data:{id:id,filedName:filedName,text:html_new_text},
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
                    }else{
                        location.reload();
                    }
                }else{
                    location.reload();
                }
            })
        }
    }),

        /*快速修改下拉列表信息*/
        $('.quick_modify_option').click(function(){
            var html_old_text = $(this).attr('data-value');
            var action_url = $(this).attr('action_url');
            var filedName = $(this).attr('name');
            var id = $(this).attr('getId');
            if(flag){
                flag = false;
                if(html_old_text == 0){
                    var option = "<select><option value='0' selected>高级母婴护理师证</option><option value='1'>小儿推拿</option><option value='2'>营养师</option><option value='3'>产后康复师</option></select>";
                }else if(html_old_text == 1){
                    var option = "<select><option value='0'>高级母婴护理师证</option><option value='1' selected>小儿推拿</option><option value='2'>营养师</option><option value='3'>产后康复师</option></select>";
                }else if(html_old_text == 2){
                    var option = "<select><option value='0'>高级母婴护理师证</option><option value='1'>小儿推拿</option><option value='2' selected>营养师</option><option value='3'>产后康复师</option></select>";
                }else if(html_old_text == 3){
                    var option = "<select><option value='0'>高级母婴护理师证</option><option value='1'>小儿推拿</option><option value='2'>营养师</option><option value='3' selected>产后康复师</option></select>";
                }
                $(this).html(option);
                $(this).children('select').focus();
                $(this).children('select').blur(function(){
                    var html_new_text = $(this).val();
                    if(html_new_text != html_old_text){
                        if(confirm("您确定要修改证书吗？")){
                            $.ajax({
                                url:action_url,
                                dataType:'JSON',
                                type:'post',
                                data:{id:id,filedName:filedName,text:html_new_text},
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
                        }else{
                            location.reload();
                        }
                    }else{
                        location.reload();
                    }
                })
            }
        })

    /*单击全选，再单击取消全选（订单页面的复选框）*/
    $('.js-check-all').click(function(){
        if($(this).prop('checked') == true){
            $('.js-check').prop('checked',true);
        }else{
            $('.js-check').prop('checked',false);
        }
    })

    /*点击删除全部（订单页面）*/
    $('.delete_all').click(function(){
        var action_url = $(this).attr('action');
        var flag = confirm('您确定要删除全部吗？');
        var id = '';
        if(flag){
            $('.js-check').each(function(){
                if($(this).is(':checked')){
                    id += ','+$(this).val();
                }
//                var checked = $(this).prop('checked');
//                if(checked){
//                    id += ','+$(this).val();
//                }
            })
        }
        $.ajax({
            url:action_url,
            dataType:'JSON',
            type:'post',
            data:{id:id},
            success:function(data){
                if(data.status > 0){
                    alert(data.message);
                    location.reload();
                }else{
                    alert(data.message);
                    location.reload();
                }
            }
        })
    })

})
