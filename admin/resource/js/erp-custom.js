jQuery.fn.select2CheckDuplicate = function(){
    $(this).on('change',function(){
        let isValidateSelection = true;
        const stockType = "<?=$type?>";
        const currentVal = $(this).val();
        const currentObj = $(this);
        const quantityInputObj = $(this).parents('.item-box').find('.quantity-input').eq(0);
        const beCheckedObj =  $(this).parents('.item-box').siblings().find('.select3');
        const maxQuantity = parseInt(currentObj.children('option:selected').attr('data-quantity'));
        const errorMsgObj = currentObj.parents('.item-box').find('.num-box');
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

function select2FormatState (opt) {
    if (!opt.id) {
        return opt.text;
    }
    var optimage = $(opt.element).data('image');
    if(!optimage){
        return opt.text;
    } else {
        var $opt = $(
            '<span><img class="avatar avatar-30 img-rounded" src="' + optimage + '"/> ' + opt.text + '</span>'
        );
        return $opt;
    }
};

function getQueryVariable(variable) {
    const query = window.location.search.substring(1);
    const vars = query.split("&");
    for (let i=0;i<vars.length;i++) {
        let pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}

//alert
let alertTimeout = null;
function showAlert(message,type=null){
    let $alertItem = $("#adminAlert");
    let $textItem = $alertItem.find("span");
    let $closeBtn = $alertItem.find(".closed");
    clearTimeout(alertTimeout);
    if(type=="error"){
        $alertItem.attr("class","myadmin-alert alert-danger myadmin-alert-top alerttop");
    }else{
        $alertItem.attr("class","myadmin-alert alert-success myadmin-alert-top alerttop");
    }
    $textItem.text(message);
    $alertItem.stop().slideDown(function(){
        alertTimeout = setTimeout(function(){
            $alertItem.stop().slideUp();
        },8000)
    });
    $closeBtn.click(function(){
        $alertItem.stop().slideUp();
    });
}

function hideAlert(){
    $("#adminAlert").stop().slideUp();
}

function setDefaultValue(){
    $("select[data-defvalue]").each(function () {
        var val = $(this).attr("data-defvalue");
        var option = $(this).find("option");
        option.each(function(){
            if($(this).val()==val){
                $(this).prop("selected", "selected");
            }
        })
    });
}

$(document).ready(function () {

    //产品图片
    const productImgObj = $("#product-img-large");
    if(productImgObj.length>0){
        let lastSmallImgIndex = 0;
        let productImgSmallArr = $(".product-img-small");
        productImgSmallArr.eq(0).addClass("selected");
        productImgSmallArr.each(function(index){
            let currentSmallImg = $(this);
            currentSmallImg.hover(
                function(){
                    if(index != lastSmallImgIndex){
                        productImgSmallArr.eq(lastSmallImgIndex).removeClass('selected');
                        currentSmallImg.addClass('selected');
                        productImgObj.attr("src",currentSmallImg.attr("src"));
                        lastSmallImgIndex = index;
                    }
                }
            )
        })

    }

    //select Default value
    setDefaultValue();

    //上传插件
    if($('.dropify').length>0){

        let drEvent = $('.dropify').dropify();
        let $input = $("<input>")

        drEvent.each(function(){
            let name = $(this).attr("data-name");
            $(this).parent().append($(`<input type="hidden" value="0" name="${name}" id="${name}"></input>`))
        })

        drEvent.on('dropify.fileReady', function(event, element){
            let name = $(this).attr("data-name");
            console.log($(this).siblings(`#${name}`).val(1))

        });

        drEvent.on('dropify.beforeClear', function(event, element){
            return confirm("Do you really want to delete the Image ?");
        });

        drEvent.on('dropify.afterClear', function(event, element){
            let name = $(this).attr("data-name");
            console.log($(this).siblings(`#${name}`).val(-1))
        });
    }

    //checkBox
    $("#cBoxAll").change(function () {
        if(this.checked){
            $(".cBox").each(function(){
                $(this).prop("checked",true);
                $("#deleteBtn").show();
            })
        }else{
            $(".cBox").each(function(){
                $(this).prop("checked",false)
                $("#deleteBtn").hide();
            })
        }
    });

    $(".cBox").each(function(){
        $(this).change(function(){
           if($(".cBox:checked").length>0){
               $("#deleteBtn").show();
           }else{
               $("#deleteBtn").hide();
           }
        })
    })

    //修改上传图片
    $('#currentImages').children('div').each(function(i,ele){
        let $btn = $(ele).find('.overlay');
        $btn.click(function(){
            $(ele).remove();
        });
    });


    //高亮搜索
    const searchValue = getQueryVariable('searchValue');
    $("*[data-hl-search]").each(function(){
        const htmlText = $(this).html();
        const exp = new RegExp(searchValue,"gi");
        const newText = htmlText.replace(exp,"<span class='text-danger'>$&</span>");
        $(this).html(newText);
    })

    //高亮排序
    const orderByValue = getQueryVariable('orderBy');
    const sort = getQueryVariable('sort');
    const s = getQueryVariable('sort');
    $(`a[data-hl-orderby]`).each(function(){
        $(this).append("<span></span>")
    })

    $(`a[data-hl-orderby=${orderByValue}]`).each(function(){
        if(sort == 'asc'){
            $(this).find("span").addClass('up')
        }else if(sort == 'desc'){
            $(this).find("span").addClass('down')
        }
    })

    $(".user-search-select-ajax").select2({
        ajax: {
            url: "/restAPI/userController.php?action=searchUser",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchValue: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data.result, function (item) {
                        return {
                            text: `${item.user_first_name} ${item.user_last_name} (${item.user_email})`,
                            id: item.user_id
                        }
                    })
                };
            },
            cache: true
        },
        placeholder: 'Search for a user',
        minimumInputLength: 1
    });


    //inventory select
    const firstItemNode = $("#item-box-template");

    setTimeout(function(){
            const iniData = $(".item-box-ini-data");
            if(iniData.length>0){
                let iniSelectedArr = [];
                let iniQuantityArr = [];
                iniData.each(function(){
                    iniSelectedArr.push($(this).attr('data-item-id'));
                    iniQuantityArr.push($(this).attr('data-item-quantity'));
                })
                copyInventoryBox(firstItemNode,0,iniSelectedArr,iniQuantityArr);
            }else{
                copyInventoryBox(firstItemNode,5);
            }
        },200
    )

    $("#add-item-btn").click(function(){
        copyInventoryBox(firstItemNode,5);
    })

    function copyInventoryBox(copiedNode,quantity,iniSelectedArr=[],iniQuantityArr=[]){
        const inventoryNode = $("#inventory-box");
        const lastItemCount = parseInt($("#inventory-box .item-box:last-child .num").text()) || 0;
        let newNode = null;
        let newNodes = $();
        if(iniSelectedArr.length > 0){
            quantity = iniSelectedArr.length;
        }
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

        if(iniSelectedArr.length > 0){
            for(let i = 0; i<iniSelectedArr.length; i++){
                newNodes.eq(i).val(iniSelectedArr[i]);
                newNodes.eq(i).trigger('change');
                newNodes.eq(i).parents('.item-box').find('.quantity-input').eq(0).attr('disabled',false).val(iniQuantityArr[i]);
            }
        }

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


});
