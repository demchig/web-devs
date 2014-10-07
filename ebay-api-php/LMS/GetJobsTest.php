<?php

include_once('../comm.inc');

  //getJobs でヘッダを生成します
  $hdrs = createHeadersLMS('BulkDataExchangeService', 'getJobs', $token);

  //FROMとTOの２つの時間を取得します。
  $t_fm = time() - 60 * 60 * 1; //2時間前の時刻
  $t_to = time();//現在の時刻

  //時間を文字列に変換します
  $gm_fm = gmdate('Y-m-d\TH:i:s.000\Z', $t_fm);
  $gm_to = gmdate('Y-m-d\TH:i:s.000\Z', $t_to);

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<getJobsRequest xmlns="http://www.ebay.com/marketplace/services">'.
  '<fileType>XML</fileType>'.
  '<creationTimeFrom>'.$gm_fm.'</creationTimeFrom>'.
  '<creationTimeTo>'.$gm_to.'</creationTimeTo>'.
  '</getJobsRequest>';

  //データの送信
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
