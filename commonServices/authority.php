<?php
//用户权限配置
$_AUT = [
    'SYSTEM_SETTING'=>
        [
            'USER_CATEGORY'=>getAuthorityNum(1)
        ],
    'USER'=>
        [
            'GET_LIST'=>getAuthorityNum(1),
            'VIEW_OTHER'=>getAuthorityNum(2),
            'ADD'=>getAuthorityNum(3),
            'UPDATE'=>getAuthorityNum(4),
            'DELETE'=>getAuthorityNum(5),
            'BIND_DEALER_TO_SELLER'=>getAuthorityNum(6)
        ],
    'COMPANY'=>
        [
            'GET_LIST'=>getAuthorityNum(1),
            'VIEW_OTHER'=>getAuthorityNum(2),
            'ADD'=>getAuthorityNum(3),
            'UPDATE'=>getAuthorityNum(4),
            'DELETE'=>getAuthorityNum(5),
        ],
    'DEALER_APPLICATION'=>
        [
            'REVIEW'=>getAuthorityNum(1),
            'DELETE'=>getAuthorityNum(2),
        ],
    'ITEM'=>
        [
            'GET_LIST'=>getAuthorityNum(1),
            'ADD'=>getAuthorityNum(2),
            'UPDATE'=>getAuthorityNum(3),
            'DELETE'=>getAuthorityNum(4)
        ],
    'WAREHOUSE'=>
        [
            'GET_LIST'=>getAuthorityNum(1),
            'ADD'=>getAuthorityNum(2),
            'UPDATE'=>getAuthorityNum(3),
            'DELETE'=>getAuthorityNum(4)
        ],
    'INVENTORY'=>
        [
            'GET_LIST'=>getAuthorityNum(1),
            'STOCK_IN'=>getAuthorityNum(2),
            'STOCK_OUT'=>getAuthorityNum(3)
        ],
    'INVENTORY_LOG'=>
        [
            'UPDATE'=>getAuthorityNum(1)
        ],
    'PRODUCT'=>
        [
            'ADD'=>getAuthorityNum(1),
            'UPDATE'=>getAuthorityNum(2),
            'DELETE'=>getAuthorityNum(3),
        ],
    'PRODUCT_CATEGORY'=>
        [
            'ADD'=>getAuthorityNum(1),
            'UPDATE'=>getAuthorityNum(2),
            'DELETE'=>getAuthorityNum(3),
        ]
];
function getAuthorityNum($int){return pow(2,$int);}
?>
