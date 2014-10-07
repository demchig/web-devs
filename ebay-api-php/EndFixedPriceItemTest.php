<?php

/**
 * EndFixedPriceItemのテストを実行する
 *
 */

include_once('comm.inc'); //共通ファイルを取り込みます。

  //ヘッダ用データを生成します
  $hdrs = createHeaders('EndFixedPriceItem');

  //入力データのXMLファイルを読み込みます。
  $body = file_get_contents('xml/case12.xml');

  //処理を実行し、結果を取得します。
  $res = makeRequest($gwTradingURL, $hdrs, $body);

  //結果のXMLをPHPのXMLオブジェクトとして取り出します。
  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->Ack == 'Success') 
  {

    //処理が成功した場合
    echo "EndFixedPriceItem successfully completed.\n";
  } 
  else 
  {
    
    //処理が失敗した場合
    if ($xml->ErrorClassification == 'SystemError') //システムエラー
      echo "eBay system error\n";
    else
      echo "Input parameter error\n"; //その他のエラー
 
    //エラーメッセージの表示
    echo $xml->Errors->ShortMessage."\n";
    echo $xml->Errors->LongMessage."\n";
    echo $xml->Errors->ErrorCode."\n";
  }
?>
