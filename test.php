<?php
function tryme($num)
{
    if ($num > 3) {
        throw new Exception("if u see this then number must be bigger than 3");
    }
}

try {
    tryme(5);
} catch (Exception $ex) {
    echo $ex->getMessage();
}
