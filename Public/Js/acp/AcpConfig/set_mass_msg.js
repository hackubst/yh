
$(function(){
	if(mass_msg_type != '' && mass_msg_media_id != ''){
		get_wx_material(mass_msg_type);
	}
	var html = '';
	
	$('#msg_type').on('change', function(){
		var msg_type = $(this).val();
		get_wx_material(msg_type);
	});


});



function set_use(msg_type, media_id){
	var group = $('#groups').val();
	if(group == '' || group == null){
		$.jPops.alert({
	        title:"提示",
	        content: '请选择群发对象',
	        okBtnTxt:"确定",
	        callback:function(){
	            return true;
	        }
	    });
	    return false;
	}
	if(msg_type == 'text'){
		media_id = $('#text_value').val();
	}
	$.jPops.confirm({
		title:"提示",
		content:'您确定要执行该操作吗？',
		okBtnTxt:"确定",
		cancelBtnTxt:"取消",
		callback:function(r){
			if(r){
				$.ajax({
					url:'/AcpConfig/set_mass_msg',
					type:'post',
					data:{msg_type:msg_type, media_id:media_id, group:group},
					success:function(data){
						if(data == 'success'){
							$.jPops.alert({
	                            title:"提示",
	                            content: '设置成功',
	                            okBtnTxt:"确定",
	                            callback:function(){
	                                location.reload();
	                                return true;
	                            }
	                        });
						}else{
							$.jPops.alert({
	                            title:"提示",
	                            content: '设置失败',
	                            okBtnTxt:"确定",
	                            callback:function(){
	                              return true;
	                            }
	                        });
						}
					}
				});
			}
			return true;
		}
	});
	
}


function get_wx_material(msg_type){
	switch(msg_type){
			case 'news':
				$.ajax({
					url:'/AcpConfig/get_wx_material',
					type:'post',
					data:{type:msg_type, page:page},
					success:function(r){
						console.log(r);
						news = r.item;
						console.log(news);
						html = '';
						$('#type').nextAll().remove();
						html += '<div class="formitems inline"><div class="tablesWrap "><table class="wxtables"><colgroup><col width="20%"><col width="10%"><col width="35%"><col width="10%"><col width="15%"></colgroup><thead><tr><td>标题</td><td>缩略图</td><td>图文简述</td><td>文章数量</td><td>操作</td></tr></thead><tbody>';
						if(news.length > 0){
							for (var i = 0; i < news.length; i++) {
								html += '<tr><td>';
								for (var j = 0; j < news[i].content.news_item.length; j++) {
									html += '<a href="'+news[i].content.news_item[j].url+'" target="_blank">'+(j+1)+'. '+news[i].content.news_item[j].title+'</a>';
									if(j != (news[i].content.news_item.length - 1)){
										html += '<br>';
									}
								}
								html += '</td><td><img style="width:75px;height:75px;" src="/AcpConfig/get_thumb?url='+news[i].content.news_item[0].thumb_url+'"></td>';
								html += '<td>'+news[i].content.news_item[0].digest+'</td>';
								html += '<td>'+news[i].content.news_item.length+'</td> ';
								if(mass_msg_media_id == news[i].media_id){
									html += '<td><a href="javascript:;" class="btn" title="使用中">使用中</a></td></tr>';
								}else{
									html += '<td><a href="javascript:;" class="btn" onclick="set_use('+"'news','"+news[i].media_id+"'"+')" title="使用">使用</a></td></tr>';
								}
								
	               			}
	               		}else{
	               			html += '<tr><td colspan="11">没有数据</td></tr>';
	               		}
	               		page_num = r.total_count / item_count;
	               		cur_page = page;
	               		cur_page = cur_page <= 0 ? 1 : cur_page;
	               		page_num = page_num <= 1 ? 1 : page_num;
	               		news_url = '/AcpConfig/set_mass_msg/page';
                    	html += '</tbody></table><div class="tables-btmctrl clearfix"><div class="fr"><div class="paginate">';
                    	if(page > 1){
                    		html += '<a href="'+news_url+'/1">第一页</a><a href="'+news_url+'/'+(cur_page - 1)+'">上一页</a>';
                    	}
                    	if(page < page_num){
                    		html += '<a href="'+news_url+'/'+(cur_page + 1)+'">下一页</a><a href="'+news_url+'/'+page_num+'">最后一页</a>';
                    	}
                    	html +='<a>'+r.total_count+' 条记录 '+cur_page+'/'+page_num+' 页</a></div></div></div></div></div>';
						
						$('#type').after(html);
						
					}
				});
				break;
			
			case 'text':
				html = '';
				$('#type').nextAll().remove();
				html += '<div class="formitems inline"><label class="fi-name"><span class="colorRed">*</span>文本内容：</label><div class="form-controls"><textarea name="text_value" id="text_value">';
				if(mass_msg_media_id != '' && mass_msg_type == 'text'){
					html += mass_msg_media_id;
				}
				html+= '</textarea><span class="fi-help-text colorRed">如需换行请输入“\\n”</span></div></div>';
				html += '<div class="formitems inline"><label class="fi-name"></label><div class="form-controls"><input type="hidden" name="act" value="set" /><span onclick="set_use('+"'text','1'"+')" class="btn btn-blue"><i class="gicon-check white"></i>设置</span></div></div>';
				$('#type').after(html);
				break;
			default:
				break;
		}
}



function set_group(){
	var group = $('#groups').val();
	if(group == '' || group == null){
		$.jPops.alert({
	        title:"提示",
	        content: '请选择群发对象',
	        okBtnTxt:"确定",
	        callback:function(){
	          return true;
	        }
	    });
	}
}