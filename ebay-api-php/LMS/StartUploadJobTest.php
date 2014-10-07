<?php

include_once('../comm.inc');

  //スタートするJOB IDをコマンドラインの引数で指定
  $jobId = $argv[1];

  //startUploadJob でヘッダを生成
  $hdrs = createHeadersLMS('BulkDataExchangeService', 'startUploadJob', $token);

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<startUploadJobRequest xmlns="http://www.ebay.com/marketplace/services">'.
  '<jobId>'.$jobId.'</jobId>'.
  '</startUploadJobRequest>';

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
    //エラーメッセージの表示
    print_r($xml);
  }
?>
