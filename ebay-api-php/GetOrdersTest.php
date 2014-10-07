<?php

/**
 * GetOrders()を実行して
 * 過去30分の間に変更があった注文一覧を取得します
 *
 */

include_once('comm.inc'); //共通ファイルを取り込みます。

  //eBayの公式時間を取得します
  $day_str = getEBayTime($gwTradingURL, $token); 

  //過去30分間に変更があった落札一覧を取得します。
  //2分は処理中の注文を除外するためのオフセットです
  $t_fm = getGMTime($day_str) - 60 * 2 - 60 * 30;
  $t_to = getGMTime($day_str) - 60 * 2; 

  $gm_fm = gmdate('Y-m-d\TH:i:s.000\Z', $t_fm);
  $gm_to = gmdate('Y-m-d\TH:i:s.000\Z', $t_to);

  //ヘッダ用データを生成します
  $hdrs = createHeaders('GetOrders');

  //送信データの生成
  $body = '<?xml version="1.0" encoding="utf-8"?>'.
  '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">'.
  '<RequesterCredentials>'.
  "<eBayAuthToken>$token</eBayAuthToken>".
  '</RequesterCredentials>'.
  "<ModTimeFrom>$gm_fm</ModTimeFrom>".
  "<ModTimeTo>$gm_to</ModTimeTo>".
  '<DetailLevel>ReturnAll</DetailLevel>'.
  '<Pagination>'.
  '<EntriesPerPage>100</EntriesPerPage>'.
  '<PageNumber>'. '1' . '</PageNumber>'.
  '</Pagination>'.
  '</GetOrdersRequest>';

  //処理を実行し、結果を取得します。
  $res = makeRequest($gwTradingURL, $hdrs, $body);

  //結果のXMLをPHPのXMLオブジェクトとして取り出します。
  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->Ack == 'Success') 
  {
    //処理が成功した場合
    if (isset($xml->OrderArray->Order))
    {
      $p_items = array();
      $a_items = array();

      foreach($xml->OrderArray->Order as $odr)
      {
        if ($odr->CheckoutStatus->PaymentMethod == 'PayPal' &&
          $odr->CheckoutStatus->Status == 'Complete' &&
          $odr->CheckoutStatus->eBayPaymentStatus == 'NoPaymentFailure')
        {
          /* 入金済み注文 */
          if (isset($odr->TransactionArray->Transaction)) {
            foreach ($odr->TransactionArray->Transaction as $tran) {
              //$p_items[] = $tran->Item;
              $p_items[] = $tran;
            }
          }
        }
        else if ($odr->OrderStatus == 'Active')
        {
          if (isset($odr->TransactionArray->Transaction)) {
            foreach ($odr->TransactionArray->Transaction as $tran) {
              //$a_items[] = $tran->Item;
              $a_items[] = $tran;
            }
          }
        }
      }

      echo "[入金済み]\n";
      print_r($p_items);

      echo "\n";
      echo "[未入金]\n";
      print_r($a_items);
    }
    else {
      echo "Order not found\n";
    }
  } 
  else 
  {
    //処理が失敗した場合
    if ($xml->ErrorClassification == 'SystemError') //システムエラー
      echo "eBay system error\n";
    else
      echo "Input parameter error\n"; //その他のエラー
 
print_r($xml);
    foreach ($xml->Errors->Error as $err) {
      //エラーメッセージの表示
      echo $err->ShortMessage."\n";
      echo $err->LongMessage."\n";
      echo $err->ErrorCode."\n";
    }
  }
?>
