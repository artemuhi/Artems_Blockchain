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
    $temp[0]["hash"]=hash("sha256", json_encode($temp[0]));
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
    
        $bc[$temp]["hash"]=hash("sha256", json_encode($bc[$temp]));
        artembc_save($filename, $bc);
        $transaction=[];
    };
    return $correct;
}
function artembc_checkbc($filename) {
    $bc=artembc_load($filename);
    $temp=count($bc);
    $correct=true;
    $correct2=true;
    foreach ($bc as $i => $transaction) {
        foreach ($transaction["transaction"] as $i1 => $v1) {
            if (count($transaction["transaction"])>0){
            	$tmp=[
            	    "id"=>$v1["id"],
            	    "idblock"=>$v1["idblock"],
            	    "time"=>$v1["time"],
            	    "data"=>$v1["data"]
            	];
            	$hash=hash("sha256", json_encode($tmp));
            	if ($v1["idblock"] != $i or $v1["id"] != $i1 or $hash != $v1["hash"]) {
            	    global $correct;
            	    $correct=false;
            	    break;
            	};
	        };
        };
        unset($i1, $v1);
        $hash=hash("sha256", json_encode([
            "id"=>$transaction["id"],
            "time"=>$transaction["time"],
            "data"=>$transaction["data"],
            "transaction"=>$transaction["transaction"],
            "prevhash"=>$transaction["prevhash"]
        ]));
        if ($hash != $transaction["hash"] or ($i != 0 and $transaction["prevhash"] != $bc[$i-1]["hash"])) {
            global $correct2;
            $correct2 = false;
            break;
        };
    };
    unset($i, $v);
    return $correct and $correct2;
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