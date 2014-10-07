<?php
/**
 * LMS File Transfer Service用共通ファイル
 */

function extractUUID($xml)
{
  if (preg_match('/xop:Include xmlns:xop=".+" href="cid:(urn:uuid:[A-Z0-9]+)"/', $xml, $matches) === 1) return $matches[1];
  return null;
}

function parseForErrorMessage($response)
{
  $beginErrorMessage = strpos($response, '<?xml');
  $endErrorMessage = strpos($response, '</errorMessage>', $beginErrorMessage);
  $endErrorMessage += strlen('</errorMessage>');
		
  return substr($response, $beginErrorMessage, $endErrorMessage - $beginErrorMessage);
}

/**
 * レスポンスから、サービスのレスポンスXMLを抽出します。
 */
function parseForResponseXML($response) 
{
  $beginResponseXML = strpos($response, '<?xml');
		
  $endResponseXML = strpos($response, '</downloadFileResponse>',
  $beginResponseXML);
		
  //Assume a service level error and die.
  if($endResponseXML === FALSE) {
    $errorXML = parseForErrorMessage($response);
    PrintUtils::printXML($errorXML);
    die();
  }	
		
  $endResponseXML += strlen('</downloadFileResponse>');
		
  return substr($response, $beginResponseXML,
    $endResponseXML - $beginResponseXML);
}

/**
 * uuidを含む文字列 から Content-IDヘッダの位置を探し
 * その後につづくバイナリデータを抽出します
 *
 * [データ構造]
 *
 * Content-ID: <urn:uuid:....>CRLF
 * CRLF 
 * <バイナリデータ>CRLF 
 * ----MIMEBoundaryurn_uuid_.... 
 *
 */
function parseForFileBytes($response, $uuid, $file_size)
{
  /* バイナリデータの始点を取得 */
  //uuidから、対応するContent-IDヘッダの先頭に移動
  $contentId = 'Content-ID: <' . $uuid . '>';
  $sOffset = strpos($response, $contentId, 0);

  //そこからさらに,Content-IDヘッダと２つのCRLFを過ぎ
  //バイナリデータの先頭に移動
  $sOffset += strlen($contentId);
  $sOffset += 4;
		
  /* バイナリデータの終点を取得 */
  //バイナリデータの終わりとなるMIME boundary の先頭に移動
  $eOffset = strpos($response, '--MIMEBoundaryurn_uuid_', $sOffset);
  //1バイトと、1つのCRLF分戻り、バイナリデータの最後に移動
  $eOffset -= 3;
		
  return substr($response, $sOffset, $eOffset - $sOffset + 1);
}

function writeZipFile($bytes, $zipFilename)
{
  $handler = fopen($zipFilename, 'wb') 
    or die("Failed. Cannot Open $zipFilename to Write!</b></p>");
  fwrite($handler, $bytes);
  fclose($handler);
}

?>
