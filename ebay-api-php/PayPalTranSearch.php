<?php
/**
 * PayPalのTransactionSearch()を呼出し、過去7日分のトランザクションを
 * 取得する
 */

include_once('comm.inc');

  $t_fm = time() - 60 * 2 - 60 * 60 * 24 * 7;
  $t_to = time() - 60 * 2;

  $gm_fm = gmdate('Y-m-d\TH:i:s.000\Z', $t_fm);
  $gm_to = gmdate('Y-m-d\TH:i:s.000\Z', $t_to);

  //送信データの生成
  $body = "USER=$PayPalID".
    "&PWD=$PayPalPW".
    "&SIGNATURE=$PayPalSignature".
    "&METHOD=TransactionSearch".
    "&STARTDATE=$gm_fm".
    "&ENDDATE=$gm_to".
    "&STATUS=Success".
    "&VERSION=98";

  //データの送信
  $res = makeRequest($gwPayPalAPIURL, createHeadersPayPal(), $body);

  //レスポンスデータの解析
  $arr = explode('&', urldecode($res));

  $res_arr = array();   //トランザクションごとのレスポンスを格納
  $other_arr = array(); //全体のレスポンスを格納

  foreach ($arr as $item) 
  {
    if (preg_match('/^([A-Z_]+)(\d)=(.+)$/', $item, $m) === 1) 
    {
      /**
       * KEY_N=VALUE 形式のデータをNごとのグループに分ける
       * ここでNはトランザクションの識別番号とする
       */
      $idx = $m[2]; $key = $m[1]; $val = $m[3];

      if (!array_key_exists($idx, $res_arr)) 
        $res_arr[$idx] = array();

      $res_arr[$idx][$key] = $val; //グループごとの連想配列に格納 
    }
    else if (preg_match('/^([A-Z]+)=(.+)$/', $item, $m) === 1) 
    {
      /**
       * KEY=VALUE 形式のデータを連想配列に格納
       */
      $key = $m[1]; $val = $m[2];
      $other_arr[$key] = $val;
    }
  }

  //トランザクションごとのレスポンスを表示
  print_r($res_arr);

  //全体のレスポンスを表示
  print_r($other_arr);

?>

