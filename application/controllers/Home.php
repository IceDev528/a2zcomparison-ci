<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	
	/**
	 * Display the home page.
	 *
	 * @return Response
	 */
	public function index($category = NULL)
	{
		if (empty($category)) $category = 'all';
		
		$keyword = $this->input->get('q');
		
		if (($category == 'all') && (empty($keyword)))
		{
			$this->load->view('index', compact('category', 'keyword'));
			return;
		}
		else {
			
			$curl = array();
			//=============================prosperent============================
			$curl[str_prosperent] = getProsperentCurl($category, $keyword);
			//=============================amazon============================
			$curl[str_amazon] = getAmazonCurl('com', $category, $keyword);
			//=============================shopzilla============================
			$curl[str_shopzilla] = getShopzillaCurl($category, $keyword);
			
			//=============================amazon============================
			//=============================amazon============================
			
			
			$mh = curl_multi_init();

			//add the two handles
			foreach($curl as $ch) {
				curl_multi_add_handle($mh,$ch);
			}
			
			$running=null;
			
			//execute the handles
			do {
				curl_multi_exec($mh,$running);
			} while($running > 0);
			
			
			$return = getProductListResult($curl); 
			
			foreach($curl as $ch) {
				curl_multi_remove_handle($mh, $ch);
				curl_close($ch); 
			}
			
			curl_multi_close($mh);
			
			$product_list = $return->product;
			$totalRecords = $return->totalRecords;
			
			$this->load->view('product_landing', compact('category', 'keyword', 'product_list', 'totalRecords'));
			return;
		}
	}

	/**
	 * Display the product page.
	 *
	 * @return Response
	 */	
	public function product($site = NULL, $productId = NULL, $upc)
	{
		if(empty($site))
			return redirect('/');
		if(empty($productId))
			return redirect('/');
		
		$curl = array();
		
		if($site == str_prosperent)
			$curl[str_currentproduct] = getAmazonCurlForId($productId);
		else if($site == str_amazon)
			$curl[str_currentproduct] = getProsperentCurlForId($productId);
		else if($site == str_shopzilla)
			$curl[str_currentproduct] = getShopzillaCurlForId($productId);
		else return redirect('/');	
		
		//=============================prosperent============================
		$curl[str_prosperent] = getProsperentCurlComparison($upc);
		//=============================amazon============================
		$curl[str_amazon] = getAmazonCurlComparison($upc);
		//=============================shopzilla============================
		$curl[str_shopzilla] = getShopzillaCurlComparison($upc);
		//=============================shopzilla============================
		$curl[str_bestbuy] = getBestbuyCurlComparison($upc);
		
		//=============================amazon============================
		//=============================amazon============================
		
		
		$mh = curl_multi_init();

		//add the two handles
		foreach($curl as $ch) {
			curl_multi_add_handle($mh,$ch);
		}
		
		$running=null;
		
		//execute the handles
		do {
			curl_multi_exec($mh,$running);
		} while($running > 0);
		
		
		$return = getProductListResultComparison($curl, $site); 
		
		foreach($curl as $ch) {
			curl_multi_remove_handle($mh, $ch);
			curl_close($ch); 
		}
		
		curl_multi_close($mh);
		
		$product = $return->product;
		$price_comparison_list = $return->price_comparison_list;
		$review_list = $return->review_list;
		
		$this->load->view('product', compact('product', 'review_list', 'price_comparison_list'));
	}
}
