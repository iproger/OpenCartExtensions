<?php

class ControllerModuleSingleclick extends Controller
{

    private $error = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        
        $this->language->load('module/singleclick');
        $this->load->model('catalog/product');
        $this->load->model('module/singleclick');
    }

    public function index()
    {
        $result = array('success' => false);
        $products_list = array();
        $messages = array();
        $customer_name = 'Клиент';
        $config_url = parse_url($this->config->get('config_url'));
        $products = array();
        
        $this->data['email_subject'] = $this->language->get('email_subject');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            switch ($this->request->post['type']) {
                case 'product':
                    $products_list[] = $this->model_catalog_product->getProduct($this->request->post['product_id']);
                    break;
                case 'cart':
                    $products_list = $this->cart->getProducts();
                    break;
            }
            
            //print_r($products_list);exit;
            
            if ($products_list) {
                foreach ($products_list as $product) {
                    $quantity = 1;
                    
                    if (isset($product['quantity'])) {
                        $quantity = $product['quantity'];
                    }
                    
                    $price = $this->currency->format($product['price']);
                    
                    $products[] = $product['name'] . " (" . $price . ")" . PHP_EOL .
                        $this->language->get('text_model') . $product['model'] . PHP_EOL .
                        $this->language->get('text_quantity') . $quantity . PHP_EOL;
                }
                
                $message =
                    $this->language->get('text_client') . $customer_name . PHP_EOL .
                    $this->language->get('text_phone') . $this->request->post['customer_phone'] . PHP_EOL .
                    $this->language->get('text_date') . date('d.m.Y H:i') . PHP_EOL . PHP_EOL . PHP_EOL .
                    implode($this->language->get('email_split') . PHP_EOL, $products);
                
                $this->model_module_singleclick->add(array(
                    'name' => $this->db->escape($customer_name),
                    'phone' => $this->db->escape($this->request->post['customer_phone']),
                    'message' => $this->db->escape($message),
                ));

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');
                //$mail->setTo($this->config->get('config_email'));
                $mail->setTo('proger.mixa@gmail.com');
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($customer_name);
                $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $config_url['host'], ENT_QUOTES, 'UTF-8')));
                $mail->setText(strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8')));
                $mail->send();

                $result['success'] = true;
            }
        } else {
            $result['error'] = $this->error;
        }
        
        $this->response->setOutput(json_encode($result));
    }

    private function validate()
    {
        if (!isset($this->request->post['customer_phone']) || (utf8_strlen($this->request->post['customer_phone']) < 5) || (utf8_strlen($this->request->post['customer_phone']) > 32)) {
            $this->error = $this->language->get('error_phone');
        }
        
        return !$this->error;
    }

}
