<?php

/**
 * AddItems() のテストを実行する
 *
 */

include_once('comm.inc'); //共通ファイルを取り込みます。

  //ヘッダ用データを生成します
  $hdrs = createHeaders('AddItems');

  //入力データのXMLファイルを読み込みます。
  $body = file_get_contents('xml/case3.xml');

  //処理を実行し、結果を取得します。
  $res = makeRequest($gwTradingURL, $hdrs, $body);

  //結果のXMLをPHPのXMLオブジェクトとして取り出します。
  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->Ack == 'Success' || $xml->Ack=='PartialFailure') 
  {

    //全て成功または一部成功の場合
    if (isset($xml->AddItemResponseContainer))
    {
      //１件ずつ処理結果を確認
      foreach($xml->AddItemResponseContainer as $cnt) 
      {
        //リクエストで指定したMessageIDを取得
        echo "Correlation ID = $cnt->CorrelationID\n";

        if (isset($cnt->Errors)) 
        {
          //エラーの場合
          echo $cnt->Errors->ErrorClassification."\n";
          echo $cnt->Errors->ShortMessage."\n";
          echo $cnt->Errors->LongMessage."\n";
          echo $cnt->Errors->ErrorCode."\n";
          
        } 
        else 
        {
          //成功した場合

          //ItemIDを取得
          echo "ItemID = $cnt->ItemID\n";

          //料金(Fee)のリストが返るので列挙します。
          foreach($cnt->Fees->Fee as $f)
          {
            $name = $f->Name;
            $fee = $f->Fee;
            $attrs = $fee->attributes();
            //料金名, 通貨名, 料金
            printf("name=%s currency=%s amount=%f\n", $name, $attrs->currencyID, $fee);
          }
        }
      }
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
    echo $xml->Errors->ShortMessage."\n";
    echo $xml->Errors->LongMessage."\n";
    echo $xml->Errors->ErrorCode."\n";
  }
?>
