//Cart start
let customModal = $("#customModal");
let customModalContent = $("#customModalContent");
$("button[data-product-id]").click(function(){
    let html =`
        <div class="modal-box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">ADD PRODUCTS TO MY QUOTATION</h3>
            </div>
            <div class="modal-body">
                <table class="table product-overview">
                    <thead>
                    <tr>
                        <th>Product info</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th style="text-align:center">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td id="cartProductName"></td>
                        <td id="cartProductPrice"></td>
                        <td width="100">
                            <input id="cartProductPriceQuantity" type="number" min="1" class="form-control" value="1">
                        </td>
                        <td align="center" width="200"><h4 id="cartProductAmount" ></h4></td>
                    </tr>
                    </tbody>
                </table>
                <table class="table product-overview">
                    <tr>
                        <td width="150">Select a quotation</td>
                        <td id="selectQuotation"></td>
                    </tr>
                </table>
                </div>
                <div class="modal-footer">
                <div class="lds-dual-ring"></div>
                <button id="cartProductAddBtn" type="button" class="btn btn-danger"><div class="lds-dual-ring loadingIcon"></div>Add to quotation</button>
            </div>
        </div>
    `;

    Swal.fire({
        html:html,
        width:640,
        showConfirmButton: false,
    })

    let name = $(this).attr('data-product-name');
    let sku = $(this).attr('data-product-sku');
    let price = Number($(this).attr('data-product-price'));
    let productId = $(this).attr('data-product-id');
    let cartId = 0;
    //$("#cartModal").modal('show');

    $('.close').click(function(){
        Swal.close();
    })

    $('#selectQuotation').selectInput(
        `/restAPI/orderController.php?action=getMyCarts&dataType=json`,
        `/restAPI/orderController.php?action=addCart&dataType=json`,
        'Create a new quotation',
        (v)=>{cartId = v;}
    );

    $("#cartProductName").html(name);
    $("#cartProductPrice").html("$"+(price/100).toFixed(2));
    let quantity = 1;
    let amount = price * quantity;
    $("#cartProductAmount").html("$"+(amount/100).toFixed(2));
    $("#cartProductPriceQuantity").on("keyup change",function(e){
        quantity = e.target.value >= 1 ? e.target.value : 1;
        $("#cartProductAmount").html("$"+(price * quantity / 100).toFixed(2));
        $(this).val(quantity);
    }).click(function(){
        $(this).select();
    });
    let cartProductAddBtn = $("#cartProductAddBtn");
    let loadingIcon = cartProductAddBtn.find(".loadingIcon");
    cartProductAddBtn.click(function(){

        if(!cartId){
            showAlert('Please create/select a quotation!','error');
            return false;
        }

        cartProductAddBtn.attr('disabled',true);
        loadingIcon.css({'display':'inline-block'});
        let params = new URLSearchParams();
        params.append('order_id', cartId);
        params.append('product_id', productId);
        params.append('product_count', quantity);

        axios.post('/restAPI/orderController.php?action=modifyOrderProduct&dataType=json',params)
            .then(function (response) {
                if(response.data.code == 200){
                    Swal.fire({
                        position: 'center',
                        type: 'success',
                        title:'Success',
                        text: response.data.message,
                        showConfirmButton: false,
                        timer: 1500
                    })
                }else if(response.data.message){
                    Swal.fire('Oops...', response.data.message, 'error');
                }else{
                    Swal.fire('Oops...', 'There is an unknown error occur in the API', 'error');
                }
            })
            .catch(function (error) {
                Swal.fire('Oops...', 'There are some errors of API', 'error');
            })
            .then(function () {
                // always executed
                cartProductAddBtn.attr('disabled',false);
                loadingIcon.hide();
            });

    })
})
//Cart end