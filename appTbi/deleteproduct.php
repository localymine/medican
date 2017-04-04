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
$sc = new ShopifyClient($shopify_shop,$shopify_token,$appkey,$secretkey);
$allcustomers = $shopName = $error =  array();
$customer_count = $sc->call('GET','/admin/products/count.json');

echo "<pre>";
print_r($customer_count);
die;
$deletedproducts   = array();
$query = "SELECT * FROM deletedproducts";
$keydata = mysqli_query($con,$query);
$row = $keydata->fetch_assoc();

while($row = $keydata->fetch_assoc()) {
    $productid = preg_replace('/\s+/', '', $row['productid']); 
    array_push($deletedproducts, $productid);
}

foreach ($deletedproducts as $key => $value) {
	    $delete = $sc->call('DELETE','/admin/products/'.$value.'.json');
}
die('done');


