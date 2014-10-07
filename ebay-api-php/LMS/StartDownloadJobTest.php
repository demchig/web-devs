<?php

include_once('../comm.inc');

  //$jobType = 'ActiveInventoryReport';
  $jobType = 'SoldReport';

  //UUIDを生成します
  $uuid = GetUUID();

  //startUploadJob でヘッダを生成
  $hdrs = createHeadersLMS('BulkDataExchangeService', 'startDownloadJob', $token);

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<startDownloadJobRequest xmlns="http://www.ebay.com/marketplace/services">'.
  '<downloadJobType>'.$jobType.'</downloadJobType>'.
  '<UUID>'.$uuid.'</UUID>'.
  '</startDownloadJobRequest>';

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
