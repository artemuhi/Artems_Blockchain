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
        "idblock"=>count($bc),
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
            break;
        };
    };
    if ($correct) {
        $bc[]=[
            "id"=>$temp,
            "time"=>time(),
            "data"=>$data,
            "transaction"=>$transaction,
            "prevhash"=>$bc[$temp-1]["hash"]
        ];
    };
    $bc[$temp]["hash"]=hash("sha256", json_encode($bc[$temp]));
    artembc_save($filename, $bc);
    return $correct;
}
function artembc_checkbc($filename) {
    $bc=artembc_load($filename);
    $temp=count($bc);
    for ($i = 0, $size = count($bc), $correct2=true; $i < $size; ++$i) {
        $transaction=$bc[$i];
        for($i1 = 0, $size = count($transaction), $correct=true; $i1 < $size; ++$i1) {
            if (count($transaction["transaction"])>0){
                $block=$transaction["transaction"][$i1];
                $hash=hash("sha256", json_encode([
                    "id"=>$block["id"],
                    "idblock"=>$block["idblock"],
                    "time"=>$block["time"],
                    "data"=>$block["data"]
                ]));
                if ($block["idblock"] != $temp-1 or $hash != $block["hash"]) {
                    $correct=false;
                    break;
                };
            };
        };
        $hash=hash("sha256", json_encode([
            "id"=>$transaction["id"],
            "time"=>$transaction["time"],
            "data"=>$transaction["data"],
            "transaction"=>$transaction["transaction"],
            "prevhash"=>$transaction["prevhash"]
        ]));
        if (/*$hash != $transaction["hash"] or */($transaction["prevhash"] != $bc[$temp-1]["hash"] and $i != 0)) {
            $correct2 = [false, $i];
            break;
        };
    };
    return [$correct, $correct2];
}
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
    return json_decode(file_get_contents($filename), true);
}
?>
