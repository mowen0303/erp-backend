<?php
try {
    global $userModel;
    $inventoryModel = new \model\InventoryModel();
    $warehouseId = (int) $_GET['warehouseId'] or Helper::throwException("Warehouse Id can not be null");
    $type = $_GET['type'];
    $inventoryModel->verifyInventoryType($type);
    $flag = $type=='in'?'Stock-in':'Stock-out';
    if($type=="in"){
        $userModel->isCurrentUserHasAuthority("INVENTORY","STOCK_IN")
        ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId)
        or Helper::throwException(null,403);
        $flag = 'Stock-in';
    }else{
        $userModel->isCurrentUserHasAuthority("INVENTORY","STOCK_OUT")
        ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId)
        or Helper::throwException(null,403);
        $flag = 'Stock-out';
    }
    $warehouse = $inventoryModel->getWarehouses([$warehouseId],[],false)[0] or Helper::throwException("Warehouse id do NOT exist");
    $itemArr = $inventoryModel->getInventoryWarehouse([0],['warehouseId'=>$warehouseId],false);
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<script>

    jQuery.fn.select2CheckDuplicate = function(){
        $(this).on('change',function(){
            let isValidateSelection = true;
            const stockType = "<?=$type?>";
            const currentVal = $(this).val();
            const currentObj = $(this);
            const quantityInputObj = $(this).parent().siblings().find('.quantity-input').eq(0);
            const beCheckedObj =  $(this).parents('.item-box').siblings().find('.select3');
            const maxQuantity = parseInt(currentObj.children('option:selected').attr('data-quantity'));
            const errorMsgObj = currentObj.parent().siblings().find('.num-box');
            if(stockType == "out"){
                quantityInputObj.prop("max",maxQuantity);
                quantityInputObj.off('keyup');
                quantityInputObj.keyup(function(){
                    if(parseInt($(this).val()) > maxQuantity){
                        errorMsgObj.addClass('error');
                        swal({
                            title: "Have no enough stock",
                            text: "The current stock quantity is only left "+maxQuantity,
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "OK"
                        });
                    } else {
                        errorMsgObj.removeClass('error')
                    }
                })
            }
            beCheckedObj.each(function(){
                if($(this).val() !== ""){
                    if($(this).val() == currentVal){
                        errorMsgObj.addClass('error');
                        swal({
                            title: "Duplicate item",
                            text: "You had selected the item already!",
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "OK"
                        });
                        isValidateSelection = false;
                        return false;
                    }else{
                        isValidateSelection = true;
                        errorMsgObj.removeClass('error')
                    }
                }
            })
            if(currentVal!="") {
                quantityInputObj.prop('disabled',!isValidateSelection);
            }else{
                quantityInputObj.prop('disabled',isValidateSelection);
            }
        })

    };

    $(document).ready(function(){

        //inventory select
        const firstItemNode = $("#item-box-template");

        setTimeout(function(){copyInventoryBox(firstItemNode,5,true);},200)

        $("#add-item-btn").click(function(){
            copyInventoryBox(firstItemNode,5,true);
        })

        function copyInventoryBox(copiedNode,quantity,loadInitSelectedData=false){
            const inventoryNode = $("#inventory-box");
            const lastItemCount = parseInt($("#inventory-box .item-box:last-child .num").text()) || 0;
            let newNode = null;
            let newNodes = $();
            for(let i = lastItemCount+1; i<=lastItemCount+quantity; i++){
                newNode = copiedNode.clone().appendTo(inventoryNode);
                newNode.find('.num').text(i);
                newNode.css({'display':'block'})
                newNodes = newNodes.add(newNode.find(".select3"));
            }
            newNodes.select2({
                templateResult: select2FormatState,
                templateSelection: select2FormatState
            });
            newNodes.select2CheckDuplicate();
        }

        $("#submitBtn").click(function(){
            if($("#inventory-box .error").length>0){
                swal({
                    title: "Inventory error",
                    text: "There are some errors in your inventory form, please fix the errors before submit.",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK"
                });
                return false;
            }else{
                return true;
            }
        })

    })



</script>


<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">INVENTORY / <?=$flag?></h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(3);?>
    </label>
</div>
<!--header end-->

<!--item-box-template start-->
<div id="item-box-template" class="form-group item-box" style="display: none">
    <label class="col-sm-3 control-label"><span class="num-box">#<span class="num">0</span></span></label>
    <div class="col-sm-3">
        <select name="item_id[]" class="form-control select3 has-error">
            <option value="">-- Select --</option>
            <?php
            foreach ($itemArr as $item){
                echo "<option data-quantity='{$item['inventory_warehouse_count']}' value='{$item['item_id']}' data-image='{$item['item_image']}'>{$item['item_sku']} ({$item['inventory_warehouse_count']})</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-sm-2">
        <input type="number" disabled max="" name="item_quantity[]" value="" class="quantity-input form-control" placeholder="0">
    </div>
</div>
<!--item-box-template end-->

<div class="row">
    <div class="col-sm-12">
        <div class="panel <?=$type=="in"?"panel-info":"panel-danger"?>">
            <div class="panel-heading"><?=$flag?></div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/inventoryController.php?action=modifyInventory" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="warehouse_id" value="<?=$warehouse['warehouse_id']?>">
                        <input type="hidden" name="inventory_log_type" value="<?=$type?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Warehouse *</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?=$inventoryModel->getWarehouseFullAddress($warehouse)?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Deliver</label>
                            <div class="col-sm-6">
                                <select name="inventory_log_deliver_id" class="user-search-select-ajax form-control"></select>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Inventory form</label>
                            <div class="col-sm-9">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-3">Item</div>
                            <div class="col-sm-2">Quantity</div>
                        </div>

                        <div id="inventory-box"></div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button id="add-item-btn" type="button" class="btn btn-outline btn-info waves-effect waves-light m-r-10"><i class="fa fa-plus-circle m-r-5"></i> Add 5 more</button>
                                <?=$type=='in'?'<a class="text-info" target="_blank" href="/admin/inventory/index.php?s=inventory-warehouse-item-map-form&warehouseId='.$warehouseId.'">Did NOT find item in the select list? Try add ITEM LOCATION first</a>':''?>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Notes</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="inventory_log_note"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" id="submitBtn" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>