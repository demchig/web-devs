<?php

/**
 * GetMyeBaySelling()を実行して出品予定一覧を取得する
 *
 */

include_once('comm.inc'); //共通ファイルを取り込みます。

  //ヘッダ用データを生成します
  $hdrs = createHeaders('GetMyeBaySelling');

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">'.
  "<RequesterCredentials><eBayAuthToken>$token</eBayAuthToken></RequesterCredentials>".
  ' <ScheduledList>'.
  '  <Include>true</Include>'.
  '  <Pagination>'.
  '   <EntriesPerPage>200</EntriesPerPage>'.
  '   <PageNumber>1</PageNumber>'.
  '  </Pagination>'.
  '  <Sort>StartTimeDescending</Sort>'.
  ' </ScheduledList>'.
  '</GetMyeBaySellingRequest>';

  //処理を実行し、結果を取得します。
  $res = makeRequest($gwTradingURL, $hdrs, $body);

  //結果のXMLをPHPのXMLオブジェクトとして取り出します。
  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->Ack == 'Success') 
  {
    //処理が成功した場合

    foreach($xml->ScheduledList->ItemArray->Item as $item)
    {
      printf("ItemID = %s\n", $item->ItemID);
      printf("Title = %s\n", $item->Title);
      printf("SKU = %s\n", $item->SKU);

      if (isset($item->ListingDetails))
        printf("StartTime = %s\n", $item->ListingDetails->StartTime);

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
 
    foreach ($xml->Errors->Error as $err) {
      //エラーメッセージの表示
      echo $err->ShortMessage."\n";
      echo $err->LongMessage."\n";
      echo $err->ErrorCode."\n";
    }
  }
?>
