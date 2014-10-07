<?php

include_once('../comm.inc');

  /**
   *送信するJOBの種類を設定します
   */ 
  //$jobType = 'AddItem';
  $jobType = 'AddFixedPriceItem';
  //$jobType = 'ReviseItem';
  //$jobType = 'EndItem';

  //UUIDを生成します
  $uuid = GetUUID(); 

  //createUploadJobでヘッダを生成します
  $hdrs = createHeadersLMS('BulkDataExchangeService', 'createUploadJob', $token);

  //送信データを生成します
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<createUploadJobRequest xmlns="http://www.ebay.com/marketplace/services">'.
  '<uploadJobType>'.$jobType.'</uploadJobType>'.
  '<UUID>'.$uuid.'</UUID>'.
  '<fileType>XML</fileType>'.
  '</createUploadJobRequest>';

  //データを送信します
  $res = makeRequest($gwLMSBulkURL, $hdrs, $body);

  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->ack == 'Success')
  {
    //処理が成功した場合

    /**
     * JOB番号, ファイル参照番号, 最大ファイルサイズを表示
     */
    echo "jobId=$xml->jobId\n";
    echo "fileReferenceId=$xml->fileReferenceId\n";
    echo "maxFileSize=$xml->maxFileSize\n";
  }
  else
  {
    print_r($xml);
  }

?>
