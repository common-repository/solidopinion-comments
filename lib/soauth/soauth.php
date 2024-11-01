<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Max Morokko
 * Date: 8/5/15
 * Time: 12:09 PM
 * To change this template use File | Settings | File Templates.
 */

namespace SolidOpinion;

require_once SO_COMMENTS_DIR . "/lib/soauth/soutils.php";

/**
 * Class SOAuth
 * @package SolidOpinion
 */
class SOAuth
{
    /**
     * Encryption key (asymmetric)
     * @var string
     */
    private $_encKey;

    /**
     * Signing key (private symmetric)
     * @var string
     */
    private $_signKey;

    /**
     * Public key (public symmetric)
     * @var string
     */
    private $_pubKey;

    /**
     * Initializing vector
     * @var string
     */
    private $_iv;

    /**
     * AES cipher method/mode
     * @var string
     */
    private $_mode;

    /**
     * RAW_DATA option
     * @var string
     */
    private $_option;

    /**
     * Constructor initialized with 3 keys
     * @param string $encKey  encryption key
     * @param string $signKey signing key
     * @param string $pubKey  public key
     * @throws SOException
     */
    public function __construct($encKey, $signKey, $pubKey)
    {
        if (!extension_loaded('openssl')) {
            die('OpenSSL lib is not loaded');
        }

        if (SOUtils::strlen($encKey) != SOUtils::keylen()) {
            die('Invalid encryption key length');
        }

        $this->_encKey = $encKey;
        $this->_signKey = $signKey;
        $this->_pubKey = $pubKey;
        $this->_iv = openssl_random_pseudo_bytes(SOUtils::keylen() / 2, $strong);
        if (!$strong) {
            die('OpenSSL version is out of date');
        }

        $bitLength = 8 * SOUtils::keylen();
        $this->_mode = 'aes-' . $bitLength . '-cbc';

        $this->_option = defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : true;
    }

    /**
     * Encrypts data
     * @param array|mixed $payload array contains user data
     * @return string
     * @throws SOException
     */
    public function encrypt($payload)
    {
        // Complete payload with extra info
        date_default_timezone_set("UTC");
        $payload['_timestamp'] = time();
        $payload['_rnd'] = sha1($payload['_timestamp']);
        $jsonPayload = json_encode($payload);

        // Encrypt payload with AES CBC
        $ciphers = openssl_get_cipher_methods();
        if (!in_array($this->_mode, $ciphers)) {
            die('No suitable cipher method available');
        }

        $data = openssl_encrypt(
            $jsonPayload,
            $this->_mode,
            $this->_encKey,
            $this->_option,
            $this->_iv
        );

        // Base64 encode iv . salt . data
        $salt = openssl_random_pseudo_bytes(SOUtils::keylen() / 2);
        $encData = base64_encode($this->_iv . $salt . $data);

        // Sign
        if (!openssl_sign(
            $encData,
            $signature,
            $this->_signKey,
            OPENSSL_ALGO_SHA1
        )) {
            die('Failed to sign data');
        }

        $result = base64_encode($encData . '|' . $signature);

        return $result;
    }
}
