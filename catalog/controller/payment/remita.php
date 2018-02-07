<?php

/**
 * Plugin Name: Remita OpenCart Payment Gateway
 * Plugin URI:  https://www.remita.net
 * Description: Remita OpenCart Payment gateway allows you to accept payment on your OpenCart store via Visa Cards, Mastercards, Verve Cards, eTranzact, PocketMoni, Paga, Internet Banking, Bank Branch and Remita Account Transfer.
 * Author:      Oshadami Mike
 * Version:     1.0
 */
class ControllerPaymentRemita extends Controller {

    public function index() {
        $this->language->load('payment/remita');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $order_id = $this->session->data['order_id'];
        if ($order_info) {
            $data['remita_mercid'] = trim($this->config->get('remita_mercid'));
            $data['remita_servicetypeid'] = trim($this->config->get('remita_servicetypeid'));
            $data['remita_apikey'] = trim($this->config->get('remita_apikey'));
            $selectedPayment = $this->config->get('remita_paymentoptions');
            $mode = trim($this->config->get('remita_mode'));
            $data['storeorderid'] = $this->session->data['order_id'];
            //	$data['orderid'] = date('His') . $this->session->data['order_id'];
            $data['returnurl'] = $this->url->link('payment/remita/callback', '', 'SSL');
            $data['notificationurl'] = $this->url->link('payment/remita/notification', '', 'SSL');
            $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
            $data['totalAmount'] = html_entity_decode($data['total']);
            $data['payerName'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
            $data['payerEmail'] = $order_info['email'];
            $data['payerPhone'] = html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');
            $data['button_confirm'] = $this->language->get('button_confirm');
            $uniqueRef = uniqid();
            $data['orderid'] = $uniqueRef . '_' . $data['storeorderid'];
            $paymentOptions = array(
                'REMITA_PAY' => "Remita Account Transfer",
                'Interswitch' => "Verve Card",
                'UPL' => "Visa",
                'MasterCard' => "MasterCard",
                'PocketMoni' => "PocketMoni",
                'BANK_BRANCH' => "Bank Branch",
                'BANK_INTERNET' => "Internet Banking",
                'POS' => "POS",
                'ATM' => "ATM"
                    //Add more static Payment option here...  
            );

            function getEnabledPaymentTypes($paymentOptions, $selected) {

                foreach ($paymentOptions as $code => $name) {
                    if (!in_array($code, $selected)) {
                        unset($paymentOptions[$code]);
                    }
                }

                return $paymentOptions;
            }

            $data['paymentOptions'] = getEnabledPaymentTypes($paymentOptions, $selectedPayment);
            if ($mode == 'test') {
                $data['gateway_url'] = 'http://www.remitademo.net/remita/ecomm/init.reg';
            } else if ($mode == 'live') {
                $data['gateway_url'] = 'https://login.remita.net/remita/ecomm/init.reg';
            }
            $hash_string = $data['remita_mercid'] . $data['remita_servicetypeid'] . $data['orderid'] . $data['total'] . $data['returnurl'] . $data['remita_apikey'];
            $data['hash'] = hash('sha512', $hash_string);
        }

        //1 - Pending Status
        $message = 'Payment Status : Pending';
        $this->model_checkout_order->addOrderHistory($order_id, 1, $message, false);
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/remita.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/remita.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/remita.tpl', $data);
        }
    }

    function remita_transaction_details($orderId) {
        $mert = trim($this->config->get('remita_mercid'));
        $api_key = trim($this->config->get('remita_apikey'));
        $hash_string = $orderId . $api_key . $mert;
        $hash = hash('sha512', $hash_string);
        if (trim($this->config->get('remita_mode')) == 'test') {
            $query_url = 'http://www.remitademo.net/remita/ecomm';
        } else if (trim($this->config->get('remita_mode')) == 'live') {
            $query_url = 'https://login.remita.net/remita/ecomm';
        }
        $url = $query_url . '/' . $mert . '/' . $orderId . '/' . $hash . '/' . 'orderstatus.reg';
        //  Initiate curl
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }

    function updatePaymentStatus($order_id, $response_code, $response_reason, $rrr) {
        switch ($response_code) {
            case "00":
                $message = 'Payment Status : - Successful - Remita Retrieval Reference: ' . $rrr;
                $this->model_checkout_order->addOrderHistory($order_id, trim($this->config->get('remita_processed_status_id')), $message, true);
                break;
            case "01":
                $message = 'Payment Status : - Successful - Remita Retrieval Reference: ' . $rrr;
                $this->model_checkout_order->addOrderHistory($order_id, trim($this->config->get('remita_processed_status_id')), $message, true);
                break;
            case "021":
                $message = 'Payment Status : - Pending Payment - RRR Generated Successfully - Remita Retrieval Reference: ' . $rrr;
                $this->model_checkout_order->addOrderHistory($order_id, 1, $message, true);
                break;
            default:
                //process a failed transaction
                $message = 'Payment Status : - Not Successful - Reason: ' . $response_reason . ' - Remita Retrieval Reference: ' . $rrr;
                //1 - Pending Status
                $this->model_checkout_order->addOrderHistory($order_id, 1, $message, true);
                break;
        }
    }

    public function callback() {
        //echo "Return URL";	
        $data['order_id'] = "";
        $data['response_code'] = "";
        $data['rrr'] = "";
        $data['response_reason'] = "";
        if (isset($_GET['orderID'])) {
            $order_id = $_GET['orderID'];
            $response = $this->remita_transaction_details($order_id);
            $order_details = explode('_', $order_id);
            $remitaorderid = $order_details[0];
            $storeorder_id = $order_details[1];
            $data['order_id'] = $storeorder_id;
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($storeorder_id);
            $data['response_code'] = $response['status'];
            if (isset($response['RRR'])) {
                $data['rrr'] = $response['RRR'];
            }
            $data['response_reason'] = $response['message'];
            $this->updatePaymentStatus($storeorder_id, $data['response_code'], $data['response_reason'], $data['rrr']);
            if (isset($this->session->data['order_id'])) {
                $this->cart->clear();
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['guest']);
                unset($this->session->data['comment']);
                unset($this->session->data['order_id']);
                unset($this->session->data['coupon']);
                unset($this->session->data['reward']);
                unset($this->session->data['voucher']);
                unset($this->session->data['vouchers']);
            }
        }
        $this->language->load('checkout/success');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'text' => $this->language->get('text_checkout'),
            'separator' => $this->language->get('text_separator')
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/success'),
            'text' => $this->language->get('text_success'),
            'separator' => $this->language->get('text_separator')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['fail_continue'] = $this->url->link('checkout/checkout', '', 'SSL');
        $data['continue'] = $this->url->link('account/order/info', 'order_id=' . $data['order_id'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/remita_success.tpl')) {
            return $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/remita_success.tpl', $data));
        } else {
            return $this->response->setOutput($this->load->view('default/template/payment/remita_success.tpl', $data));
        }
    }

    public function notification() {
        $token = $_GET['key'];
        if ($token == trim($this->config->get('remita_token'))) {
            $json = file_get_contents('php://input');
            $arr = json_decode($json, true);
            try {
                if ($arr != null) {
                    foreach ($arr as $key => $orderArray) {
                        $order_id = $orderArray["orderRef"];
                        $response = $this->remita_transaction_details($order_id);
                        $orderId = $response['orderId'];
                        $order_details = explode('_', $orderId);
                        $remitaorderid = $order_details[0];
                        $storeorder_id = $order_details[1];
                        $data['response_code'] = $response['status'];
                        $data['rrr'] = $response['RRR'];
                        $data['response_reason'] = $response['message'];
						$this->load->model('checkout/order');
						$order_info = $this->model_checkout_order->getOrder($storeorder_id);
                        $this->updatePaymentStatus($storeorder_id, $data['response_code'], $data['response_reason'], $data['rrr']);
                    }
                }

                exit('OK');
            } catch (Exception $e) {
                exit('Error Updating Notification: ' . $e);
            }
        }
    }

}

?>