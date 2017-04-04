<?php 
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


$query = "SELECT * FROM products_demo_catslive";

$keydata = mysqli_query($con,$query);

$row = $keydata->fetch_assoc();

$catlist  = array();
while($row = $keydata->fetch_assoc()) {
   // $shop = $row['shopify_shop'];
     //$key = preg_replace('/\s+/', '', $row['category_name']);    
  //;  array_push($catlist[$key], $row['category_name']);
    // $catlist = $row['category_name'];
    array_push($catlist, $row['category_name']);
}
array_push($catlist, 'Vibrators');
//$catlist['Vibrators'] = "421149896";


$sc = new ShopifyClient($shopify_shop,$shopify_token,$appkey,$secretkey);

   $custom_collection = array("custom_collection"=>array(
                                                     "title"=>"Men's Wear",
                                     ));
       $res = $sc->call('POST','/admin/custom_collections.json',$custom_collection);

       $res2 = "INSERT INTO products_demo_catslive(`category_id`,`category_name`) VALUES('".$res['id']."', '".$value."')";

       $con->query($res2);



// $xmlstr = get_xml_from_url('http://www.sextoydropshipping.com/feed/product-feed.xml');
// $xmlobj = new SimpleXMLElement($xmlstr);
// $xmlobj = (array)$xmlobj;//optional

// //$arr = json_decode( json_encode($xmlobj) , 1);
// function get_xml_from_url($url){
//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

//     $xmlstr = curl_exec($ch);
//     curl_close($ch);

//     return $xmlstr;
// }

// function xml2array($xml)
// {
//     $arr = array();

//     foreach ($xml as $element)
//     {
//         $tag = $element->getName();
//         $e = get_object_vars($element);
//         if (!empty($e))
//         {
//             $arr[$tag] = $element instanceof SimpleXMLElement ? xml2array($element) : $e;
//         }
//         else
//         {
//             $arr[$tag] = trim($element);
//         }
//     }

//     return $arr;
// }

// function xml_attribute($object, $attribute)
// {
//     if(isset($object[$attribute]))
//         return (string) $object[$attribute];
// }

//  $livecatlist = array(); 
// foreach ($xmlobj['item'] as $key => $value) {
// 	  foreach ($value->categories->category as $key => $value) {
// 			# code... 
			 
// 			  $cat  =  explode(">", $value);
// 			  if(!empty($cat)){
// 				  if(!empty($cat[0])){
// 					array_push($livecatlist, $cat[0]);
// 				  }
// 				  if(!empty($cat[1])){
// 				  	array_push($livecatlist, $cat[1]);
// 				  }
// 			 }else{
// 			 	array_push($livecatlist,$value);
// 			 } 
//         }

// }




// //echo "<pre>";
// $finalcatlist = array_unique($catlist);
//  //print_r($finalcatlist);
//  //die;
// $query2= array(); 
// if(!empty($finalcatlist)){
//   $i = 0;
//   foreach ($finalcatlist as $key => $value) {
//  	# code...

//  	if(in_array($value,$catlist)){


//  	}else{

//  		echo $i."--->".$value;
//  		die;
//  	}

//  	//echo $value;
//     // if(!empty($value)){
//  	  //   $custom_collection = array("custom_collection"=>array(
//     //                                                  "title"=> $value,
//     //                                  ));
//     //    $res = $sc->call('POST','/admin/custom_collections.json',$custom_collection);

//     //    $res2 = "INSERT INTO products_demo_cats(`category_id`,`category_name`) VALUES('".$res['id']."', '".$value."')";

//     //    $con->query($res2);

//     //   }
//     $i++;    
//    }


// }


// function createcategory($value){
// 	    $list = array();
// 		foreach ($value->categories->category as $key => $value) {
// 			# code... 
// 			  //$ca = xml2array($value);
// 			  $cat  =  explode(">", $value);
// 			  if(!empty($cat)){
// 				  if(!empty($cat[0])){
// 					array_push($list, $cat[0]);
// 				  }
// 				  if(!empty($cat[1])){
// 				  	array_push($list, $cat[1]);
// 				  }
// 			 }else{
// 			 	array_push($list,$value);
// 			 } 
			  				


// 	}
// 	$finalcat = array_unique($list);
// 	return  $finalcat;

// }
//  echo "done";
//  die;    

 
//  die('done');

// foreach ($finalcatlist as $key => $value) {
// 	echo "------>".$value;
// 	die;
// 	# code...
// 	/  $custom_collection = array("custom_collection"=>array(
//                                                      "title"=> $value,
//                                     ));
//      $res = $sc->call('POST','/admin/custom_collections.json',$custom_collection);
//      echo "<pre>";
//      print_r($res);
//      die;

// }

//     $res = $sc->call('GET','/admin/custom_collections.json');

//      echo "<pre>";
//      print_r($res);
//      die;

// die;



 // $custom_collection = array("custom_collection"=>array(
  //                                                    "title"=> $data["categories"]["category"],
  //                                   ));
  //    $res = $sc->call('POST','/admin/custom_collections.json',$custom_collection);
  //    echo "<pre>";
  //    print_r($res);
  //    die;

  
    //$sql[] = '("'.mysql_real_escape_string($row['first']).'", '.$row['sec'].')';


//mysql_query('INSERT INTO table (id, name) VALUES '.implode(',', $sql));






// GET /admin/custom_collections/count.json


// $optnsData =  array
//                         ("title" => $value['title'],
//                          "price" => $value['msrp'],
//                          "sku" => $value['sku'],
//                          "inventory_management"=>'shopify',
//                          "inventory_quantity_adjustment"=>1,
//                          "taxable" => 0,
//                          "inventory_quantity" => 0,
//                          "weight" =>$value['weight'],
//                          "weight_unit" =>'lb',
//                          "old_inventory_quantity" =>1,
//                          "requires_shipping" => 1,
//                         );
//         $product = array("product"=>array(
//                     "title"=> $value['title'],
//                     "body_html"=>$value['description'],
//                     "vendor"=> $value['manufacturer'],
//                     "product_type"=> $product_type,
//                     "tags"=>$value['brand'],
//                     "published_scope"=>"global",
//                     "images"=> $allImages,
//                     "variants"=>array($optnsData)
//                   ));




//             "parameters" => array(
//                 "custom_collection" => array(
//                     "location" => "json",
//                     "parameters" => array(
//                         "body_html" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "handle" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "image" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "metafield" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "metafields" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "published" => array(
//                             "type" => "boolean",
//                             "location" => "json",
//                         ),
//                         "published_at" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "published_scope" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "sort_order" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "template_suffix" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "title" => array(
//                             "type" => "boolean",
//                             "location" => "json",
//                         ),
//                         "collects" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         ),
//                         "image" => array(
//                             "type" => "string",
//                             "location" => "json",
//                         )
//                     )

// $csvData = file_get_contents('http://www.sextoydropshipping.com/feed/product-feed.csv');
// $lines = explode(PHP_EOL, $csvData);
// $array = array();
// foreach ($lines as $line) {
//     $array[] = str_getcsv($line);
// }
// echo "<pre>";
// print_r($array);
// die;



// $xmlstr = get_xml_from_url('http://www.sextoydropshipping.com/feed/product-feed.xml');
// $xmlobj = new SimpleXMLElement($xmlstr);
// $xmlobj = (array)$xmlobj;//optional

// //$arr = json_decode( json_encode($xmlobj) , 1);






// function get_xml_from_url($url){
//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

//     $xmlstr = curl_exec($ch);
//     curl_close($ch);

//     return $xmlstr;
// }

// function xml2array($xml)
// {
//     $arr = array();

//     foreach ($xml as $element)
//     {
//         $tag = $element->getName();
//         $e = get_object_vars($element);
//         if (!empty($e))
//         {
//             $arr[$tag] = $element instanceof SimpleXMLElement ? xml2array($element) : $e;
//         }
//         else
//         {
//             $arr[$tag] = trim($element);
//         }
//     }

//     return $arr;
// }

// function xml_attribute($object, $attribute)
// {
//     if(isset($object[$attribute]))
//         return (string) $object[$attribute];
// }


// foreach ($xmlobj['item'] as $key => $value) {

//      $value = xml2array($value);
       
//         $img   =  $value['image1'];
//         $allImages = array(
//            array(
//                 "src"=> $img
//                )
//             );



// }


?>
