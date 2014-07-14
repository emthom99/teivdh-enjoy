<?php
/*
 * RC4 symmetric cipher encryption/decryption
 *
 * @license Public Domain
 * @param string key - secret key for encryption/decryption
 * @param string str - string to be encrypted/decrypted
 * @return string
 */

class Rc4 {
    private static function rc4_encode($key, $src_data) {
            $s = array();
            for ($i = 0; $i < 256; $i++) {
                    $s[$i] = $i;
            }
            $j = 0;
            for ($i = 0; $i < 256; $i++) {
                    $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
                    $x = $s[$i];
                    $s[$i] = $s[$j];
                    $s[$j] = $x;
            }
            $i = 0;
            $j = 0;
            $res = '';
            $sizeData=count($src_data);
            for ($y = 0; $y < $sizeData; $y++) {
                    $i = ($i + 1) % 256;
                    $j = ($j + $s[$i]) % 256;
                    $x = $s[$i];
                    $s[$i] = $s[$j];
                    $s[$j] = $x;
                    $res[]= $src_data[$y] ^ ($s[($s[$i] + $s[$j]) % 256]);
            }
            return $res;
    }
    private static function charsToHex($chars){
            $res='';
            $sizeData=count($chars);
            for($i=0;$i<$sizeData;$i++)
                    $res.=sprintf('%02s',dechex($chars[$i]));
            return $res;
    }

    private static function hexToChars($str){
            $res=array();
            $sizeData=strlen($str);
            for($i=substr($str,0,2)=='0x'?2:0;$i<$sizeData;$i+=2)
                    $res[]=hexdec(substr($str,$i,2));
            return $res;
    }

    private static function charsToStr($chars){
            $res='';
            $sizeData=count($chars);
            for($i=0;$i<$sizeData;$i++)
                    $res.=chr($chars[$i]);
            return $res;
    }

    private static function strToChars($str){
            $res=array();
            $sizeData=strlen($str);
            for($i=0;$i<$sizeData;$i++)
                    $res[]=ord($str[$i]);
            return $res;
    }

    public static function encode($key,$content){
            $content=self::strToChars($content);
            $res=self::rc4_encode($key,$content);
            return self::charsToHex($res);
    }

    public static function decode($key,$content){
            $content=self::hexToChars($content);
            $res=self::rc4_encode($key,$content);
            return self::charsToStr($res);
    }
}
?>