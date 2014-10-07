<?php

/**
 * 取得したトークンを表示する画面
 *
 * auth.php からGETで呼び出される
 */
include_once('comm.inc');

  //パラメータからeBay session idを取得
  $s_id = $_GET['s_id'];

  //ヘッダ情報の生成
  $hdrs = createHeaders('FetchToken');

  //入力XMLデータの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">'.
  '<RequesterCredentials>'.
  "<eBayAuthToken>$token</eBayAuthToken>".
  '</RequesterCredentials>'.
  "<SessionID>$s_id</SessionID>".
  '</FetchTokenRequest>';

  //処理の実行と結果の取得
  $res = makeRequest($gwTradingURL, $hdrs, $body);

  if (($xml = simplexml_load_string($res)) !== FALSE) {
    if ($xml->Ack == 'Failure') {

      //処理が失敗
      print_r($xml);
    } else {

      //処理成功、トークンを取得
      $target_token = $xml->eBayAuthToken;
      $msg .= 'Fetched token is : ' . $target_token;
    }
  }
?>

<html>
<head>
<title>Auth And Auth</title>
</head>

<body>
<p><?echo $msg; ?></p>
</body>
</html>
