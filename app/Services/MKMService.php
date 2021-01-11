<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;

class MKMService
{

    private $baseUrl = "https://api.cardmarket.com";
    private $baseUrlSandbox = "https://sandbox.cardmarket.com";
    //private $baseUrl = "https://sandbox.mkmapi.eu/ws/v2.0/output.json/";
    private $advancedUrlJson = "/ws/v2.0/output.json/";
    private $advancedUrl = "/ws/v2.0/";

    private $json = true;
    private $method;
    private $url;
    private $appToken;
    private $appSecret;
    private $accessToken;
    private $nonce;
    private $timestamp;
    private $signatureMethod;
    private $version;

    private $params;
    private $baseString;
    private $header;

    private $isStatic;
    private $languages = array('cz', 'EN', 'FR', 'DE', 'ES', 'IT', 'CH', 'JA', 'PO', 'RU', 'KO', 'TCH');

    private $dataBag;
    private $dataBagCPT = 0;

    const Send = "send";
    const ConfirmReception = "confirmReception";
    const Cancel = "cancel";
    const RequestCancellation = "requestCancellation";
    const AcceptCancellation = "acceptCancellation";

    /*
     *    1 - English
     *    2 - French
     *    3 - German
     *    4 - Spanish
     *    5 - Italian
     *    6 - Simplified Chinese
     *    7 - Japanese
     *    8 - Portuguese
     *    9 - Russian
     *    10 - Korean
     *    11 - Traditional Chinese
     */

    public function __construct()
    {
        if (env("APP_SANDBOX")) {
            $this->baseUrl = $this->baseUrlSandbox;
        }
        $this->dataBag = array();
    }

    public function changeState($id,string $action ,string $reason = null, ?bool $relistItems = null)
    {
        $data = new changeOrderState($action, $reason, $relistItems);
\Debugbar::info($data->getXML());
        return $this->call('order/' . $id , "PUT", $data);

    }

    public function addToDataBag($idProduct, $count, $price, $condition = "MT", $language = "EN", $comments = "", $isFoil = "false", $isSigned = "false", $isAltered = "false", $isPlayset = "false")
    {
        if ($this->dataBagCPT >= 100)
            return false;

        $this->dataBagCPT++;

        if (is_numeric($language))
            $idLanguage = $language;
        else
            $idLanguage = array_search(strtoupper($language), $this->languages);

        $data = new product();

        $data->idProduct = $idProduct;
        $data->idLanguage = $idLanguage;
        $data->comments = $comments;
        $data->count = $count;
        $data->price = $price;
        $data->condition = $condition;
        $data->isFoil = $isFoil;
        $data->isSigned = $isSigned;
        $data->isAltered = $isAltered;
        $data->isPlayset = $isPlayset;

        array_push($this->dataBag, $data);
    }

    public function addToStockFromDataBag()
    {
        return $this->call("stock", "POST", $this->dataBag);
    }

    /*
        public function registerUser()
        {
            $this->json = false;
            return $this->call("account/register");
        }
    */
    public function getCaptcha()
    {
        //$this->json = false;
        return $this->call("captcha");
    }

    public function getGames()
    {
        return $this->call("games");
    }

    public function getAccount()
    {
        return $this->call("account");
    }

    public function getExpansions($idGame = 1)
    {
        $this->isStatic = true;
        return $this->call("games/" . $idGame . "/expansions");
    }

    public function getSingles($idExpansion = 2447)
    {
        $this->isStatic = true;
        return $this->call("expansions/" . $idExpansion . "/singles");
    }

    public function getProduct($idProduct = 378099)
    {
        return $this->call("products/" . $idProduct);
    }

    public function getArticle($idArticle)
    {
        return $this->call("stock/article/" . $idArticle);
    }

    public function getStock($starting = null)
    {
        if ($starting == null)
            return $this->call("stock");
        return $this->call("stock/" . $starting);
    }

    public function getPriceGuide()
    {
        $this->isStatic = true;
        return $this->call("priceguide");
    }

//bought or 1
//paid or 2
//sent or 4
//received or 8
//lost or 32
//cancelled or 128
    public function getSellerOrders($state, $start = null)
    {
        //$this->isStatic = true; //Todo: remove after testing
        return $this->call("orders/1/" . $state . ($start != null ? '/' . $start : ''));
    }

    public function getOrder($id)
    {
        //$this->isStatic = true; //Todo: remove after testing
        return $this->call("order/" . $id);
    }

    public function getProductList()
    {
        $this->isStatic = true;
        return $this->call("productlist");
    }

    public function saveProductList()
    {
        $response = $this->getProductList();

        if (!isset($response->productsfile))
            return false;

        $productListCoded = $response->productsfile;

        $productList = base64_decode($productListCoded);
        $productlistCSV = gzdecode($productList);
        Storage::put('MKMResponses/productList.csv', $productlistCSV);

        return;
    }

    public function getStockFile()
    {
        $this->isStatic = true;
        return $this->call("stock/file");
    }

    public function saveStockFile()
    {
        $response = $this->getStockFile();

        if (!isset($response->stock))
            return false;

        $stockFileCoded = $response->stock;

        $stockFile = base64_decode($stockFileCoded);
        $stockFileCSV = gzdecode($stockFile);
        Storage::put('MKMResponses/stockFile.csv', $stockFileCSV);

        return true;
    }


    public function addToStock($idProduct, $count, $price, $condition = "MT", $language = "EN", $comments = "", $isFoil = "false", $isSigned = "false", $isAltered = "false", $isPlayset = "false")
    {
        if (is_numeric($language))
            $idLanguage = $language;
        else
            $idLanguage = array_search(strtoupper($language), $this->languages);

        $data = new product();

        $data->idProduct = $idProduct;
        $data->idLanguage = $idLanguage;
        $data->comments = $comments;
        $data->count = $count;
        $data->price = $price;
        $data->condition = $condition;
        $data->isFoil = $isFoil;
        $data->isSigned = $isSigned;
        $data->isAltered = $isAltered;
        $data->isPlayset = $isPlayset;

        return $this->call("stock", "POST", $data);
    }

    public function increaseStock($idArticle, $quantity)
    {
        $data = new baseArticle();
        $data->idArticle = $idArticle;
        $data->count = $quantity;
        return $this->call("stock/increase", "PUT", $data);
    }

    public function decreaseStock($idArticle, $quantity)
    {
        $data = new baseArticle();
        $data->idArticle = $idArticle;
        $data->count = $quantity;
        return $this->call("stock/decrease", "PUT", $data);
    }

    public function changeArticleInStock($idArticle, $count, $price, $condition = "MT", $language = "EN", $comments = "", $isFoil = "false", $isSigned = "false", $isAltered = "false", $isPlayset = "false")
    {
        $idLanguage = array_search(strtoupper($language), $this->languages);

        $data = new article();

        $data->idArticle = $idArticle;
        $data->idLanguage = $idLanguage;
        $data->comments = $comments;
        $data->count = $count;
        $data->price = $price;
        $data->condition = $condition;
        $data->isFoil = $isFoil;
        $data->isSigned = $isSigned;
        $data->isAltered = $isAltered;
        $data->isPlayset = $isPlayset;
        return $this->call("stock", "PUT", $data);
    }

    public function deleteFromStock($idArticle, $count)
    {
        $data = new baseArticle();

        $data->idArticle = $idArticle;
        $data->count = $count;

        return $this->call("stock", "DELETE", $data);

    }

    public function setTrackingNumber($id, $trackingNumber){
        $data = new trackingNumber($trackingNumber);

        return $this->call("order/" . $id ."/tracking", "PUT", $data);
    }

    private function call($command, $method = "GET", $data = null)
    {

        if ($this->isStatic) {
            $exp = ".json";
            $sandbox = "";

            if (!$this->json)
                $exp = ".xml";

            if (env("APP_SANDBOX"))
                $sandbox = ".sandbox";

            $path = "MKMResponses/" . $command;
            $pathArray = explode('/', $path);

            $filename = 'data' . $sandbox . $exp;

            if (in_array($path . '/' . $filename, Storage::files($path))) {
                $responseFile = json_decode(Storage::get($path . '/' . $filename));
                if ($responseFile != null) {
                    \Debugbar::info("took from localhost");
                    return $responseFile;
                }
            }
        }
//$t = time();
        $this->init($command, $method);
        $this->setParams();
        $this->setMethodAndUrl();
        $this->encodeParams();
        $this->sign();
        $this->getHeader();
        $xmlData = null;
        if ($data != null)
            if (count($this->dataBag) > 0)
                $xmlData = $this->getXMLFromDataBag();
            else
                $xmlData = $data->getXml();
        //var_dump($xmlData);

        \Debugbar::info($xmlData);

        $response = $this->exec($xmlData);
//var_dump(time()- $t);
        if ($this->isStatic && $response != null) {
            $p = '';
            foreach ($pathArray as $dir) {
                if (!Storage::exists($p . $dir))
                    Storage::makeDirectory($p . $dir);
                $p .= $dir . '/';
            }

            if ($this->json) {
                $toStore = json_encode($response);
            } else {
                $toStore = $response;
            }
            if (!in_array($path . '/' . $filename, Storage::directories($path))) {

                $responseFile = Storage::put($path . '/' . $filename, $toStore);

            }
        }

        return $response;
    }


    /**
     * Declare and assign all needed variables for the request and the header
     *
     * @var $method string Request method
     * @var $url string Full request URI
     * @var $appToken string App token found at the profile page
     * @var $appSecret string App secret found at the profile page
     * @var $accessToken string Access token found at the profile page (or retrieved from the /access request)
     * @var $accessSecret string Access token secret found at the profile page (or retrieved from the /access request)
     * @var $nonce string Custom made unique string, you can use uniqid() for this
     * @var $timestamp string Actual UNIX time stamp, you can use time() for this
     * @var $signatureMethod string Cryptographic hash function used for signing the base string with the signature, always HMAC-SHA1
     * @var version string OAuth version, currently 1.0
     */
    private function init($command, $method)
    {
        $url = $this->baseUrl . ($this->json ? $this->advancedUrlJson : $this->advancedUrl) . $command;

        //\Debugbar::info($url);
        $this->method = $method;
        $this->url = $url;
        if (env("APP_SANDBOX")) {
            $this->appToken = "ojasRxl1ABB2fqbs";
            $this->appSecret = "Ev06X3y8vDBQlJrwNV70sM2m79EdPNjP";
            $this->accessToken = "h9li5PXejXIVLxsHhjF7sLsAuAmzEO9y";
            $this->accessSecret = "EPXwe1aFaKUtNlaPkU877UISl63c6rcr";
        } else {
            $this->appToken = "pOvuvylU7zLW8wW0";
            $this->appSecret = "SmKuTvyuSOHeTblc9IWekFZ4q4CzPGAG";
            $this->accessToken = "vRj39mHudtGfKCaKF6bXwIoL2XCKWTVx";
            $this->accessSecret = "QPbBWajSgTfaINwtka9P5EPpsSrQ7X89";
        }
        $this->nonce = $this->getNonce();
        $this->timestamp = time();
        $this->signatureMethod = "HMAC-SHA1";
        $this->version = "1.0";


    }

    /**
     * Gather all parameters that need to be included in the Authorization header and are know yet
     *
     * Attention: If you have query parameters, they MUST also be part of this array!
     *
     * @var $params array|string[] Associative array of all needed authorization header parameters
     */
    private function setParams()
    {
        $this->params = array(
            'realm' => $this->url,
            'oauth_consumer_key' => $this->appToken,
            'oauth_token' => $this->accessToken,
            'oauth_nonce' => $this->nonce,
            'oauth_timestamp' => $this->timestamp,
            'oauth_signature_method' => $this->signatureMethod,
            'oauth_version' => $this->version,
        );
        //\Debugbar::info($this->params);
    }

    /**
     * Start composing the base string from the method and request URI
     *
     * Attention: If you have query parameters, don't include them in the URI
     *
     * @var $baseString string Finally the encoded base string for that request, that needs to be signed
     */
    private function setMethodAndUrl()
    {
//check if query parameters
        $url = $this->url;
        $this->baseString = strtoupper($this->method) . "&";
        $this->baseString .= rawurlencode($url) . "&";

    }

    /*
     * Gather, encode, and sort the base string parameters
     */
    private function encodeParams()
    {
        $encodedParams = array();
        foreach ($this->params as $key => $value) {
            if ("realm" != $key) {
                $encodedParams[rawurlencode($key)] = rawurlencode($value);
            }
        }
        ksort($encodedParams);
        /*
    * Expand the base string by the encoded parameter=value pairs
    */
        $values = array();
        foreach ($encodedParams as $key => $value) {
            $values[] = $key . "=" . $value;
        }
        $paramsString = rawurlencode(implode("&", $values));
        $this->baseString .= $paramsString;
        //    \Debugbar::info($this->baseString);

    }

    private function sign()
    {

        /*
         * Create the signingKey
         */
        $signatureKey = rawurlencode($this->appSecret) . "&" . rawurlencode($this->accessSecret);

        /**
         * Create the OAuth signature
         * Attention: Make sure to provide the binary data to the Base64 encoder
         *
         * @var $oAuthSignature string OAuth signature value
         */
        $rawSignature = hash_hmac("sha1", $this->baseString, $signatureKey, true);
        $oAuthSignature = base64_encode($rawSignature);

        /*
         * Include the OAuth signature parameter in the header parameters array
         */
        $this->params['oauth_signature'] = $oAuthSignature;

    }

    private function getHeader()
    {

        /*
         * Construct the header string
         */
        $this->header = "Authorization: OAuth ";
        $headerParams = array();
        foreach ($this->params as $key => $value) {
            $headerParams[] = $key . "=\"" . $value . "\"";
        }
        $this->header .= implode(", ", $headerParams);
        //\Debugbar::info($this->header);
    }

    private function exec($data = null)
    {

        /*
         * Get the cURL handler from the library function
         */
        $curlHandle = curl_init();

        /*
         * Set the required cURL options to successfully fire a request to MKM's API
         *
         * For more information about cURL options refer to PHP's cURL manual:
         * http://php.net/manual/en/function.curl-setopt.php
         */
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_URL, $this->url);
        //\Debugbar::info($data);
        if ($data != null) {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $this->method);
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array($this->header));
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        /**
         * Execute the request, retrieve information about the request and response, and close the connection
         *
         * @var $content string Response to the request
         * @var $info array Array with information about the last request on the $curlHandle
         */
        $content = curl_exec($curlHandle);
        $info = curl_getinfo($curlHandle);
        curl_close($curlHandle);
        //\Debugbar::info($info);

        /*
         * Convert the response string into an object
         *
         * If you have chosen XML as response format (which is standard) use simplexml_load_string
         * If you have chosen JSON as response format use json_decode
         *
         * @var $decoded \SimpleXMLElement|\stdClass Converted Object (XML|JSON)
         */
        return json_decode($content);
        //return simplexml_load_string($content);
    }

    private function getNonce()
    {
        return rand(1000000000000, 9999999999999);
    }

    private function getXMLFromDataBag()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?><request>';
        foreach ($this->dataBag as $data)
            $xml .= $data->getPureXML();
        $xml .= '</request>';

        return $xml;
    }

}

abstract class request
{

    public abstract function getPureXML();

    public function getXML()
    {

        return '<?xml version="1.0" encoding="UTF-8" ?>' .
            '<request>' .
            $this->getPureXML() .
            '</request>';
    }
}

class trackingNumber extends request{
    private $trackingNumber;
    public function __construct($trackingNumber)
    {
        var_dump($trackingNumber);
        $this->trackingNumber = $trackingNumber;
    }

    public function getPureXML()
    {
return '<trackingNumber>' . $this->trackingNumber . '</trackingNumber>';    }
}


class baseArticle extends request
{
    public $idArticle;
    public $count;

    public function getPureXML()
    {
        return '<article>
        <idArticle>' . $this->idArticle . '</idArticle>
        <count>' . $this->count . '</count>
    </article>';
    }

}

class article extends baseArticle
{
    public $idLanguage = "1";
    public $comments = "";
    public $price;
    public $condition = "MT";
    public $isFoil = "false";
    public $isSigned = "false";
    public $isAltered = "false";
    public $isPlayset = "false";

    public function getPureXML()
    {
        return '
    <article>
        <idArticle>' . $this->idArticle . '</idArticle>
        <idLanguage>' . $this->idLanguage . '</idLanguage>
        <comments>' . $this->comments . '</comments>
        <count>' . $this->count . '</count>
        <price>' . $this->price . '</price>
        <condition>' . $this->condition . '</condition>
        <isFoil>' . $this->isFoil . '</isFoil>
        <isSigned>' . $this->isSigned . '</isSigned>
        <isAltered>' . $this->isAltered . '</isAltered>
        <isPlayset>' . $this->isPlayset . '</isPlayset>
    </article>';
    }

}

class product extends article
{
    public $idProduct;

    public function getPureXML()
    {
        return '<article>
        <idProduct>' . $this->idProduct . '</idProduct>
        <idLanguage>' . $this->idLanguage . '</idLanguage>
        <comments>' . $this->comments . '</comments>
        <count>' . $this->count . '</count>
        <price>' . $this->price . '</price>
        <condition>' . $this->condition . '</condition>
        <isFoil>' . $this->isFoil . '</isFoil>
        <isSigned>' . $this->isSigned . '</isSigned>
        <isAltered>' . $this->isAltered . '</isAltered>
        <isPlayset>' . $this->isPlayset . '</isPlayset>
        </article>';
    }


}

class changeOrderState extends request
{
    private $action;
    private $reason;
    private  $relistItems;

    public function __construct(string $action,string $reason = null, ?bool $relistItems = null)
    {
        $this->action = $action;
        $this->reason = $reason;
        $this->relistItems = $relistItems;
    }

    public function getPureXML()
    {
        return '<action>' .
            $this->action .
            '</action>' .
            ($this->reason != null ?
                '<reason>' .
                $this->reason .
                '</reason>' :
                '') .
            ($this->relistItems != null ?
                '<relistItems>' .
                $this->relistItems .
                '</relistItems>' :
                '');
    }
}
