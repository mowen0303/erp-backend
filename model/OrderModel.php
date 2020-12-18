<?php
namespace model;
use Exception;
use \Model as Model;
use \Helper as Helper;

class OrderModel extends Model
{
    public $deliverType = ['shipping','pickup'];

    public $orderType = [
        'cart'=>'cart',
        'order'=>'order'
    ];

    public $orderStatus = [
        'initiate'=>'initiate',
        'make_payment'=>'make_payment',
        'order_confirmed'=>'order_confirmed',
        'ready_for_pick_up'=>'ready_for_pick_up',
        'picked_up'=>'picked_up',
        'shipped'=>'shipped',
        'delivered'=>'delivered'
    ];

    /**
     * @param string orders_name
     * @return int
     * @throws Exception
     */
    public function addCart(){
        $userModel = new UserModel();
        $orderId = Helper::uuid(0);
        $arr = [];
        $arr['orders_id'] = $orderId;
        $arr['orders_user_id'] = $userModel->getCurrentUserId();
        $arr['orders_name'] = Helper::post('orders_name',null,1,255);
        $this->addRow('orders', $arr);
        return $orderId;
    }

    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyCartTitle($orderId){
        $order = $this->sqltool->getRowBySql("SELECT * FROM orders WHERE orders_id IN ('$orderId')") or Helper::throwException(null,404);
        $userModel = new UserModel();
        $userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS",$order['orders_user_id']) or Helper::throwException(null,403);
        $arr = [];
        $arr['orders_name'] = Helper::post('orders_name',null,1,255);
        return $this->updateRowById('orders', $orderId, $arr,false);
    }

    /**
     * @param array $userIds
     * @param $type [cart|order]
     * @param array $option
     * @return array|null
     * @throws Exception
     */
    public function getOrders(array $orderIds, array $option=[]){
        $bindParams = [];
        $selectFields = "orders.*";
        $whereCondition = "";
        $orderCondition = "";
        $joinCondition = "";
        $result = null;

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($orderIds)!=0){
            $orderIds = Helper::convertIDArrayToString($orderIds,false);
            $whereCondition .= " AND orders_id IN ($orderIds)";
        }

        if($option['userIds']){
            $userIds = Helper::convertIDArrayToString($option['userIds']);
            $whereCondition .= " AND orders_user_id IN ($userIds)";
        }

        if($option['sellerIds']){
            $sellerIds = Helper::convertIDArrayToString($option['sellerIds']);
            $whereCondition .= " AND orders_seller_id IN ($sellerIds)";
        }

        if($option['type']){
            $type = $option['type'];
            $whereCondition .= " AND orders_type = '{$this->orderType[$type]}'";
        }

        if ($orderBy) {
            $orderCondition = "? ?";
            $bindParams[] = $orderBy;
            $bindParams[] = $sequence;
        }

        $selectFields .= ",".$this->userFieldSample1;
        $joinCondition .= " LEFT JOIN user ON user_id = orders_user_id ";

        $sql = "SELECT {$selectFields} FROM orders {$joinCondition} WHERE true {$whereCondition} ORDER BY {$orderCondition} orders_update_time DESC";
        if(array_sum($userIds)!=0){
            $result = $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            $result = $this->getListWithPage('orders',$sql,$bindParams,$pageSize);
        }

        if($option['withProducts'] && $result){
            $ordersIdArr=[];
            foreach ($result as $orders){
                $ordersIdArr[] = $orders['orders_id'];
            }
            $ordersIdStr = Helper::convertIDArrayToString($ordersIdArr,false);
            $sql = "
                    SELECT * FROM orders_product 
                    INNER JOIN product ON orders_product_product_id = product_id 
                    LEFT JOIN product_category ON product_product_category_id = product_category_id 
                    LEFT JOIN item_style ON product_item_style_id = item_style_id
                    WHERE orders_product_orders_id IN ({$ordersIdStr})
                    ";
            $productResult = $this->sqltool->getListBySql($sql);
            foreach ($productResult as $product){
                foreach ($result as $ordersKey => $orders){
                    if($product['orders_product_orders_id']==$orders['orders_id']){
                        $result[$ordersKey]['products'][] = $product;
                    }
                }
            }
        }

        return $result;

    }

    public function placeOrder($id){
        try {
            $userModel = new UserModel();
            $this->sqltool->mysqli->autocommit(FALSE);
            $deliverType = Helper::post('orders_deliver_type','Delivery type is required');
            $billingAddress = Helper::post('orders_billing_address');
            $warehouseAddress = Helper::post('orders_warehouse_address');
            $user = $userModel->getProfileOfUserById($userModel->getCurrentUserId());
            in_array($deliverType,$this->deliverType) or Helper::throwException('Delivery type is not accepted');
            $order = $this->sqltool->getRowBySql("SELECT * FROM orders WHERE orders_id IN ('$id')");
            $userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS",$order['orders_user_id']) or Helper::throwException(null,403);

            $arr = [];
            $arr['orders_deliver_type'] = $deliverType;
            $arr['orders_type'] = $this->orderType['order'];
            $arr['orders_status'] = $this->orderStatus['make_payment'];
            $arr['orders_date'] = date("Y-m-d H:i:s");
            $arr['orders_update_time'] = date("Y-m-d H:i:s");
            $arr['orders_seller_id'] = $user['user_reference_user_id'];

            if($deliverType == "shipping"){
                $billingAddress or Helper::throwException('Billing address is required');
                $arr['orders_billing_address'] = $billingAddress;
                $arr['orders_warehouse_address'] = '';
            }else{
                $warehouseAddress or Helper::throwException('Warehouse address is required');
                $arr['orders_billing_address'] = '';
                $arr['orders_warehouse_address'] = $warehouseAddress;
            }
            $result = $this->updateRowById('orders',$id,$arr,false);
            $this->recalculatePrice($id);
            $this->sqltool->mysqli->commit();
            return $result;
        }catch (Exception $e){
            $this->sqltool->mysqli->rollback();
            Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
        }


    }


    public function deleteOrderByIds($orderIds){
        try {
            $userModel = new UserModel();
            $currentUserId = $userModel->getCurrentUserId();
            $orderIdStr = Helper::convertIDArrayToString($orderIds,false);
            $sql = "SELECT * FROM orders WHERE orders_id IN ({$orderIdStr})";
            $ordersArr = $this->sqltool->getListBySql($sql) or Helper::throwException("Order Id do not exist!",404);
            foreach ($ordersArr as $order){
                $order['orders_type'] == $this->orderType['cart'] or Helper::throwException("ORDERMD 1001 - Can not delete the orders.",403);
                $order['orders_user_id'] == $currentUserId or Helper::throwException("ORDERMD 1002 - Can not delete the orders.",403);
            }
            $this->sqltool->mysqli->autocommit(FALSE);
            $sql = "DELETE FROM orders WHERE orders_id IN ({$orderIdStr})";
            $this->sqltool->query($sql);
            $affectedRow = $this->sqltool->affectedRows;
            $sql = "DELETE FROM orders_product WHERE orders_product_orders_id IN ({$orderIdStr})";
            $this->sqltool->query($sql);
            $this->sqltool->mysqli->commit();
            return $affectedRow;
        }catch (Exception $e){
            $this->sqltool->mysqli->rollback();
            Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
        }
    }


    public function modifyOrderProduct($orderId, int $productId, int $count, $isAccumulate = true){
        $userModel = new UserModel();
        $order = $this->sqltool->getRowBySql("SELECT * FROM orders WHERE orders_id IN ({$orderId})") or Helper::throwException(null,404);
        $this->isAbleUpdateOrder($order) or Helper::throwException('The order status is not allowed to update',403);

        if($order['orders_status']==$this->orderStatus['make_payment']){
            $userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS") or Helper::throwException("Has no permission (ODMD1002)",403);
        }

        $productModel = new ProductModel();
        if($productModel->isProductExist($productId)){
            $sql = "SELECT * FROM orders_product WHERE orders_product_product_id IN ({$productId}) AND orders_product_orders_id IN ({$orderId})";
            $orderProduct = $this->sqltool->getRowBySql($sql);
            if($orderProduct){
                $arr = [];
                if($isAccumulate == ture){
                    $arr['orders_product_count'] = $orderProduct['orders_product_count']+$count;
                }else{
                    $arr['orders_product_count'] = $count;
                }
                $id = $this->updateRowById('orders_product',$orderProduct['orders_product_id'],$arr,false);
            }else{
                $arr = [];
                $arr['orders_product_product_id'] = $productId;
                $arr['orders_product_orders_id'] = $orderId;
                $arr['orders_product_count'] = $count;
                $id = $this->addRow('orders_product',$arr);
            }
            return $this->recalculatePrice($orderId);
        }else{
            Helper::throwException("Product is not existed",404);
        }


    }

    private function recalculatePrice($orderId){
        $productArr = $this->sqltool->getListBySql("
            SELECT * FROM orders_product 
            LEFT JOIN product ON orders_product_product_id = product_id 
            LEFT JOIN item_style ON product_item_style_id = item_style_id 
            LEFT JOIN product_category ON product_product_category_id = product_category_id 
            WHERE orders_product_orders_id IN ({$orderId})
        ");
        if(!$productArr) return ['orders_price_original'=>0,'orders_price_tax'=>0];
        $price = 0;
        foreach ($productArr as $product){
            $price += $product['orders_product_count']*$product['product_price'];
            $orderProductArr = [];
            $orderProductArr['orders_product_snapshot_price'] = $product['product_price'];
            $orderProductArr['orders_product_snapshot_attrs'] = "{$product['item_style_title']} {$product['product_category_title']} {$product['product_w']}x{$product['product_h']}x{$product['product_l']}";
            $orderProductArr['orders_product_snapshot_name'] = $product['product_name'];
            $orderProductArr['orders_product_snapshot_sku'] = $product['product_sku'];
            $this->updateRowById('orders_product',$product['orders_product_id'],$orderProductArr, false);
        }
        $tax = round($price*HST);
        $arr = [];
        $arr['orders_price_original'] = $price;
        $arr['orders_price_final'] = $price;
        $arr['orders_price_tax'] = $tax;
        $this->updateRowById('orders',$orderId,$arr,false);
        return ['orders_price_original'=>$price,'orders_price_tax'=>$tax];
    }

    public function updateOrderFinalPrice($orderId,$price){
        $finalPrice = (int) ($price*100);
        $arr = [];
        $arr['orders_price_final'] = $finalPrice;
        $arr['orders_price_tax'] = round($finalPrice*HST);
        $this->updateRowById('orders',$orderId,$arr);
        return $arr;
    }

    public function deleteOrderProductByIds($orderId,int $productId){
        try {
            $this->sqltool->mysqli->autocommit(FALSE);
            $order = $this->sqltool->getRowBySql("SELECT * FROM orders WHERE orders_id IN ('{$orderId}')") or Helper::throwException(null,404);
            $userModel = new UserModel();
            if($order['orders_type'] != $this->orderType['cart']){
                $userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS") or Helper::throwException(null,403);
            }else{
                $order['orders_user_id'] == $userModel->getCurrentUserId() or Helper::throwException(null,403);
            }
            $this->sqltool->mysqli->autocommit(false);
            $sql = "DELETE FROM orders_product WHERE orders_product_product_id IN ($productId) AND orders_product_orders_id IN ($orderId)";
            $this->sqltool->query($sql);
            $result =  $this->recalculatePrice($orderId);
            $this->sqltool->mysqli->commit();
            return $result;
        }catch (Exception $e){
            $this->sqltool->mysqli->rollback();
            Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
        }
    }

    public function echoOrderStatus($status){
        if($status == $this->orderStatus['make_payment']){
            echo "<span class='label label-info'>Make payment</span>";
        }else if($status == $this->orderStatus['ready_for_pick_up']){
            echo "<span class='label label-info'>Ready for pick up</span>";
        }else if($status == $this->orderStatus['picked_up']){
            echo "<span class='label label-info'>Picked up</span>";
        }else if($status == $this->orderStatus['shipped']){
            echo "<span class='label label-info'>Shipped</span>";
        }else if($status == $this->orderStatus['delivered']){
            echo "<span class='label label-info'>Delivered</span>";
        }
    }

    public function echoDeliveryToolTipHTML($orderRow){
        switch ($orderRow['orders_deliver_type']){
            case "shipping":
                $name = explode(',',$orderRow['orders_billing_address'])[0];
                echo "Ship to <span class='mytooltip tooltip-effect-4'>
                        <span class='tooltip-item'>{$name}</span> 
                        <span class='tooltip-content clearfix'>
                            <span class='tooltip-text'>{$orderRow['orders_billing_address']}</span> 
                        </span>
                    </span>";

                break;
            case "pickup":
                echo "Pick up from <span class='mytooltip tooltip-effect-4'>
                        <span class='tooltip-item'>Warehouse</span> 
                        <span class='tooltip-content clearfix'>
                            <span class='tooltip-text'>{$orderRow['orders_warehouse_address']}</span> 
                        </span>
                    </span>";
                break;
        }
    }

    public function getDeliver($orderRow){
        $result = [
            'type'=>'',
            'address'=>''
        ];
        switch ($orderRow['orders_deliver_type']){
            case "shipping":
                $name = explode(',',$orderRow['orders_billing_address'])[0];
                $result['type'] = 'Ship to';
                $result['address'] = $orderRow['orders_billing_address'];
                break;
            case "pickup":
                $result['type'] = 'Pick up from';
                $result['address'] = $orderRow['orders_warehouse_address'];
                break;
        }
        return $result;
    }

    public function isAbleUpdateOrder($order){
        try{
            $userModel = new UserModel();
            $currentUserId = $userModel->getCurrentUserId();
            if($order['orders_type']==$this->orderType['cart']){
                if($order['orders_user_id'] == $currentUserId) return false;
            }else if($order['orders_type']==$this->orderType['order']){
                if($order['orders_status']==$this->orderStatus['make_payment']){
                    $userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS");
                    return true;
                }
            }
            return false;
        }catch(Exception $e){
            return false;
        }
    }


}


?>
