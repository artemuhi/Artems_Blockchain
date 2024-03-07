<?php
function artembc_createbc($filename, $namebc, $data=[]) {
    $temp=[[
        "id"=>0,
        "time"=>time(),
        "data"=>$data,
        "transaction"=>[],
        "prevhash"=>"ZeroBlock"
    ]];
    $temp[0]["data"]["namebc"]=$namebc;
    $temp[0]["hash"]=hash("sha256", json_encode($temp));
    return artembc_save($filename, $temp);
}
function artembc_addtransaction($filename, &$transaction, $data) {
    $bc=artembc_load($filename);
    $temp=count($transaction);
    $transaction[]=[
        "id"=>count($transaction),
        "idblock"=>count($bc)+1;
        "time"=>time(),
        "data"=>$data
    ];
    $transaction[$temp]["hash"]=hash("sha256", json_encode($transaction[$temp]));
    return true;
}
function artembc_initblock($filename, &$transaction, $data=[]) {
    $bc=artembc_load($filename);
    $temp=count($bc);
    for($i = 0, $size = count($transaction), $correct=true; $i < $size; ++$i) {
        $block=$transaction[$i];
        $hash=hash("sha256", json_encode([
            "id"=>$block["id"],
            "idblock"=>$block["idblock"],
            "time"=>$block["time"],
            "data"=>$block["data"]
        ]));
        if ($block["idblock"] != $temp and $hash != $block["hash"]) {
            $correct=false;
        };
    };
    if ($correct) {
        $bc[]=[
            "id"=>$temp,
            "time"=>time(),
            "data"=>$data,
            "transaction"=>$transaction,
            "prevhash"=>hash("sha256", json_encode($bc[$temp-1]))
        ];
    };
    artembc_save($filename, $bc);
    return $correct;
}
function artembc_checkbc() {}
function artembc_getblock($filename, $id) {
    $temp=artembc_load($filename);
    return $temp[$id];
}
function artembc_getbc($filename) {
    return artembc_load($filename);
}
function artembc_getnamebc($filename) {
    $bc=artembc_load($filename);
    return $bc[0]["data"]["namebc"];
}
function artembc_save($filename, $data) {
    return file_put_contents($filename, json_encode($data));
}
function artembc_load($filename) {
    return json_decode(file_get_contents($filename));
}
?>