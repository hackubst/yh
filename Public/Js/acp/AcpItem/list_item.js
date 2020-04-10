$(function(){
    // 删除" + item_name + "
    single_action(".J_del", "您确定要删除这件" + item_name + "吗？", "/AcpItemAjax/single_action", "del_item");

    // " + item_name + "下架
    single_action(".J_store", "您确定要下架这件" + item_name + "吗？", "/AcpItemAjax/single_action", "store_item");

    // " + item_name + "上架
    single_action(".J_display", "您确定要上架这件" + item_name + "吗？", "/AcpItemAjax/single_action", "display_item");

    // 还原已删除" + item_name + "到出售中
    single_action(".J_to_sale", "您确定要还原这件" + item_name + "吗？", "/AcpItemAjax/single_action", "restore_to_sale");

    // 还原已删除" + item_name + "到仓库中
    single_action(".J_to_store", "您确定要还原这件" + item_name + "吗？", "/AcpItemAjax/single_action", "restore_to_store");

    // 彻底删除" + item_name + "
    single_action(".J_deep_del", "您确定要彻底删除这件" + item_name + "吗？", "/AcpItemAjax/single_action", "deep_del_item");

    // 批量下架" + item_name + "
    batch_action(".J_batch_store", "您确定要下架选中的" + item_name + "吗?", "请选择需要下架的" + item_name + "!", "/AcpItemAjax/batch_action", "batch_store_item");

    // 批量上架" + item_name + "
    batch_action(".J_batch_display", "您确定要上架选中的" + item_name + "吗?", "请选择需要上架的" + item_name + "!", "/AcpItemAjax/batch_action", "batch_display_item");

    // 批量删除" + item_name + "
    batch_action(".J_batch_del", "您确定要删除选中的" + item_name + "吗?", "请选择需要删除的" + item_name + "!", "/AcpItemAjax/batch_action", "batch_del_item");

    // 批量彻底删除" + item_name + "
    batch_action(".J_batch_deep_del", "您确定要彻底删除选中的" + item_name + "吗?", "请选择需要彻底删除的" + item_name + "!", "/AcpItemAjax/batch_action", "batch_deep_del");

    // 批量还原删除" + item_name + "到出售中
    batch_action(".J_batch_to_sale", "您确定要还原选中的" + item_name + "吗?", "请选择需要还原的" + item_name + "!", "/AcpItemAjax/batch_action", "batch_to_sale");

    // 批量还原删除" + item_name + "到仓库里
    batch_action(".J_batch_to_store", "您确定要还原选中的" + item_name + "吗?", "请选择需要还原的" + item_name + "!", "/AcpItemAjax/batch_action", "batch_to_store");
});

