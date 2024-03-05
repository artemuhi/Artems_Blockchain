<?php
class artembc {}
function artembc_init($filename, $lock=true, $max_size=4094) {
    $bc=new artembc;
    $bc->max_size=$max_size;
    $bc->file=fopen($filename, "w+");
    if ($lock) {
        if (!flock($bc->file, LOCK_EX)) {
            return false;
            die("bcerror:locked_file");
        }
    }
    
}
function artembc_createbc() {}
function artembc_addtransaction() {}
function artembc_initblock() {}
function artembc_checkbc() {}
function artembc_getblock() {}
function artembc_getbc() {}
function artembc_getnamebc() {}
?>