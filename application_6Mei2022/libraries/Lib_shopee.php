<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib_shopee
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        $this->CI->load->model(array('Shopee_model'));
	}

	public function check_expire_in()
	{
		date_default_timezone_set("Asia/Jakarta");
		$get_data_api_shopee = $this->CI->Shopee_model->get_by_id(1);
		if ($get_data_api_shopee->timestamp_access_token != NULL) {
			$date = new DateTime();
			$timestamp_now = new DateTime($date->format('Y-m-d H:i:s'));
			$timestamp_past = new DateTime(date('Y-m-d H:i:s', $get_data_api_shopee->timestamp_access_token));
			$interval = $timestamp_now->diff($timestamp_past);

			$str_time = $interval->h.":".$interval->i.":".$interval->s;

			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

			$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

			if ($time_seconds > $get_data_api_shopee->expire_in) {
				$data = array(
		          'timestamp_access_token'	=> NULL,
		          'access_token'			=> NULL,
		          'refresh_token'			=> NULL,
		          'expire_in'				=> NULL,
		          'timestamp_access_token'	=> NULL
		        );

		        $this->CI->Shopee_model->update(1, $data);

			    write_log();

				$this->CI->session->set_flashdata('message', '<div class="alert alert-danger">Access Token and Refresh Token has expired. Please do Auth Partner again!</div>');
			    redirect('admin/shopee');
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
		
	}

	public function SignIn($api_path)
	{
		date_default_timezone_set("Asia/Jakarta");
		$get_data_api_shopee = $this->CI->Shopee_model->get_by_id(1);
		// JIKA TIME STAMP KOSONG
		if ($get_data_api_shopee->timestamp == '' || $get_data_api_shopee->timestamp == NULL) {
			$date = new DateTime();
			$timestamp = $date->getTimestamp();
			$futureTimestamp = $timestamp+(60*5);
			// echo $timestamp."<br>";
			// echo $futureTimestamp."<br>";
			// echo date("Y-m-d H:i:s", $timestamp)."<br>";
			// echo date("Y-m-d H:i:s", $futureTimestamp)."<br>";
			$partner_id = strval($get_data_api_shopee->partner_id);
			$partner_key = strval($get_data_api_shopee->partner_key);
			$path = strval($api_path); //without the host
			$base_str = $partner_id . $path . $timestamp;            
			$sign = hash_hmac('sha256', $base_str,  $partner_key);

			$data = array(
	          'timestamp'         => $timestamp,
	        );

	        $this->CI->Shopee_model->update(1, $data);

	        write_log();

			$result = array( 'status_sign'	=> 1,
							 'signin'		=> $sign,
							 'partner_id'	=> $partner_id,
							 'partner_key'	=> $partner_key,
							 'timestamp'	=> $timestamp,
							 'host'			=> $get_data_api_shopee->host,
							 'code'			=> $get_data_api_shopee->code,
							 'shop_id'		=> $get_data_api_shopee->shop_id,
							 'access_token'	=> $get_data_api_shopee->access_token,
							 'refresh_token'=> $get_data_api_shopee->refresh_token,
							 'expire_in'	=> $get_data_api_shopee->expire_in,
			);

			return $result;	
		}else{
			// JIKA TIDAK
			$date = new DateTime();
			$timestamp = $date->getTimestamp();
			// JIKA HASIL PENGURANGAN TIMESTAMP TERBARU DENGAN YANG ADA DI DB >= 300 (5 Menit)
			if (((int)$timestamp - (int) $get_data_api_shopee->timestamp) >= 600) {
				$partner_id = strval($get_data_api_shopee->partner_id);
				$partner_key = strval($get_data_api_shopee->partner_key);
				$path = strval($api_path); //without the host
				$base_str = $partner_id . $path . $timestamp;            
				$sign = hash_hmac('sha256', $base_str,  $partner_key);

				$data = array(
		          'code'      => NULL,
		          'timestamp' => NULL
		        );

		        $this->CI->Shopee_model->update(1, $data);

		        write_log();

				$result = array( 'status_sign'	=> 2,
								 'signin'		=> $sign,
								 'partner_id'	=> $partner_id,
								 'partner_key'	=> $partner_key,
								 'timestamp'	=> $timestamp,
								 'host'			=> $get_data_api_shopee->host,
								 'shop_id'		=> $get_data_api_shopee->shop_id,
								 'access_token'	=> $get_data_api_shopee->access_token,
								 'refresh_token'=> $get_data_api_shopee->refresh_token,
								 'expire_in'	=> $get_data_api_shopee->expire_in,
				);

				return $result;	
			}else{
				// JIKA HASIL PENGURANGAN TIMESTAMP TERBARU DENGAN YANG ADA DI DB < 300 (5 Menit)
				$partner_id = $get_data_api_shopee->partner_id;
				$partner_key = $get_data_api_shopee->partner_key;
				$path = $api_path; //without the host
				$base_str = $partner_id . $path . $timestamp;            
				$sign = hash_hmac('sha256', $base_str,  $partner_key);

				$result = array( 'status_sign'	=> 3,
								 'signin'		=> $sign,
								 'partner_id'	=> $partner_id,
								 'partner_key'	=> $partner_key,
								 'timestamp'	=> $timestamp,
								 'host'			=> $get_data_api_shopee->host,
								 'code'			=> $get_data_api_shopee->code,
								 'shop_id'		=> $get_data_api_shopee->shop_id,
								 'access_token'	=> $get_data_api_shopee->access_token,
								 'refresh_token'=> $get_data_api_shopee->refresh_token,
								 'expire_in'	=> $get_data_api_shopee->expire_in,
				);

				return $result;	
			}
		}
	}

	public function SignInWithToken($api_path)
	{
		date_default_timezone_set("Asia/Jakarta");
		$get_data_api_shopee = $this->CI->Shopee_model->get_by_id(1);
		// JIKA TIME STAMP KOSONG
		if ($get_data_api_shopee->timestamp == '' || $get_data_api_shopee->timestamp == NULL) {
			$date = new DateTime();
			$timestamp = $date->getTimestamp();
			$partner_id = strval($get_data_api_shopee->partner_id);
			$partner_key = strval($get_data_api_shopee->partner_key);
			$access_token = strval($get_data_api_shopee->access_token);
			$shop_id = strval($get_data_api_shopee->shop_id);
			$path = strval($api_path); //without the host
			$base_str = $partner_id . $path . $timestamp . $access_token . $shop_id;            
			$sign = hash_hmac('sha256', $base_str,  $partner_key);

			$data = array(
	          'timestamp'         => $timestamp,
	        );

	        $this->CI->Shopee_model->update(1, $data);

	        write_log();

			$result = array( 'status_sign'	=> 1,
							 'signin'		=> $sign,
							 'partner_id'	=> $partner_id,
							 'partner_key'	=> $partner_key,
							 'timestamp'	=> $timestamp,
							 'host'			=> $get_data_api_shopee->host,
							 'code'			=> $get_data_api_shopee->code,
							 'shop_id'		=> $get_data_api_shopee->shop_id,
							 'access_token'	=> $get_data_api_shopee->access_token,
							 'refresh_token'=> $get_data_api_shopee->refresh_token,
							 'expire_in'	=> $get_data_api_shopee->expire_in,
			);

			return $result;	
		}else{
			// JIKA TIDAK
			$date = new DateTime();
			$timestamp = $date->getTimestamp();
			// JIKA HASIL PENGURANGAN TIMESTAMP TERBARU DENGAN YANG ADA DI DB >= 300 (5 Menit)
			if (((int)$timestamp - (int) $get_data_api_shopee->timestamp) >= 600) {
				$partner_id = strval($get_data_api_shopee->partner_id);
				$partner_key = strval($get_data_api_shopee->partner_key);
				$access_token = strval($get_data_api_shopee->access_token);
				$shop_id = strval($get_data_api_shopee->shop_id);
				$path = strval($api_path); //without the host
				$base_str = $partner_id . $path . $timestamp . $access_token . $shop_id;              
				$sign = hash_hmac('sha256', $base_str,  $partner_key);

				$data = array(
		          'code'      => NULL,
		          'timestamp' => NULL
		        );

		        $this->CI->Shopee_model->update(1, $data);

		        write_log();

				$result = array( 'status_sign'	=> 2,
								 'signin'		=> $sign,
								 'partner_id'	=> $partner_id,
								 'partner_key'	=> $partner_key,
								 'timestamp'	=> $timestamp,
								 'host'			=> $get_data_api_shopee->host,
								 'shop_id'		=> $get_data_api_shopee->shop_id,
								 'access_token'	=> $get_data_api_shopee->access_token,
								 'refresh_token'=> $get_data_api_shopee->refresh_token,
								 'expire_in'	=> $get_data_api_shopee->expire_in,
				);

				return $result;	
			}else{
				// JIKA HASIL PENGURANGAN TIMESTAMP TERBARU DENGAN YANG ADA DI DB < 300 (5 Menit)
				$partner_id = strval($get_data_api_shopee->partner_id);
				$partner_key = strval($get_data_api_shopee->partner_key);
				$access_token = strval($get_data_api_shopee->access_token);
				$shop_id = strval($get_data_api_shopee->shop_id);
				$path = strval($api_path); //without the host
				$base_str = $partner_id . $path . $timestamp . $access_token . $shop_id;               
				$sign = hash_hmac('sha256', $base_str,  $partner_key);

				$result = array( 'status_sign'	=> 3,
								 'signin'		=> $sign,
								 'partner_id'	=> $partner_id,
								 'partner_key'	=> $partner_key,
								 'timestamp'	=> $timestamp,
								 'host'			=> $get_data_api_shopee->host,
								 'code'			=> $get_data_api_shopee->code,
								 'shop_id'		=> $get_data_api_shopee->shop_id,
								 'access_token'	=> $get_data_api_shopee->access_token,
								 'refresh_token'=> $get_data_api_shopee->refresh_token,
								 'expire_in'	=> $get_data_api_shopee->expire_in,
				);

				return $result;	
			}
		}
	}

	public function AuthPartner($redirect_url)
	{
		$data = $this->SignIn('/api/v2/shop/auth_partner');

		$url = $data['host'].'/api/v2/shop/auth_partner?partner_id='.$data['partner_id'].'&timestamp='.$data['timestamp'].'&sign='.$data['signin'].'&redirect='.$redirect_url;

		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Operasional Brighty');
		$query = curl_exec($curl_handle);
		curl_close($curl_handle);

		$dom = new DOMDocument;
		$dom->loadHTML($query);
		$nodes = $dom->getElementsByTagName('a');
		$attr  = $nodes->item(0)->getAttribute('href');
		$json = array( 	'sukses'	=> 'Auth Partner Berhasil!',
						'href_link' => strval($attr)
				);
		echo json_encode($json);
		// $obj = json_decode($json);
		// echo $obj->access_token;
	}

	public function getAccessToken()
	{
		$data = $this->SignIn('/api/v2/auth/token/get');

		if ($data['status_sign'] == 2 || $data['code'] == NULL || $data['code'] == '') {
			$this->CI->session->set_flashdata('message', '<div class="alert alert-danger">Please do Auth Partner again!</div>');
		    redirect('admin/shopee');
		}else{
			// BARU
			$url = $data['host'].'/api/v2/auth/token/get?sign='.$data['signin'].'&partner_id='.$data['partner_id'].'&timestamp='.$data['timestamp'];
			$timestamp_access_token = $data['timestamp'];
			$data = array("code" => $data['code'], "partner_id" => (int)$data['partner_id'], "shop_id" => (int)$data['shop_id']);

			$postdata = json_encode($data);

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $postdata,
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json'
			  ),
			));

			$response = curl_exec($curl);
			$response_decode = json_decode($response);

			curl_close($curl);

			$dataUpdate = array(
		          'code'      					=> NULL,
		          'timestamp_access_token'	 	=> $timestamp_access_token,
		          'access_token'				=> $response_decode->access_token,
		          'refresh_token'				=> $response_decode->refresh_token,
		          'expire_in'					=> $response_decode->expire_in,
		    );

	        $this->CI->Shopee_model->update(1, $dataUpdate);
			
			$this->CI->session->set_flashdata('message', '<div class="alert alert-success">Access Token and Refresh Token successfully!</div>');
		    redirect('admin/shopee');
		}
	}
	
	public function getShopInfo()
	{
		$shopee = $this->SignInWithToken('/api/v2/shop/get_shop_info');

		$url = $shopee['host'].'/api/v2/shop/get_shop_info?partner_id='.$shopee['partner_id'].'&timestamp='.$shopee['timestamp'].'&access_token='.$shopee['access_token'].'&shop_id='.$shopee['shop_id'].'&sign='.$shopee['signin'];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		$response_decode = json_decode($response);

		curl_close($curl);

		return $response_decode;
	}

	public function getShopProfle()
	{
		$shopee = $this->SignInWithToken('/api/v2/shop/get_profile');

		$url = $shopee['host'].'/api/v2/shop/get_profile?partner_id='.$shopee['partner_id'].'&timestamp='.$shopee['timestamp'].'&access_token='.$shopee['access_token'].'&shop_id='.$shopee['shop_id'].'&sign='.$shopee['signin'];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		$response_decode = json_decode($response);

		curl_close($curl);

		return $response_decode;
	}

	public function getChannelList()
	{
		$shopee = $this->SignInWithToken('/api/v2/logistics/get_channel_list');

		$url = $shopee['host'].'/api/v2/logistics/get_channel_list?partner_id='.$shopee['partner_id'].'&timestamp='.$shopee['timestamp'].'&access_token='.$shopee['access_token'].'&shop_id='.$shopee['shop_id'].'&sign='.$shopee['signin'];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		$response_decode = json_decode($response);

		curl_close($curl);

		return $response_decode;
	}

	public function getShopPerformance()
	{
		$shopee = $this->SignInWithToken('/api/v2/account_health/shop_performance');

		$url = $shopee['host'].'/api/v2/account_health/shop_performance?partner_id='.$shopee['partner_id'].'&timestamp='.$shopee['timestamp'].'&access_token='.$shopee['access_token'].'&shop_id='.$shopee['shop_id'].'&sign='.$shopee['signin'];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		$response_decode = json_decode($response);

		curl_close($curl);

		return $response_decode;
	}

	public function getOrderList()
	{
		$shopee = $this->SignInWithToken('/api/v2/order/get_order_list');

		// date_default_timezone_set("Asia/Jakarta");
		$date = new DateTime();
		$nowTimestamp = $date->getTimestamp();
		$pastTimestamp = $nowTimestamp-(60*21600);

		$url = $shopee['host'].'/api/v2/order/get_order_list?partner_id='.$shopee['partner_id'].'&timestamp='.$shopee['timestamp'].'&access_token='.$shopee['access_token'].'&shop_id='.$shopee['shop_id'].'&sign='.$shopee['signin'].'&time_range_field=create_time&time_from='.$pastTimestamp.'&time_to='.$nowTimestamp.'&page_size=100';

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		$response_decode = json_decode($response);

		curl_close($curl);

		return $response_decode;
	}
}

/* End of file Lib_shopee.php */
/* Location: ./application/libraries/Lib_shopee.php */
