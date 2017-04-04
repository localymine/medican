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


$query = "SELECT * FROM products_demo";

$keydata = mysqli_query($con,$query);
$skudb   = array();

//$row = $keydata->fetch_assoc();


while($row = $keydata->fetch_assoc()) {
   // $shop = $row['shopify_shop'];
	$sku = preg_replace('/\s+/','',$row['sku']); 
    
    array_push($skudb, $row['sku']);
}
if(!empty($skudb)){

	array_push($skudb,"BSW-127380");
}

// echo "<pre>";
// print_r($skudb);
// die;
$sc = new ShopifyClient($shopify_shop,$shopify_token,$appkey,$secretkey);


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



$query = "SELECT * FROM products";

$keydata = mysqli_query($con,$query);


//$rows = $keydata->fetch_assoc();


$i = 0;
$skulist = array();

$imagesarr = array();
$allImages = array();

while($row = $keydata->fetch_assoc()) {

 
   $skunew = preg_replace('/\s+/', '', $row['abs']);
    if(in_array($skunew, $skudb)){

    }else{
        //array_push($skulist, $rs);

      // if(!empty($row['msrp']))
       $catelist  = array($row['msrp'],
                         $row['main_cat_1'],
                         $row['sub_cat_1'],
                         $row['main_cat_2'],
                         $row['sub_cat_2'],$row['main_cat_3'],$row['sub_cat_3'],
                         $row['main_cat_4']);
              
       $imagesarr = array($row['country_of_manufacture'],$row['image1'],$row['image2'],$row['image3'],$row['image4'],$row['image5']);
       $k = 0;  
      foreach($imagesarr as $img){
                    $allImages[$k]['src'] = $img;
                   $k++;
      }

      $price  =  2 * floatval($row['image6']);
      $optnsData =  array
                        ("title" => $row['sku'],
                         "price" =>$price,
                         "compare_at_price" => $row['wholesale_price'],
                         "sku" => $skunew,
                         "inventory_management"=>'shopify',
                         "inventory_quantity_adjustment"=>1,
                         "taxable" => 0,
                         "inventory_quantity" => 5,
                         "weight" =>$row['description'],
                         "weight_unit" =>'lb',
                         "old_inventory_quantity" =>1,
                         "requires_shipping" => 1,
                        );
        $product = array("product"=>array(
                    "title"=> $row['sku'],
                    "body_html"=>$row['disco'],
                    "vendor"=> $row['title'],
                    "product_type"=> $row['msrp'],
                    "tags"=>$row['manufacturer'],
                    "published_scope"=>"global",
                    "images"=> $allImages,
                    "variants"=>array($optnsData)
                  ));

        $productname =  '';
        $create =    $sc->call('POST','/admin/products.json',$product);
       
        if($create){
$query = "INSERT INTO products_demo(sku,productname,product_id) VALUES ('".$skunew."','".$productname."','".$create['id']."')";

          
         $con->query($query);
        }
          $result = array_filter($catelist); 
          
          foreach ($result as $key => $value) {
            # code...

            $string = preg_replace('/\s+/', '', $value);
            $key =  array_key_exists($string,$catlist);

            if($key){
                $collects = array("collect"=>array(
                                  "product_id"=> $create['id'],
                                  "collection_id"=>$catlist[$string]
                               ));
                    $res = $sc->call('POST','/admin/collects.json',$collects);
                }


          }

  }

    
  
$i++;


}







?>