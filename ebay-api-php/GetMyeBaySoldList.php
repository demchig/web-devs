<?php

/**
 * GetMyeBaySelling()を実行して落札一覧を取得する
 *
 */

include_once('comm.inc'); //共通ファイルを取り込みます。

  //過去60日間の落札一覧を取得します。
  $days = 60; //最大値60

  //ヘッダ用データを生成します
  $hdrs = createHeaders('GetMyeBaySelling');

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">'.
  "<RequesterCredentials><eBayAuthToken>$token</eBayAuthToken></RequesterCredentials>".
  ' <SoldList>'.
  "  <DurationInDays>$days</DurationInDays>".
  '  <Include>true</Include>'.
  '  <Pagination>'.
  '   <EntriesPerPage>200</EntriesPerPage>'.
  '   <PageNumber>1</PageNumber>'.
  '  </Pagination>'.
  '  <Sort>EndTimeDescending</Sort>'.
  ' </SoldList>'.
  '</GetMyeBaySellingRequest>';

  //処理を実行し、結果を取得します。
  $res = makeRequest($gwTradingURL, $hdrs, $body);

  //結果のXMLをPHPのXMLオブジェクトとして取り出します。
  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->Ack == 'Success') 
  {
    //処理が成功した場合

//print_r($xml);
    foreach($xml->SoldList->OrderTransactionArray->OrderTransaction as $otran)
    {
      if (isset($otran->Order)) {
        echo "[Order]\n";
        printf("OrderID = %s\n", $otran->Order->OrderID);

        foreach($otran->Order->TransactionArray->Transaction as $tran)
        {
          printf("ItemID = %s\n", $tran->Item->ItemID); 
          printf("SKU = %s\n", $tran->Item->SKU); 
          printf("Title = %s\n", $tran->Item->Title); 
          printf("TransactionID = %s\n", $tran->TransactionID); 
          printf("TotalTransactionPrice = %s\n", $tran->TotalTransactionPrice); 
          printf("SippingServiceCost = %s\n", 
            $tran->Item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost); 

          printf("PaidTime = %s\n", $tran->PaidTime); 
        }
        echo "\n";
      } else if (isset($otran->Transaction)) {
        echo "[Transaction]\n";
        $tran = $otran->Transaction;

        printf("ItemID = %s\n", $tran->Item->ItemID); 
        printf("SKU = %s\n", $tran->Item->SKU); 
        printf("Title = %s\n", $tran->Item->Title); 
        printf("TransactionID = %s\n", $tran->TransactionID); 
        printf("TotalTransactionPrice = %s\n", $tran->TotalTransactionPrice); 
        printf("SippingServiceCost = %s\n", 
          $tran->Item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost); 
        echo "\n";
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
 
    foreach ($xml->Errors->Error as $err) {
      //エラーメッセージの表示
      echo $err->ShortMessage."\n";
      echo $err->LongMessage."\n";
      echo $err->ErrorCode."\n";
    }
  }
?>
