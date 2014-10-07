<?php

include_once('../comm.inc');
include_once('ftrans.inc');

  //JOB IDを指定します
  $taskRefId = '50002635994';

  //JOB IDに対応するファイル参照番号を指定します。
  //このIDはcreateUploadJob()でeBayが返したfileReferenceIdです。
  $fileRefId = '50002776244';

  //アップロードするファイルの指定
  //$fileName = '../xml/case13.xml';   //AddItem()用

  $fileName = '../xml/case14.xml'; //AddFixedPriceItem()用

  //$fileName = '../xml/case15.xml'; //ReviseItem()用

  //$fileName = '../xml/case16.xml'; //EndItem()用

  //MIMEにエンコードするための情報を取得
  $boundary = 'MIMEBoundaryurn_uuid_'; 
  $uuid = GetUUID('', true); 
  $uuid_req = GetUUID('', true); 
  $uuid_attach = GetUUID('', true); 

  //XMLファイルのデータをZIPにエンコードします
  $payload = gzencode(file_get_contents($fileName));
  $flen = strlen($payload);

  //送信データの生成
  $body = "--$boundary$uuid\r\n".
    "Content-Type: application/xop+xml; charset=UTF-8; type=\"text/xml; charset=UTF-8\"\r\n".
    "Content-Transfer-Encoding: binary\r\n".
    "Content-ID: <0.urn:uuid:$uuid_req>\r\n\r\n".

    "<?xml version=\"1.0\" encoding=\"utf-8\"?>".
    '<uploadFileRequest xmlns:sct="http://www.ebay.com/soaframework/common/types" xmlns="http://www.ebay.com/marketplace/services">'.
    "<taskReferenceId>$taskRefId</taskReferenceId>".
    "<fileReferenceId>$fileRefId</fileReferenceId>".
    '<fileFormat>gzip</fileFormat>'.
    "<fileAttachment><Size>$flen</Size><Data><xop:Include xmlns:xop=\"http://www.w3.org/2004/08/xop/include\" href=\"cid:urn:uuid:$uuid_attach\"/></Data></fileAttachment>".
    "</uploadFileRequest>\r\n".
    "--$boundary$uuid\r\n".
    "Content-Type: application/octet-stream\r\n".
    "Content-Transfer-Encoding: binary\r\n".
    "Content-ID: <urn:uuid:$uuid_attach>\r\n\r\n".

    $payload.
    "\r\n--$boundary$uuid--\r\n";

  //ヘッダ用のデータの生成
  $contentType = sprintf('multipart/related; boundary=MIMEBoundaryurn_uuid_%s;type="application/xop+xml";start="<0.urn:uuid:%s>";start-info="text/xml"', $uuid, $uuid_req);
  $c_len = strlen($body);

  //uploadFile でヘッダを生成
  $hdrs = createHeadersLMSFtrans(
    'FileTransferService', 'uploadFile', 
    $token, $contentType, $c_len);

  //データの送信
  $res = makeRequest($gwLMSFtransURL, $hdrs, $body);

  if (($xml = simplexml_load_string($res)) === FALSE) return;

  if ($xml->ack == 'Success')
  {

    //処理が成功した場合
    echo "Successfilly completed.\n";
  }
  else
  {
    //エラーメッセージの表示
    print_r($xml->error);
  }

?>
