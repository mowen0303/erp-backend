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
        ],
    'COMPANY'=>
        [
            'GET_LIST'=>getAuthorityNum(1),
            'VIEW_OTHER'=>getAuthorityNum(2),
            'ADD'=>getAuthorityNum(3),
            'UPDATE'=>getAuthorityNum(4),
            'DELETE'=>getAuthorityNum(5),
        ]
];
function getAuthorityNum($int){return pow(2,$int);}
?>
