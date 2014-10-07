<?php

include_once('../comm.inc');
include_once('ftrans.inc');

  //JOB IDを指定します
  $taskRefId = $argv[1];

  //JOB IDに対応するファイル参照番号を指定します。
  //このIDはgetJobsまたはgetJobStatusでeBayが返したfileReferenceIdです。
  $fileRefId = $argv[2];

  //ダウンロードしたファイルの保存先
  $fileName = "./download/$taskRefId.zip";

  //downloadFileでヘッダの生成
  $hdrs = createHeadersLMSFtrans('FileTransferService', 'downloadFile', $token);

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<downloadFileRequest xmlns="http://www.ebay.com/marketplace/services">'.
  '<taskReferenceId>'.$taskRefId.'</taskReferenceId>'.
  '<fileReferenceId>'.$fileRefId.'</fileReferenceId>'.
  '</downloadFileRequest>';

  //データの送信
  $res = makeRequest($gwLMSFtransURL, $hdrs, $body);

  //レスポンスからXMLの箇所を抜き出し,SimpleXMLに変換します
  $xml_str = parseForResponseXML($res);

  if (($xml = simplexml_load_string($xml_str)) === FALSE) return;

  if ($xml->ack == 'Success')
  {
    //処理が成功した場合

    //レスポンスからZIPファイルのバイナリデータを抽出します
    $uuid = extractUUID($xml_str);
    $fileBytes = parseForFileBytes($res, $uuid);
    
    //バイナリデータをファイルに保存します。
    writeZipFile($fileBytes, $fileName);

    echo "Output file name is $fileName\n";
  }
  else
  {
    //エラーメッセージの表示
    print_r($xml);
  }

?>
