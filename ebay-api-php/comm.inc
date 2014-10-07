<?php
/*
 * 共通ファイル
 */

//Trading APIのスキーマのバージョン
define('TradingSchemaVer', '837');

//Trading APIのGW
$gwTradingURL = 'https://api.sandbox.ebay.com/ws/api.dll';
//$gwUrl = 'https://api.ebay.com/ws/api.dll'; //本番用

//LMS Bulk Data Exchange Serivce のGW
$gwLMSBulkURL = 'https://webservices.sandbox.ebay.com/BulkDataExchangeService';

//LMS File Transfer Seivice のGW
$gwLMSFtransURL = 'https://storage.sandbox.ebay.com/FileTransferService';

//認証サーバ
$auth_svr = 'signin.sandbox.ebay.com'; 
//$auth_svr = 'signin.ebay.com'; //本番用

/**********************************************************
 * ユーザ固有の情報 
 * 
 * eBayの開発者サイトから取得します。
 *
 * https://developer.ebay.com/
 *
 * テスト環境(Sandbox Keys)と本番環境(Production Keys)の２種類があります
 */

//セキュリティートークン
$token = '';

//DevID
define('devID', '');

//AppID
define('appID', '');

//CertID
define('certID', '');

/**
 * RuName は[application settings] -> [Customize the eBay User Consent Form] -> [Manage Your RuNames] から取得できます
 */
$eBayRuName = '';

/**
 * eBayの開発者アカウントにリンクしているPayPalのEMailアドレス
 *
 * リンクの方法は、http://www.sandbox.ebay.com/ にログインし、
 * [My eBay] -> [Account] -> [PayPal Account] -> [Link My PayPal Account] でリンクします
 */
$eBayPayPalEmail = '';

/**********************************************************/

/***********************************************************
 * PayPal 関連の設定
 *
 * PayPalのサイトから取得します
 *  http://www.paypal.com/ 
 *
 * [個人設定] -> [販売ツール] -> [APIアクセス] -> [更新] -> [API署名の表示]
 */

$gwPayPalAPIURL = 'https://api-3t.sandbox.paypal.com/nvp';
//$gwPayPalAPIURL = 'https://api-3t.paypal.com/nvp'; //本番用

//PayPalのID
$PayPalID = '';

//PayPalのパスワード
$PayPalPW = '';

//PayPalのSignature
$PayPalSignature = '';
/**********************************************************/

/**
 * UUID の生成
 */
function GetUUID($prefix = '', $no_hyphen = false)
{
  $chars = md5(uniqid(mt_rand(), true));

  if ($no_hyphen) return $prefix.strtoupper($chars);

  $uuid  = substr($chars,0,8).'-';
  $uuid .= substr($chars,8,4).'-';
  $uuid .= substr($chars,12,4).'-';
  $uuid .= substr($chars,16,4).'-';
  $uuid .= substr($chars,20,12);

  return $prefix.strtoupper($uuid);
}

/**
 * ヘッダを生成します。
 *
 * 設定内容はMyeBayから取得します。
 * テスト環境と本番環境で設定値が異なります。
 *
 */
function createHeaders($callName, $siteID = 0)
{
  return array(
    "X-EBAY-API-COMPATIBILITY-LEVEL: " . TradingSchemaVer,
     "X-EBAY-API-DEV-NAME: " . devID,
     "X-EBAY-API-APP-NAME: " . appID,
     "X-EBAY-API-CERT-NAME: " . certID,
     "X-EBAY-API-CALL-NAME: ".$callName,
     "X-EBAY-API-SITEID: ".$siteID
  );
}

/**
 * LMS呼出用のヘッダを生成します
 * (File Transfer Service 以外)
 *
 */
function createHeadersLMS($svcName, $opName, $token)
{
  return array(
    "CONTENT-TYPE: XML",
    "X-EBAY-SOA-OPERATION-NAME: " . $opName,
    "X-EBAY-SOA-SERVICE-NAME: " . $svcName,
    "X-EBAY-SOA-SECURITY-TOKEN: " . $token,
    "X-EBAY-SOA-SERVICE-VERSION: 1.4.1"
  );
}

/**
 * LMS File Transfer Serivice 呼出用のヘッダを生成します
 *
 */
function createHeadersLMSFtrans($svcName, $opName, $token, $contentType='', $c_len=0)
{
  $arr = array(
    "X-EBAY-SOA-OPERATION-NAME: $opName",
    "X-EBAY-SOA-SERVICE-NAME: $svcName",
    "X-EBAY-SOA-SECURITY-TOKEN: $token",
    "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
    "X-EBAY-SOA-RESPONSE-DATA-FORMAT: XML",
    "X-EBAY-SOA-SERVICE-VERSION: 1.1.0",
  );

  if (strlen($contentType) > 0) $arr[] = "CONTENT-TYPE: $contentType";
  if ($c_len > 0) $arr[] = "Content-Length: $c_len";

  return $arr;
}

function createHeadersPayPal()
{
  return array(
    "X-PAYPAL-RESPONSE-DATA-FORMAT:" . "json"
  );
}

/**
 * 処理を実行し、結果を取得します
 *
 */
function makeRequest($url, $hdrs, $body)
{
  global $token;
  global $eBayPayPalEmail;

  $arr1 = array('###TOKEN###', '###TRADING_VER###', '###PAYPAL_EMAIL###');
  $arr2 = array($token, TradingSchemaVer, $eBayPayPalEmail);
  $body = str_replace($arr1, $arr2, $body);

  $ch = curl_init();
  $res= curl_setopt ($ch, CURLOPT_URL, $url);

  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $res = curl_exec($ch);
  curl_close ($ch);

  return $res;
}

function getGMTime($str)
{
  if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}).+Z$/', $str, $m) === 1) { 
    return gmmktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]);
  } 
  return null;
}

/**
 * eBayの時刻を取得します
 *
 */
function getEBayTime($url, $token)
{
  //ヘッダ用データを生成します
  $hdrs = createHeaders('GeteBayOfficialTime');

  $body = '<?xml version="1.0" encoding="utf-8"?>'.
    '<GeteBayOfficialTimeRequest xmlns="urn:ebay:apis:eBLBaseComponents">'.
    '<RequesterCredentials>'.
    "<eBayAuthToken>$token</eBayAuthToken>".
    '</RequesterCredentials>'.
    '</GeteBayOfficialTimeRequest>'; 

  //処理を実行し、結果を取得します。
  $res = makeRequest($url, $hdrs, $body);

  //結果のXMLをPHPのXMLオブジェクトとして取り出します。
  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->Ack == 'Success')
  {
    return $xml->Timestamp;
  }

  return null; 
}

?>
