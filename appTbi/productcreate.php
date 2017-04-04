<?php 

header("Access-Control-Allow-Origin: *");
session_start();
error_reporting(0);
require 'shopify.php';
require 'db.php';
$con = mysqli_connect($servername,$username,$password,$dbname);
/* get all products data */
$query = "SELECT * FROM wandenvy where id = '1'";


$keydata = mysqli_query($con,$query);
while($row = $keydata->fetch_assoc()) {
   // $shop = $row['shopify_shop'];
    $shopify_shop = $row['shopify_shop'];
    $shopify_token = $row['shopify_token'];
}

$sc = new ShopifyClient($shopify_shop,$shopify_token,$appkey,$secretkey);

$xmlstr = get_xml_from_url('http://www.sextoydropshipping.com/feed/product-feed.xml');
$xmlobj = new SimpleXMLElement($xmlstr);
$xmlobj = (array)$xmlobj;//optional

//$arr = json_decode( json_encode($xmlobj) , 1);






function get_xml_from_url($url){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    $xmlstr = curl_exec($ch);
    curl_close($ch);

    return $xmlstr;
}

function xml2array($xml)
{
    $arr = array();

    foreach ($xml as $element)
    {
        $tag = $element->getName();
        $e = get_object_vars($element);
        if (!empty($e))
        {
            $arr[$tag] = $element instanceof SimpleXMLElement ? xml2array($element) : $e;
        }
        else
        {
            $arr[$tag] = trim($element);
        }
    }

    return $arr;
}

function xml_attribute($object, $attribute)
{
    if(isset($object[$attribute]))
        return (string) $object[$attribute];
}


$query= array(); 

// DELETE /admin/products/#{id}.json



foreach ($xmlobj['item'] as $key => $value) {
        
        $product_type =  xml_attribute($value->specifications,'name');
        # code...
        $value = xml2array($value);
        $cats = xml2array($value->categories);
       
        $img   =  $value['image1'];
        $allImages = array(
           array(
                "src"=> $img
               )
            );
        $optnsData =  array
                        ("title" => $value['title'],
                         "price" => $value['msrp'],
                         "sku" => $value['sku'],
                         "inventory_management"=>'shopify',
                         "inventory_quantity_adjustment"=>1,
                         "taxable" => 0,
                         "inventory_quantity" => 0,
                         "weight" =>$value['weight'],
                         "weight_unit" =>'lb',
                         "old_inventory_quantity" =>1,
                         "requires_shipping" => 1,
                        );
        $product = array("product"=>array(
                    "title"=> $value['title'],
                    "body_html"=>$value['description'],
                    "vendor"=> $value['manufacturer'],
                    "product_type"=> $product_type,
                    "tags"=>$value['brand'],
                    "published_scope"=>"global",
                    "images"=> $allImages,
                    "variants"=>array($optnsData)
                  ));
   

   
        $sc     = new ShopifyClient($shopify_shop,$shopify_token,$appkey,$secretkey);
        $create =    $sc->call('POST','/admin/products.json',$product);
    if($create){
     $query = "INSERT INTO `products` (`sku`, `productname`, `shop_product_id`) VALUES ('".$value['sku']."', '".$create['title']."', '".$create['id']."')";
    
     $con->query($query);
     }


     // $query[] = '("'.mysql_real_escape_string($value['title']).'",'.$data["categories"]["category"].','.$create['id'].'")';
     


    
}

 $res = " INSERT INTO products_demo(`name`,`category`, `product_id`) VALUES ".implode(',', $query);
 $con->query($res);
 echo "done";
 die;    




   












?>