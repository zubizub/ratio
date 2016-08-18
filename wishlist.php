#!/usr/bin/php
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
define("STOP_STATISTICS", true);
CModule::includeModule("sale");

//* получаем пользователей *//
$order = array('sort' => 'asc');
$tmp = 'sort'; 
$rsUsers = CUser::GetList($order, $tmp);
$aUsers = array();
while($rsUsers->NavNext(true,"u_")) :
        $aUsers[$u_ID]['NAME'] = $u_NAME.' '.$u_LAST_NAME;
        $aUsers[$u_ID]['EMAIL'] = $u_EMAIL;
endwhile;


?>

<?foreach ($aUsers as $key => $user) {


if ($arFUser = CSaleUser::GetList(array('USER_ID' => $key))){
         
        $arFilter = Array(
           "USER_ID" => $arFUser['ID'],
            ">=DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), 1, date("Y")))
        );

        $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
        while ($ar_sales = $db_sales->Fetch())
        {

           $dbBasketItems = CSaleBasket::GetList(
             array(
                  "NAME" => "ASC",
                  "ID" => "ASC"
                  ),
             array(

                "ORDER_ID" => $ar_sales['ID'],
                  ),
                false,
                false,
                array("ID", "PRODUCT_ID")
                        );
        while ($arItems = $dbBasketItems->Fetch())
        {
            $arBasketItems[$key][] = $arItems['PRODUCT_ID'];
        }


        }

       $dbBasketItems = CSaleBasket::GetList(
        array(
              "NAME" => "ASC",
              "ID" => "ASC"
           ),
        array(
              "FUSER_ID" => $arFUser['ID'],
              "LID" => SITE_ID,
              "ORDER_ID" => "NULL",
              "DELAY" => "Y" 
           ),
        false,
        false,
        array("ID", "DELAY", "PRODUCT_ID")
     );
     while ($arItems = $dbBasketItems->Fetch())
     {
      $arBasketItems[$key]['DELAY'][] = $arItems['PRODUCT_ID'];
     }
     $arBasketItems[$key]['USER'] = $user;
   }
}

foreach ($arBasketItems as $key => $items) {

  $wishlist[$key]['USER'] = $items['USER'];


    foreach ($items['DELAY'] as $delayed) {

      if (!in_array($delayed, $items)) {
          $wishlist[$key]['WISH_PRODUCT_ID'][] = $delayed;
      }
      
    }

}


foreach ($wishlist as $key => $wishes) {
  
  
  unset ($html);

    $html .= 'Добрый день, ' . $wishes['USER']['NAME'] . '.';
    $html .= 'В вашем вишлисте хранятся товары:'."\n";

    foreach ($wishes['WISH_PRODUCT_ID']  as $product) {
        
        $res = CIBlockElement::GetByID($product);
        if($ar_res = $res->GetNext()){
        $html .= $ar_res['NAME']."\n";
        }

     }

     mail($wishes['USER']['EMAIL'], "Ваш лист пожеланий", $html, "From: root@localhost", "-froot@localhost");


}
