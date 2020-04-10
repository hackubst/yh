/* 
 * 管理员对代销商申请的处理
 * author: zhoutao@360shop.cc  zhoutao0928@sina.com
 * date: 2014-03-12
 */


//同意代销商的申请
$('.accept').click(function(){
        var i = $(this).parent().parent().find('input[type="hidden"]').val();
        var u = $(this).parent().parent().find('input[type="checkbox"]').val();
        $.post('/AcpUserAjax/doAgentApply',{ i:i,u:u,data:1},function(data){
                 var data = eval("("+data+")");
                // popMessage(data.message);
                 if(data.type === 1)
                 {
                     $.jPops.message({  
                        title:"提示",  
                        content:data.message,  
                        timing:1000,  
                        callback:function(){    
                            location.reload();
                        }  
                      });
                 }else{
                     $.jPops.message({  
                        title:"提示",  
                        content:data.message,  
                        timing:1000,  
                        callback:function(){ 
                            return false;  
                        }  
                      });
                 }
        });
    });
    
    
    //拒绝代销商的申请
    $('.refuse').click(function(){
        var i = $(this).parent().parent().find('input[type="hidden"]').val();
        var u = $(this).parent().parent().find('input[type="checkbox"]').val();
        $.jPops.custom({  
            title:"可以填写您的理由",  
            content:"<textarea id='refuse_reason' cols='80' rows='10'></textarea>",  
            okBtnTxt:"确定",  
            cancelBtnTxt:"取消",  
            callback:function(r){  
                if(r){  
                    var reason = $('#refuse_reason').val();
                    $.post('/AcpUserAjax/doAgentApply',{ i:i,u:u,reason:reason},function(data){
                         var data = eval("("+data+")");
                        if(data.type === 1)
                        {
                            $.jPops.message({  
                                title:"提示",  
                                content:data.message,  
                                timing:1000,  
                                callback:function(){    
                                    location.reload();
                                }  
                            });
                        }else{
                            $.jPops.message({  
                                title:"提示",  
                                content:data.message,  
                                timing:1000,  
                                callback:function(){    
                                    return false;
                                }  
                            });
                        }
                    });  
                }  
                else{  
                // console.log("我是自定义html的回调,false");  
                }  
            return true;  
            }  
        });
        
    });
