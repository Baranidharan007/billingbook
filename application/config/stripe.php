<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
/* 
| ------------------------------------------------------------------- 
|  Stripe API Configuration 
| ------------------------------------------------------------------- 
| 
| You will get the API keys from Developers panel of the Stripe account 
| Login to Stripe account (https://dashboard.stripe.com/) 
| and navigate to the Developers >> API keys page 
| 
|  stripe_api_key            string   Your Stripe API Secret key. 
|  stripe_publishable_key    string   Your Stripe API Publishable key. 
|  stripe_currency           string   Currency code. 
*/ 
$config['stripe_api_key']         = 'sk_test_51GaDAnB5TcGYIInBQ0lO3ucMUze485kGRTBghAn8ordblplJrUy3a439LRqkLICpxAh3U0Id6Ua28c2r2Dg2Gt0a00RekNi3LH'; 
$config['stripe_publishable_key'] = 'pk_test_VvwIQkhUm13tVDse6SYnU0gx00dem2XTMK'; 
$config['stripe_currency']        = 'usd';