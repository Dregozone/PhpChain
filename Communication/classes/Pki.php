<?php 

    Class Pki 
    {
        public static function generateKeyPair() {
            
            $res = openssl_pkey_new([
                "private_key_bits" => 512, /* 2048, */
                "private_key_type" => OPENSSL_KEYTYPE_RSA
            ]);
            
            openssl_pkey_export($res, $privKey);
            
            return [$privKey, openssl_pkey_get_details($res)['key']];
        }

        /* This will sign the transaction hash */
        public static function encrypt($message, $privKey) {
            
            openssl_private_encrypt($message, $crypted, $privKey);
            
            return base64_encode($crypted);
        }

        public static function decrypt($crypted, $pubKey) {
            
            openssl_public_decrypt(base64_decode($crypted), $decrypted, $pubKey);
            
            return $decrypted;
        }

        public static function isValid($message, $crypted, $pubKey) {
            
            return $message == self::decrypt($crypted, $pubKey);
        }
    }
