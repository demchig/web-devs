<?php

include_once('../comm.inc');

  //JOB IDを設定します。
  $jobId = 50002901891;

  //aborJob でヘッダを生成します
  $hdrs = createHeadersLMS('BulkDataExchangeService', 'abortJob', $token);

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<abortJobRequest xmlns="http://www.ebay.com/marketplace/services">'.
  "<jobId>$jobId</jobId>".
  '</abortJobRequest>';

  //データの送信
  $res = makeRequest($gwLMSBulkURL, $hdrs, $body);

  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->ack == 'Success')
  {
    //処理が成功した場合  
    echo "Successfilly completed.\n";
  }
  else
  {
    //処理が失敗した場合
    if ($xml->ErrorClassification == 'SystemError') //システムエラー
      echo "eBay system error\n";
    else
      echo "Input parameter error\n"; //その他のエラー

    //エラーメッセージの表示
    print_r($xml->error);
  }
?>
