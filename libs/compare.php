<?php

class Compare
{

    private $session;
    private $db;
    private $load;
    private $list = array();

    public function __construct($registry)
    {
        $this->session = $registry->get('session');
        $this->db = $registry->get('db');
        $this->load = $registry->get('load');

        $this->load->model('catalog/product');
        $this->load->model('catalog/category');

        $this->list = &$this->load->session->data['compare'];

        if (!isset($this->session->data['compare'])) {
            $this->clear();
        }
    }

    public function addProduct($product_id)
    {
        $product_info = $this->load->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            $product_category = $this->getCategoryByProduct($product_id);

            if ($product_category) {
                $category_id = $product_category['category_id'];

                $this->addCategory($category_id);

                if (!in_array($product_id, $this->list[$category_id])) {
                    $this->list[$category_id][] = $product_id;
                }

                return true;
            }
        }

        return false;
    }

    public function removeProduct($product_id)
    {
        if ($this->productExists($product_id)) {
            $product_category = $this->getCategoryByProduct($product_id);

            if ($product_category) {
                $category_id = $product_category['category_id'];

                foreach ($this->list as & $products) {
                    if (($key = array_search($product_id, $products)) !== false) {
                        unset($products[$key]);
                        
                        break;
                    }
                }
                
                if (!count($this->list[$category_id])) {
                    $this->removeCategory($category_id);
                }
            }
        }

        return true;
    }

    public function productExists($product_id)
    {
        foreach ($this->list as $products) {
            if (in_array($product_id, $products)) {
                return true;
            }
        }

        return false;
    }
    
    public function getCountProducts()
    {
        $count = 0;
        
        foreach ($this->list as $products) {
            $count += count($products);
        }

        return $count;
    }
    
    public function getProductsByCategory($category_id)
    {
        if ($this->categoryExists($category_id)) {
            return $this->list[$category_id];
        }
        
        return false;
    }

    public function addCategory($category_id)
    {
        if (!$this->categoryExists($category_id)) {
            $this->list[$category_id] = array();
        }

        return true;
    }

    public function removeCategory($category_id)
    {
        if ($this->categoryExists($category_id)) {
            unset($this->list[$category_id]);
        }

        return true;
    }

    public function categoryExists($category_id)
    {
        return isset($this->list[$category_id]);
    }

    public function getCategoryByProduct($product_id)
    {
        $categories = $this->load->model_catalog_product->getCategories($product_id);

        if ($categories) {
            return $this->load->model_catalog_category->getCategory($categories[0]['category_id']);
        }

        return false;
    }

    public function clear()
    {
        $this->session->data['compare'] = array();
    }

    public function debug()
    {
        print_r($this->list);
    }

}
