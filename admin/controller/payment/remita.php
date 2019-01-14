<?php
/**
 * Plugin Name: Remita OpenCart Payment Gateway
 * Plugin URI:  https://www.remita.net
 * Description: Remita OpenCart Payment gateway allows you to accept payment on your OpenCart store via Visa Cards, Mastercards, Verve Cards, eTranzact, PocketMoni, Paga, Internet Banking, Bank Branch and Remita Account Transfer.
 * Author:      SystemSpecs Limited
 * Version:     1.0
 */
class ControllerPaymentRemita extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/remita');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('remita', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_test'] = $this->language->get('text_test');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['entry_publickey'] = $this->language->get('entry_publickey');
        $data['entry_token'] = $this->language->get('entry_token');
		$data['entry_secretkey'] = $this->language->get('entry_secretkey');
		$data['entry_debug'] = $this->language->get('entry_debug');	
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$data['entry_processed_status'] = $this->language->get('entry_processed_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['remita_publickey'])) {
			$data['error_publickey'] = $this->error['remita_publickey'];
		} else {
			$data['error_publickey'] = '';
		}
		if (isset($this->error['remita_secretkey'])) {
			$data['error_secretkey'] = $this->error['remita_secretkey'];
		} else {
			$data['error_secretkey'] = '';
		}
 		
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/remita', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = $this->url->link('payment/remita', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['remita_publickey'])) {
			$data['remita_publickey'] = $this->request->post['remita_publickey'];
		} else {
			$data['remita_publickey'] = $this->config->get('remita_publickey');
		}	
		if (isset($this->request->post['remita_secretkey'])) {
			$data['remita_secretkey'] = $this->request->post['remita_secretkey'];
		} else {
			$data['remita_secretkey'] = $this->config->get('remita_secretkey');
		}
		if (isset($this->request->post['remita_mode'])) {
			$data['remita_mode'] = $this->request->post['remita_mode'];
		} else {
			$data['remita_mode'] = $this->config->get('remita_mode');
		}
		if (isset($this->request->post['remita_token'])) {
			$data['remita_token'] = $this->request->post['remita_token'];
		} elseif ($this->config->get('remita_token')) {
			$data['remita_token'] = $this->config->get('remita_token');
		} else {
			$data['remita_token'] = sha1(uniqid(mt_rand(), 1));
		}
	
		if (isset($this->request->post['remita_debug'])) {
			$data['remita_debug'] = $this->request->post['remita_debug'];
		} else {
			$data['remita_debug'] = $this->config->get('remita_debug');
		}
								
		if (isset($this->request->post['remita_pending_status_id'])) {
			$data['remita_pending_status_id'] = $this->request->post['remita_pending_status_id'];
		} else {
			$data['remita_pending_status_id'] = $this->config->get('remita_pending_status_id');
		}
									
		if (isset($this->request->post['remita_processed_status_id'])) {
			$data['remita_processed_status_id'] = $this->request->post['remita_processed_status_id'];
		} else {
			$data['remita_processed_status_id'] = $this->config->get('remita_processed_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['remita_geo_zone_id'])) {
			$data['remita_geo_zone_id'] = $this->request->post['remita_geo_zone_id'];
		} else {
			$data['remita_geo_zone_id'] = $this->config->get('remita_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['remita_status'])) {
			$data['remita_status'] = $this->request->post['remita_status'];
		} else {
			$data['remita_status'] = $this->config->get('remita_status');
		}
		
		if (isset($this->request->post['remita_sort_order'])) {
			$data['remita_sort_order'] = $this->request->post['remita_sort_order'];
		} else {
			$data['remita_sort_order'] = $this->config->get('remita_sort_order');
		}
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('payment/remita.tpl', $data));
	}
		
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/remita')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['remita_publickey']) {
			$this->error['remita_publickey'] = $this->language->get('error_publickey');
		}
		if (!$this->request->post['remita_secretkey']) {
			$this->error['remita_secretkey'] = $this->language->get('error_secretkey');
		}
	
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>