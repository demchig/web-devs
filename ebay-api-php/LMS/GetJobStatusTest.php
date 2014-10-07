<?php

include_once('../comm.inc');

  //JOB IDを指定します
  $jobId = '50002635234'; 

  //getJobStatusでヘッダを生成します
  $hdrs = createHeadersLMS('BulkDataExchangeService', 'getJobStatus', $token);

  //送信データを生成します
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<getJobStatusRequest xmlns="http://www.ebay.com/marketplace/services">'.
  '<jobId>'.$jobId.'</jobId>'.
  '</getJobStatusRequest>';

  //データを送信します
  $res = makeRequest($gwLMSBulkURL, $hdrs, $body);

  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->ack == 'Success')
  {
    //処理が成功した場合

    foreach ($xml->jobProfile as $prof) {
      /**
       * JOB番号, ファイル参照番号, 最大ファイルサイズを表示
       */
      echo "jobId=$prof->jobId\n";
      echo "jobType=$prof->jobType\n";
      echo "jobStatus=$prof->jobStatus\n";
      echo "fileReferenceId=$prof->fileReferenceId\n";
      echo "inputFileReferenceId=$prof->inputFileReferenceId\n";
      echo "creationTime=$prof->creationTime\n";
      echo "percentComplete=$prof->percentComplete\n";
      echo "completionTime=$prof->completionTime\n";
      echo "errorCount=$prof->errorCount\n";
      echo "\n";
    }
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
