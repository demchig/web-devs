<?php

/**
 * トークン取得テスト用画面
 *
 * この画面からスタートします
 */

include_once('comm.inc'); //共通ファイルの読込

  //ヘッダ情報の生成
  $hdrs = createHeaders('GetSessionID');

  //XMLデータの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">'.
  '<RequesterCredentials>'.
  "<eBayAuthToken>$token</eBayAuthToken>".
  '</RequesterCredentials>'.
  "<RuName>$eBayRuName</RuName>".
  '</GetSessionIDRequest>';

  //処理の実行と結果の取得
  $res = makeRequest($gwTradingURL, $hdrs, $body);

  if (($xml = simplexml_load_string($res)) === FALSE) return;

  //セッションIDの取得(後にGETのパラメータに設定)
  $s_id =  $xml->SessionID;

  //fetch.phpのURLの設定
  //環境に合わせて変更します
  $fetch_url = "/token-test/fetch.php?s_id=$s_id";

?>

<html>
<head>
<title>Auth And Auth Demo</title>
</head>
<body>
<form>
<!-- eBay認証画面をポップアップする -->
<p><span>1.</span><input TYPE="button" NAME="AUTHORIZE" VALUE="Launch Auth & Auth" onclick="window.open('https://<? echo $auth_svr ?>/ws/eBayISAPI.dll?SignIn&runame=<? echo $eBayRuName; ?>&SessID=<? echo $s_id; ?>');"/></p>

<!-- トークン取得画面をポップアップする -->
<p><span>2.</span><input TYPE="button" NAME="fetch" VALUE="Fetch token" onclick="window.open('<? echo $fetch_url; ?>');"/></p>
</form>
</body>
</html>
