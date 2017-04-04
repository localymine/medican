<?php 

session_start();

ini_set('display_errors',1);

require 'shopify.php';

require 'db.php';



$conn=new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$con = mysqli_connect($servername,$username,$password,$dbname);

if (mysqli_connect_errno())

{

  die( "Failed to connect to MySQL: " . mysqli_connect_error());

}

/* creating table for app data for different stores */

$query = "CREATE TABLE IF NOT EXISTS `$table_name` (

        `id` int(11) NOT NULL AUTO_INCREMENT,

        `shopify_shop` text,

        `shopify_token` text,

        `status` text,

        `code` text,

        `phone_number` text,

        `time` text,

        `confirm_url` text,

        `payment_id` text,

        `api_client_id` text,

        `payment_status` text,

        `created_at` text,

        `trial` text,

         PRIMARY KEY (`id`)

        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$con->query($query);

define('SHOPIFY_API_KEY',$appkey);

define('SHOPIFY_SECRET',$secretkey);

//$shopify_shop = 'product-customization-4.myshopify.com';

    //$shopify_token = 'a31004027299ef027786351727cad355';

/* Define requested scope (access rights) - checkout https://docs.shopify.com/api/authentication/oauth#scopes   */

define('SHOPIFY_SCOPE','write_products,read_products'); //eg: define('SHOPIFY_SCOPE','read_orders,write_orders');

//define('SHOPIFY_SCOPE','read_content,write_content,read_themes, write_themes, write_script_tags,read_script_tags,read_products,write_products,read_orders,write_orders,read_customers,write_customers');



$shopify_shop="";

$shopify_token = "";



$nm_rows2=$conn->query("SELECT * FROM `".$table_name."` WHERE `shopify_shop`='".$_GET['shop']."'")->fetch();



if ($nm_rows2 > 0)

{

    $shopify_shop = $nm_rows2['shopify_shop'];

    $shopify_token = $nm_rows2['shopify_token'];

}

else

{
    

    $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];

    

    $shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);

    $shopifyClient = new ShopifyClient($_GET['shop'], "", SHOPIFY_API_KEY, SHOPIFY_SECRET);

    $shopify_token = $shopifyClient->getAccessToken($_GET['code']);



    

    $shopify_shop = $_GET['shop'];

    $shopify_code = $_GET['code'];

        

    

    if(empty($shopify_token))

    {

        header("Location: " . $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, $pageURL));

        echo "<script type='text/javascript'> location.href='".$shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, $pageURL)."';</script>";

    }

    else

    {

        $query = "INSERT INTO `".$table_name."` (`shopify_token`, `shopify_shop`, `code`) VALUES ('".$shopify_token."', '".$shopify_shop."', '".$shopify_code."')";

        $conn->query($query);

    }

}







$shopify_shop = $shopify_shop;

$shopify_token = $shopify_token;

$sc = new ShopifyClient($shopify_shop,$shopify_token,$appkey,$secretkey);



/*** Shopify app installation and initialization ***/



if(!empty($shopify_token))

{

    /* check if hooks exist*/

$myhooks = array();



$checkhooks = $sc->call('GET', '/admin/webhooks.json');



foreach($checkhooks as $exist_hooks){

        $myhooks[] =  $exist_hooks['topic'];

}

/* create app uninstall create hook if not exist*/

if(!in_array("app/uninstalled", $myhooks)){

$uninstallhook = array

            (

                    "webhook"=>array

                    (

                     "topic"=>"app/uninstalled",

                     "address"=> $fileLoc."uninstall.php?domain=".$shopify_shop,

                     "format"=>"json"

                    )

            );



$hook4 = $sc->call('POST', '/admin/webhooks.json', $uninstallhook);

}



 /* get all script hooks */

$script_tgs = $sc->call('GET','/admin/script_tags.json');

 if(!empty($script_tgs)){

      foreach($script_tgs as $exist_scripts){

               $myscripts[] =  $exist_scripts['display_scope'];

       }

 }

/* add thankyou code hook start */

 if(!in_array("all", $myscripts)){

  $productJs = array

                 (

                     "script_tag"=>array

                     (

                     "event"=>"onload",

                     "src"=>$fileLoc.'js/custom_product.js',

                     "async"=>"false",

                     "display_scope"=>"all"

                     )

                 );

   $pJs = $sc->call('POST','/admin/script_tags.json',$productJs);

 }

// $product = $sc->call('GET','/admin/products/9772136004.json');

// echo "<pre>";print_r($product);

?>  

    <html>

        <head>

            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

            <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>

            <script type="text/javascript">

            ShopifyApp.init({

                apiKey: '<?php echo $appkey; ?>',

                shopOrigin: 'https://<?php echo $shopify_shop; ?>'

            });

            </script>

                

            <script type="text/javascript">

                ShopifyApp.ready(function(){

                ShopifyApp.Bar.initialize({

                icon: "<?php echo $fileLoc; ?>images/favicon.png",

                title: "Settings"

                });

                });

            </script>

        </head>

        <body>

                    <div>Welcome, this is product customization app.</div>

        </body>

    </html>

<?php

}

?>