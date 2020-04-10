$(function(){
    // 删除商品类型
    single_action(".J_del", "您确定要删除这个商品类型吗？", "/AcpItemType/ajax_del_type", "");

    // 批量删除商品类型
    batch_action(".J_batch_del", "您确定要删除选中的商品类型吗？", "请选择需要删除的商品类型！", "/AcpItemType/ajax_batch_del_type", "");
})
