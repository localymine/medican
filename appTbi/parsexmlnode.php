<?php
ini_set('max_execution_time',300000);
header("Access-Control-Allow-Origin: *");
session_start();
error_reporting(0);
require 'shopify.php';
require 'db.php';
$con = mysqli_connect($servername,$username,$password,$dbname);
/* get all products data */
$query = "SELECT * FROM wandenvy where id = '3'";
$keydata = mysqli_query($con,$query);
while($row = $keydata->fetch_assoc()) {
   // $shop = $row['shopify_shop'];
    $shopify_shop = $row['shopify_shop'];
    $shopify_token = $row['shopify_token'];
}


$query = "SELECT * FROM products";

$keydata = mysqli_query($con,$query);
$skudb   = array();

$row = $keydata->fetch_assoc();


while($row = $keydata->fetch_assoc()) {
   // $shop = $row['shopify_shop'];
	$row['sku'] = preg_replace('/\s+/', '', $row['sku']); 
    
    array_push($skudb, $row['sku']);
}
array_push($skudb,"BSW-127380");



/////// get categories//////////////

$query = "SELECT * FROM products_demo_catslive";

$keydata = mysqli_query($con,$query);

$row = $keydata->fetch_assoc();

$catlist  = array();
while($row = $keydata->fetch_assoc()) {
   // $shop = $row['shopify_shop'];
     $key = preg_replace('/\s+/', '', $row['category_name']);    
  //;  array_push($catlist[$key], $row['category_name']);
    $catlist[$key] = $row['category_id'];

}
$catlist['Vibrators'] = "421149896";







$sc = new ShopifyClient($shopify_shop,$shopify_token,$appkey,$secretkey);

$xmlstr = get_xml_from_url('http://www.sextoydropshipping.com/feed/product-feed.xml');
$xmlobj = new SimpleXMLElement($xmlstr);
//$xmlobj = (array)$xmlobj;//optional

// $nodes = $data->xpath('//items/item/id[.="12437"]/parent::*');
// $result = $nodes[0];

echo "<pre>";
print_r($xmlobj);
die;







?>