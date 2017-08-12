<?php
namespace WebReinvent\Detect;

use \Curl\Curl;

/**
*  @author webreinvent
*/
class CMS{

   private $url;

   public function __construct($url)
   {
       $this->url = $url;
   }
   //---------------------------------------------------
    public function detect()
    {
        $scrap = $this->scrap();
        if($scrap['status'] == 'failed')
        {
            return $scrap;
        }

		return $this->find_cms($scrap['data']);
    }
    //---------------------------------------------------

    public function scrap()
    {
        $curl = new Curl();
        $curl->get($this->url);

        if ($curl->error) {
            $response['status'] = 'failed';
            $response['errors']['code']= $curl->errorCode;
            $response['errors']['message']= $curl->errorMessage;
            return $response;
        } else {
            $response['status'] = 'success';
            $response['data']= $curl->response;
            return $response;
        }
    }

    //---------------------------------------------------
    public function find_cms($html)
    {
        $response['data']['url'] = $this->url;
        if (strpos($html, 'wp-admin') !== false)
        {
            $response['status'] = 'success';
            $response['data']['cms'] = 'wordpress';
            return $response;
        } else if(strpos($html, 'woocommerce') !== false)
        {
            $response['status'] = 'success';
            $response['data']['cms']= 'woocomerce';
            return $response;
        }else if(strpos($html, 'skin/frontend/') !== false)
        {
            $response['status'] = 'success';
            $response['data']['cms']= 'magento';
            return $response;
        }else if(strpos($html, 'cdn.shopify.com') !== false)
        {
            $response['status'] = 'success';
            $response['data']['cms']= 'shopify';
            return $response;
        }

        unset($response['data']);
        $response['errors'][] = "CMS not detected for ".$this->url;
        $response['status'] = 'failed';
        return $response;
    }
    //---------------------------------------------------
    //---------------------------------------------------
}