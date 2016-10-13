<?php
class Json {
    private $aValues = array();

    public function setData($sKey, $mValue) {
        $this->aValues[$sKey] = $mValue;
    }

    public function keyIsSet($sKey) {
        return array_key_exists($sKey, $this->aValues);
    }

    public function delData($sKey) {
        unset($this->aValues[$sKey]);
    }

    public function getJsonAsString() {
        return json_encode($this->aValues);
    }
}