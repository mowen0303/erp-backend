$.fn.tableRecalculate = function(){
    //console.log(11,$(this).html())
    let amount = 0;
    let subtotalEle = $(this).find(".subtotal");
    let taxEle = $(this).find(".tax");
    let totalEle = $(this).find(".total");
    $(this).find('.amount').each(function(){
        amount += Number($(this).attr('data-amount'));
    })
    subtotalEle.html(amount).priceFormat({prefix: '$',});
    let tax = getTax(amount)
    taxEle.html(tax).priceFormat({prefix: '$',});
    totalEle.html(amount+tax).priceFormat({prefix: '$',});
}

$.fn.trRecalculate = function(){
    let $table = $(this).parents("table");
    let $tr = $(this);
    let price = $tr.find(".price").attr("data-price");
    let $quantity = $tr.find('.quantity');
    let quantity = $tr.find('.quantity').val();

    if(quantity<1){
        quantity = 1;
        $quantity.val(quantity)
    }else if(quantity>1000){
        quantity = 1000;
        $quantity.val(quantity)
    }

    let amountElement = $tr.find(".amount");
    let amount = price*quantity;
    amountElement.attr('data-amount',amount).html(amount).priceFormat({prefix: '$',});

    let url = `/restAPI/orderController.php?action=modifyOrderProduct&dataType=json`;
    let params = new URLSearchParams();
    params.append('order_id',$table.attr('data-order-id'));
    params.append('product_id',$tr.attr('data-product-id'));
    params.append('product_count',quantity);
    params.append('is_accumulate',false);
    axios.post(url,params)
        .then(res=>{
            if(res.data.code==200){
                $table.tableRecalculate();
            }else{
                showAlert(res.data.message,'error');
            }

        })
        .catch(error=>{
            showAlert(error,'error');
        })
}

$(document).ready(function(){

    $('table[data-order-id]').each(function(){
        $(this).tableRecalculate();
    })

    $(".deleteProductBtn").click(function(){
        const deleteProductBtn = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to remove the product?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value == true) {
                let url = `/restAPI/orderController.php?action=deleteOrderProductByIds&dataType=json`;
                let params = new URLSearchParams();
                params.append('orders_id',$(this).attr('data-order-id'));
                params.append('product_id',$(this).attr('data-product-id'));
                axios.post(url,params)
                    .then(res=>{
                        if(res.data.code==200){
                            Swal.fire({
                                position: 'center',
                                type: 'success',
                                title: res.data.message,
                                showConfirmButton: false,
                                timer: 1500
                            })

                            deleteProductBtn.parents("tr").slideUp(function(){
                                let tableElement = $(this).parents("table");
                                $(this).remove();
                                tableElement.tableRecalculate();
                            });
                        }else{
                            Swal.fire({
                                position: 'center',
                                type: 'error',
                                title: 'Error',
                                text:res.data.message,
                                showConfirmButton: true
                            })
                        }
                    })
                    .catch(error=>{

                    })
            }
        })
        return false;
    });

    $(".quantity").on("keyup change",function(){
        $(this).parents("tr").trRecalculate();
    }).click(function(){
        $(this).select();
    });

    $(".deleteCartBtn").click(function(){
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete the quotation?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value == true) {
                let url = `/restAPI/orderController.php?action=deleteOrderByIds&dataType=json`;
                let params = new URLSearchParams();
                params.append('orders_id',$(this).attr('data-order-id'));
                axios.post(url,params)
                    .then(res=>{
                        if(res.data.code==200){
                            Swal.fire({
                                position: 'center',
                                type: 'success',
                                title: res.data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $(this).parents(".panel").slideUp();
                        }else{
                            Swal.fire({
                                position: 'center',
                                type: 'error',
                                title: 'Error',
                                text: res.data.message,
                                showConfirmButton: true
                            })
                        }
                    })
                    .catch(error=>{
                        Swal.fire({
                            position: 'center',
                            type: 'error',
                            title: error,
                            showConfirmButton: true
                        })
                    })
            }
        })
        return false;
    })

    $(".modifyCartBtn").click(function(){

        let cartId = $(this).attr('data-order-id');
        let cartTitle = $(this).parents('.panel').find(".cartTitle");

        let html =`
            <div class="modal-box p-10">
                <h3 class="modal-title">Edit quotation title</h3>
                <div class="form-group m-t-20">
                    <input id="input" type="text" value="" class="form-control" placeholder="">
                </div>
                <div>
                    <button id="cancelBtn" type="button" class="btn btn-danger"><div class="lds-dual-ring loadingIcon"></div>Cancel</button>
                    <button id="doneBtn" type="button" class="btn btn-info pull-right"><div class="lds-dual-ring loadingIcon"></div>Done</button>
                </div>
            </div>
        `;

        Swal.fire({html:html, showConfirmButton: false});
        let input = $("#input");
        input.val(cartTitle.html()).select()

        $("#cancelBtn").click(function(){
            Swal.close();
            return false;
        });

        $("#doneBtn").click(function(){
            let url = `/restAPI/orderController.php?action=modifyCartTitle&dataType=json`;
            let params = new URLSearchParams();
            params.append('orders_id',cartId);
            params.append('orders_name',$("#input").val());
            axios.post(url,params)
                .then(res=>{
                    if(res.data.code == 200){
                        cartTitle.html($("#input").val());
                        Swal.fire({
                            position: 'center',
                            type: 'success',
                            title: 'Success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }else{
                        Swal.fire({
                            position: 'center',
                            type: 'error',
                            title:'Error',
                            text: res.data.message,
                            showConfirmButton: true
                        })
                    }
                })
                .catch(error=>{
                    Swal.fire({
                        position: 'center',
                        type: 'error',
                        title: error,
                        showConfirmButton: true
                    })
                })
            Swal.close();
            return false;
        })
        return false;
    });

    $(".checkout-btn").click(function(){
        let isAbleCheckout = true;
        let text = "";
        const $panel = $(this).parents(".panel");
        const orderId = $panel.find('table').attr('data-order-id');

        if($panel.find('input').val()==undefined){
            Swal.fire('Oops...','Please add at least one product to your quotation!','warning');
            return false;
        }


        $panel.find("tr[data-product-id]").each(function(){
            const sku = $(this).find('.sku').html();
            const quantity = Number($(this).find('input').val());
            const stock = Number($(this).find('[data-stock]').attr('data-stock'));
            if(quantity > stock){
                isAbleCheckout = false;
                text += `${sku}, `;
            }
        })

        if(isAbleCheckout){
            const url = `/admin/order/index.php?s=quotation-list-checkout&orderId=${orderId}`;
            window.location.href = url;
        }else{
            Swal.fire({
                type: 'warning',
                title: 'Out of stock',
                text:`${text} has no enough stock. Please contact with us to get help!`,
                showConfirmButton: true
            });
        }
        return false;
    })

})