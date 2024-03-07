<?php
function artembc_createbc($filename, $data=[]) {
    $temp=[[
        "id"=>0,
        "time"=>time(),
        "data"=>$data,
        "transaction"=>[]
    ]];
    $temp["hash"]=hash("sha256", json_encode($temp));
    return artembc_save($filename, $temp);
}
function artembc_addtransaction() {}
function artembc_initblock() {}
function artembc_checkbc() {}
function artembc_getblock() {}
function artembc_getbc() {}
function artembc_getnamebc() {}
function artembc_save($filename, $data) {
    return file_put_contents($filename, json_encode($data));
}
function artembc_load($filename) {
    return json_decode(file_get_contents($filename));
}
?>