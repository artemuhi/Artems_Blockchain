<?php
class artembc {}
function artembc_init($filename, $lock=true, $max_size=400094) {
    $bc=new artembc;
    $bc->max_size=$max_size;
    $bc->file=fopen($filename, "w+");
    if ($lock) {
        if (!flock($bc->file, LOCK_EX)) {
            return false;
        }
    }
    return $bc;
}
function artembc_createbc($bc, $data=[]) {
    $temp=[[
        "id"=>0,
        "time"=>time(),
        "data"=>$data,
        "transaction"=>[]
    ]];
    $temp["hash"]=hash("sha256", json_encode($temp));
    return fwrite($bc->file, json_encode($temp), $bc->max_size);
}
function artembc_addtransaction() {}
function artembc_initblock() {}
function artembc_checkbc() {}
function artembc_getblock() {}
function artembc_getbc() {}
function artembc_getnamebc() {}
?>