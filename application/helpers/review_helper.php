<?php
define('max_product_list_count', 20);
define('max_comparison_count', 10);
define('max_review_count', 5);
define('str_amazon','amazon');
define('str_prosperent','prosperent');
define('str_shopzilla','shopzilla');
define('str_cnet','cnet');
define('str_bestbuy','bestbuy');

define('str_currentproduct','currentproduct');
define('str_product_item_search','product_item_search');
define('str_product_universial_item_search','product_universial_item_search');
//=====================================category list=================================
//template, prosperent, amazon, shopzilla,
//$category_array_list = array(
//array("computer&tablets","clothing & accessories","computer | laptop","computer | laptop"));
///====================================amazon functions===============================
function getAmazonCurl($region, $category, $keyword) {
 
	$data = array(
		"Operation" => "ItemSearch",
		"IncludeReviewsSummary" => False,
		"ResponseGroup" => "Medium,OfferSummary,Accessories,Images",
		"Condition" => "All",
	);
	
	if(!empty($keyword))
	{
		$Keywords = $keyword; 
		$data = $data + compact('Keywords');
	}
	else {
		$Keywords = $category; 
		$data = $data + compact('Keywords');
		
	}
	
	$category = 'All';
	
	if(!($category == 'all'))
	{
		$SearchIndex = $category; 
		$data = $data + compact('SearchIndex');
	}
	
	$ch = aws_signed_request($region, $data);	
	
	return $ch;
}

function getAmazonCurlForId($asin) {
 
	$data = array(
		"Operation" => "ItemLookup",
		"ItemId" => $asin,
		"IncludeReviewsSummary" => False,
		"ResponseGroup" => "Medium,OfferSummary,Accessories,Images",
	);
	
	$ch = aws_signed_request('com', $data);	
	
	return $ch;
}
 
function getAmazonCurlComparison($upc) {
 
	$data = array(
		"Operation" => "ItemLookup",
		"ItemId" => $upc,
		"IdType" => "UPC",
		"SearchIndex" => "All",
		"ResponseGroup" => "Medium,OfferSummary,Accessories,Images",
	);
	
	$ch = aws_signed_request('com', $data);	
	
	return $ch;
}
 
function getPage($url) {
 
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 15);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	
	return $curl;
}
 
function aws_signed_request($region, $params) {
 
	$public_key = "AKIAIQNXANCW6XFR55BA";
	$private_key = "Z5unWeON8270VwlmTaROz1XpnQLO/j/IqiTsL36K";
	
	$method = "GET";
	$host = "webservices.amazon." . $region;
	$uri = "/onca/xml";
 
	$params["Service"] = "AWSECommerceService";
	$params["AssociateTag"] = "affiliate-20"; // Put your Affiliate Code here
	$params["AWSAccessKeyId"] = $public_key;
	$params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	$params["Version"] = "2015-05-26";
 
	ksort($params);
	$canonicalized_query = array();
	foreach ($params as $param => $value) {
		$param = str_replace("%7E", "~", rawurlencode($param));
		$value = str_replace("%7E", "~", rawurlencode($value));
		$canonicalized_query[] = $param . "=" . $value;
	}
	$canonicalized_query = implode("&", $canonicalized_query);
 
	$string_to_sign = $method . "\n" . $host . "\n" . $uri . "\n" . $canonicalized_query;
	$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, True));
	$signature = str_replace("%7E", "~", rawurlencode($signature));
 
	$request = "http://" . $host . $uri . "?" . $canonicalized_query . "&Signature=" . $signature;
	$ch = getPage($request);
	
	return $ch;
	
}
//====================================prosperent functions===============================
function getProsperentCurl($category, $keyword) {
	$apikey = '274ab56313562fc993a85d25a957ae8e'; 

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, 'http://api.prosperent.com/api/search '); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POST, true); 
	$data = array( 
			'api_key' => $apikey, 
			'imageSize' => '250x250',
			'limit' => '20', 

			
	); 
	
	if($category == str_product_item_search) {
		if(!empty($keyword))
		{
			$filterCatalogId = $keyword; 
			$data = $data + compact('filterCatalogId');
		}	
		else die;
	}
	else if($category == str_product_universial_item_search) {
		if(!empty($keyword))
		{
			$query = $keyword; 
			$data = $data + compact('query');
		}	
		else die;
	}
	else {
		if(!empty($keyword))
		{
			$query = $keyword; 
			$data = $data + compact('query');
		}	
		else if(!($category == 'all'))
		{
			$filterKeyword = $category; 
			$data = $data + compact('filterKeyword');
		}
	}
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	
	return $ch;
			
}

function getProsperentCurlForId($productId) {
	return getProsperentCurl(str_product_item_search, $productId);			
}

function getProsperentCurlComparison($upc) {
	return getProsperentCurl(str_product_universial_item_search, $upc);			
}

//====================================shopzilla functions===============================
function getShopzillaCurl($category, $keyword) {
	$publisherId = "601730"; 
	$apikey = "36ea984ef3a983337262e15ca5b1a299"; 
	
	$method = "GET";
	$host = "http://catalog.bizrate.com";
	$uri = "/services/catalog/v1/api/product";

	$params["publisherId"] = $publisherId; 
	$params["apiKey"] = $apikey;
	
	if($category == str_product_item_search) {
		if(!empty($keyword))
			$params["productId"] = $keyword;
		else die;
	}
	else if ($category == str_product_universial_item_search) {
		if(!empty($keyword))
		{
			$params["productId"] = $keyword;
			$params['productIdType'] = 'UPC';
			$params["keyword"] = '';

		}
		else die;
	}
	else {
			
		if(!empty($keyword))
			$params["keyword"] = $keyword;
		else if(!($category == 'all'))
			$params["keyword"] = $category;
	}

	$params["format"] = 'json';

	$canonicalized_query = array();
	foreach ($params as $param => $value) {
		$param = str_replace("%7E", "~", rawurlencode($param));
		$value = str_replace("%7E", "~", rawurlencode($value));
		$canonicalized_query[] = $param . "=" . $value;
	}
	
	$canonicalized_query = implode("&", $canonicalized_query);

	$request = $host . $uri . "?" . $canonicalized_query;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$request);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	
	return $ch;
}

function getShopzillaCurlForId($productId) {
	return getShopzillaCurl(str_product_item_search, $productId);
}

function getShopzillaCurlComparison($upc) {
	return getShopzillaCurl(str_product_universial_item_search, $upc);
}

//====================================shopzilla functions===============================
function getBestbuyCurl($category, $keyword) {
	$apikey = "84u6rzhxeqf9qratdbwur4fw"; 
	
	$method = "GET";
	$host = "http://api.bestbuy.com";
	if($category == str_product_item_search) {
		if(!empty($keyword))
		{
			$host = "http://api.remix.bestbuy.com";
			$query="(sku=$keyword)";
			$uri = "/v1/reviews".$query;
			
			$params["apiKey"] = $apikey;
		}
		else die;
	}
	else if ($category == str_product_universial_item_search) {
		if(!empty($keyword))
		{
			$query="(upc=$keyword)";
			$uri = "/v1/products".$query;
			
			$params["apiKey"] = $apikey;
			//$params["show"] = "sku,upc,name,customerReviewAverage,customerReviewCount";
			$params["show"] = "all";
		}
		else die;
	}
	else {
	}

	$params["format"] = 'json';

	$canonicalized_query = array();
	foreach ($params as $param => $value) {
		$param = str_replace("%7E", "~", rawurlencode($param));
		$value = str_replace("%7E", "~", rawurlencode($value));
		$canonicalized_query[] = $param . "=" . $value;
	}
	
	$canonicalized_query = implode("&", $canonicalized_query);

	$request = $host . $uri . "?" . $canonicalized_query;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$request);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	
	return $ch;
}

function getBestbuyCurlForId($productId) {
	return getBestbuyCurl(str_product_item_search, $productId);
}

function getBestbuyCurlComparison($upc) {
	return getBestbuyCurl(str_product_universial_item_search, $upc);
}
//====================================bestbuy functions=================================
//====================================cnet functions====================================

/*
convert various result to same form
*/
function converttonormal($product_list) {
	$return = array();
	$temp = (object)[];
	
	if(!empty($product_list[str_prosperent]->data))
		$temp->product = $product_list[str_prosperent]->data;
	else $temp->product = array();
	
	if(isset($product_list[str_prosperent]->totalRecordsFound))
		$temp->totalRecords = $product_list[str_prosperent]->totalRecordsFound;
	else $temp->totalRecords = 0;
	
	$return[str_prosperent] = $temp;
	//============================================================
	$temp = (object)[];
	
	if(!empty($product_list[str_shopzilla]))
	{
		$ncount = $product_list[str_shopzilla]->products->includedResults;
		$temp->totalRecords = $ncount;
		$temp->product = array();
		if($ncount>0)
		{
			foreach($product_list[str_shopzilla]->products->product as $product) {
				$t = (object)[];
				
				if(!empty($product->brand))
					$t->brand = $product->brand->name;
				else $t->brand = "";
				
				$t->title = $product->title;
				$t->image_url = $product->images->image[3]->value;
				
				if(!empty($product->upc))
					$t->upc = $product->upc;
				else continue; 
					
				if(!empty($product->sku))
					$t->catalogId = $product->sku;
				else if(!empty($product->skus)) $t->catalogId = $product->skus->sku[0];
				else continue;
				$temp->product[] = $t;
			}
		}
		
		$return[str_shopzilla] = $temp;
	}
	else {
		$temp->totalRecords =0;
		$temp->product = array();
		$return[str_shopzilla] = $temp;
	}
	//============================================================
	$temp = (object)[];
	
	if(!empty($product_list[str_amazon]))
	{
		$temp->totalRecords = $product_list[str_amazon]->Items->TotalResults;
		$temp->product = array();
		foreach($product_list[str_amazon]->Items->Item as $product) {
			$t = (object)[];
			
			if(!empty($product->ItemAttributes->Brand))
				$t->brand = $product->ItemAttributes->Brand;
			else $t->brand = "";
			
			if(!empty($product->ItemAttributes->UPC ))
				$t->upc = $product->ItemAttributes->UPC;
			else continue;
			
			$t->title = $product->ItemAttributes->Title;
			$t->catalogId = $product->ASIN;
			$t->image_url = $product->LargeImage->URL;
			$temp->product[] = $t;
		}
		
		$return[str_amazon] = $temp;
	
	}
	
	else {
		$temp->totalRecords =0;
		$temp->product = array();
		$return[str_amazon] = $temp;
	}
	
	return $return;
}

//====================================getProductListResult==================================
function getProductListResult($curl) {
	$json_format_list = array(str_prosperent,str_shopzilla);
	
	foreach($curl as $key => $ch) {
		$output[$key]  = curl_multi_getcontent($ch);
	}
	
	$product_list = array();
	
	foreach($json_format_list as $key) {
		$return  = json_decode( $output[$key] ); 
		$product_list[$key] = $return;
	}
	
	$product_list[str_amazon] = @simplexml_load_string($output[str_amazon]);;
	
	//$product_list[$key] = $return;
	$return = (object)[];
	$return->totalRecords = 0;
	$return->product = array();
	
	$result = converttonormal($product_list);
	foreach($result as $product_info_key => $products_info)
	{
		foreach($products_info->product as $product)
		{
			if($return->totalRecords >= max_product_list_count)
				break;
			
			if(empty($product->brand))
				continue;
			if(empty($product->upc))
				continue;
			
			if($product_info_key == str_prosperent)
				$product->catalogId = str_prosperent.'/'.$product->catalogId.'/'.$product->upc;
			if($product_info_key == str_shopzilla)
				$product->catalogId = str_shopzilla.'/'.$product->catalogId.'/'.$product->upc;
			if($product_info_key == str_amazon)
				$product->catalogId = str_amazon.'/'.$product->catalogId.'/'.$product->upc;
			
			$return->product[] = $product;
			$return->totalRecords++;
		}
	}
	
	return $return;
}

function converttoproductcomparison($product_list, $site) {
	$return = array();
	
	if(!empty($product_list[str_prosperent]->data))
	{
		$return[str_prosperent] = array();
		
		//var_dump($product_list[str_prosperent]->data);
		//die;
		foreach($product_list[str_prosperent]->data as $product)
		{
			$temp = (object)[];
			
			if(!empty($product->brand))
				$temp->brand = $product->brand;
			else $temp->brand = "";
			
			if(!empty($product->upc))
				$temp->upc = $product->upc;
			else continue;
				
			$temp->title = $product->merchant;
			$temp->merchant = $product->merchant;
			$temp->image_url = $product->image_url;
			$temp->overallrating = 5;
			$temp->description = $product->description;
			$temp->shipType = 'Free shipping';
			$temp->price = '$'.$product->price;
			$temp->tax_price = 0;
			$temp->url = $product->affiliate_url;
			$temp->dropped_percent = $product->percentOff;
			$temp->catalogId = $product->catalogId;
			
			$return[str_prosperent][] = $temp;
		}
	}
	else {
		$return[str_prosperent] = '';
	}
	
	
	//var_dump($return[str_prosperent]);
	//die;
	//============================================================
	if(!empty($product_list[str_shopzilla]))
	{
		$return[str_shopzilla] = array();
		
		foreach($product_list[str_shopzilla]->offers->offer as $product) {
			$t = (object)[];
			
			if(!empty($product->brand))
				$t->brand = $product->brand->name;
			else $t->brand = "";
			
			if(!empty($product->upc))
				$t->upc = $product->upc;
			else continue;
				
			$t->title = $product->title;
			$t->merchant = $product->merchantName;
			$t->image_url = $product->images->image[3]->value;
			$t->overallrating = min($product->price->integral/5000, 5);
			$t->description = $product->description;
			$t->shipType = $product->shipType;
			$t->price = $product->price->value;
			$t->tax_price = 0;
			$t->url = $product->url->value;
			$t->dropped_percent = $product->markdownPercent;
			
			if(!empty($product->sku))
				$t->catalogId = $product->sku;
			else if(!empty($product->skus)) $t->catalogId = $product->skus->sku[0];
			else continue;
			
			$return[str_shopzilla][] = $t;
		}

		
	}
	else {
		//$temp->totalRecords =0;
		//$temp->product = array();
		$return[str_shopzilla] = '';
	}
	
	//============================================================
	if(!empty($product_list[str_amazon]->Items->Item))
	{
		//var_dump($product_list[str_amazon]->Items->Item);
		//die;
		$return[str_amazon] = array();
		
		foreach($product_list[str_amazon]->Items->Item as $product) {
			$t = (object)[];
			
			if(!empty($product->ItemAttributes->Brand))
				$t->brand = $product->ItemAttributes->Brand;
			else $t->brand = "";
			
			$t->title = $product->ItemAttributes->Title;
			
				
			if(!empty($product->ItemAttributes->UPC ))
				$t->upc = (string)$product->ItemAttributes->UPC;
			else continue;
			
			$t->catalogId = $product->ASIN;
			//var_dump($product);
			//die;
			$t->merchant = $product->ItemAttributes->Studio;
			
			//var_dump($t->merchant);
			
			if(empty($t->merchant))
				$t->merchant = $t->brand;
			
			$t->image_url = $product->LargeImage->URL;
			$t->overallrating = min($product->SalesRank/1000, 5);
			//$t->description = $product->EditorialReviews->EditorialReview->Content;
			$t->shipType = "Free shipping";
			$t->price = $product->OfferSummary->LowestNewPrice->FormattedPrice;
			$t->tax_price = 0;
			$t->url = $product->DetailPageURL;
			if(!empty($product->OfferSummary->LowestUsedPrice->Amount))
				$t->dropped_percent = max(($product->OfferSummary->LowestUsedPrice->Amount-$product->OfferSummary->LowestNewPrice->Amount)/$product->OfferSummary->LowestUsedPrice->Amount*100.00, 0);
			else $t->dropped_percent = "";
			
			if($t->dropped_percent < 0)
				$t->dropped_percent = 0;
			
			if(!empty($product->EditorialReviews->EditorialReview->Content))
				$t->description = $product->EditorialReviews->EditorialReview->Content;
			else $t->description = "";
			
			$t->image_url = $product->LargeImage->URL;
			
			$return[str_amazon][] = $t;
		}
	}
	else {
		//$temp->totalRecords =0;
		//$temp->product = array();
		$return[str_amazon] = '';
	}
	
	//var_dump($product_list[str_amazon]);
	//die;
	
	return $return;
}

function converttoproductreview($product_list) {
	//============================================================
	//var_dump($product_list[str_bestbuy]);
	//die;
	if(!empty($product_list[str_bestbuy]->products))
	{
		//var_dump($product_list[str_bestbuy]);
		//die;
		//var_dump($product_list[str_bestbuy]->total);
		//foreach($product_list[str_bestbuy]->products as $product) 
		$product = $product_list[str_bestbuy]->products[0];
		{
			$t = (object)[];
			
			$t->sku = $product->sku;
			$t->customerReviewAverage = $product->customerReviewAverage;
			$t->customerReviewCount = $product->customerReviewCount;
			
			$ch = getBestbuyCurlForId($t->sku);
			$output = curl_exec($ch); 
			$info = curl_getinfo($ch);
			curl_close($ch); 
			
			$temp = json_decode( $output )->reviews;
			$t->review_array = array();
			$count = 0;
			
			foreach($temp as $item) {
				if($count >= max_review_count)
					continue;
				
				$count++;
				$t->review_array[] = $item;
			}
			//var_dump($t);
			//die;
			$return = $t;
		}
	}
	else {
		$return[str_bestbuy] = '';
	}
	
	return $return;
}

function getProductListResultComparison($curl, $site) {
	$json_format_list = array(str_prosperent,str_shopzilla,str_bestbuy);
	
	foreach($curl as $key => $ch) {
		$output[$key]  = curl_multi_getcontent($ch);
	}
	
	$product_list = array();
	
	foreach($json_format_list as $key) {
		$return  = json_decode( $output[$key] ); 
		$product_list[$key] = $return;
	}
	
	$product_list[str_amazon] = @simplexml_load_string($output[str_amazon]);;
	$return = (object)[];
	$return->totalRecords = 0;
	$return->price_comparison_list = array();
	$return->review_list = array();
	$return->product = (object)[];
	$result = converttoproductcomparison($product_list, $site);
	$review_list = converttoproductreview($product_list);
	
	if(!empty($result[$site][0]->brand))
	{
		$temp = $result[$site][0];
		$return->product->brand = $temp->brand;
		$return->product->title = $temp->title;
		if(!empty($review_list->customerReviewAverage))
			$return->product->overallrating = $review_list->customerReviewAverage;
		else $return->product->overallrating = '0.0';
		$return->product->image_url = $temp->image_url;
		$return->product->description = $temp->description;
	}
	else die;
	
	$price_comparison_count =0;
	foreach($result as $product_info_key => $parray)
	{
		if(!empty($parray))
		{
			foreach($parray as $product)
			{
				if($price_comparison_count >= max_comparison_count)
					continue;
				
				$product->dropped_percent = ((integer)($product->dropped_percent*100))/100;
				
				$return->price_comparison_list[] = $product;
				$price_comparison_count++;
			}
		}
	}
	
	//var_dump($return->price_comparison_list);
	if(!empty($review_list->review_array))
		$return->review_list = $review_list->review_array;
	else $return ->review_list = NULL;
	return $return;
}
