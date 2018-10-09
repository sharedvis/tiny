<?php
/**
 * Created by PhpStorm.
 * User: fadhlan
 * Date: 7/14/17
 * Time: 3:27 PM
 */

namespace Tiny\Helper;

use \Tiny\Phalcon\Models\BaseModel;

class NewCalcFunction
{
    protected $items;
    protected $type;
    protected $payments;

    protected $base_vat;
    protected $income_tax; // pph 23 = 2%, pph 22 = 1.5%

    protected $company_type;
    protected $data;
    protected $is_pkp;

    public function setData($data){
        $this->company_type = $data['company_type'];
        $this->data = $data['data'];
        $this->is_pkp = isset($data['is_pkp']) ? $data['is_pkp'] : 0;
        $this->base_vat = $data['base_vat'];
        $this->income_tax = $data['income_tax'];
        $this->items = $data['items'];
    }

    /**
     * @param $items array (kumpulan items)
     * @param $data array (data induk items - rfq, quotation)
     * @param $company_id integer (company id, if 0 then it's vendor)
     * @return array | mixed
     */
    public function CalcItems(){
        $items = $this->items;

        switch ($this->company_type){
            case 0: // vendor
                $data_transaction = $this->CalcVendor();
                break;

            case 1: // B2B
                $data_transaction = $this->CalcB2B();
                break;

            case 2: // BUMN
                $data_transaction = $this->CalcBumn();
                break;

            case 3: // b2g010
                $data_transaction = $this->CalcB2G010();
                break;

            case 4: // b2g020
                $data_transaction = $this->CalcB2G0202();
                break;

            default:
                return "wrong type (B2G, Vendor, BUMN and B2G010, B2G020)";
                break;
        }

        if($data_transaction['status'] != 200){
            return $data_transaction;
        }

        $data_transaction_items = $data_transaction['data']['items'];

        foreach ($items as $key => $item){
            $items[$key] = array_merge($item,$data_transaction_items[$key]);
        }

        if(!empty($this->data)) {
            $data_transaction_data = $data_transaction['data']['data'];
            $data = array_merge($this->data, $data_transaction_data);
        }
        else{
            $data = $data_transaction['data']['data'];
        }

        $return = [
            'data' => $data,
            'items' => $items
        ];

        return ['status' => 200, 'message' => 'Success get transaction data', 'data' => $return];
    }

    /**
     * Item calculation for B2B customer
     * 
     * @return array|mixed
     */
    private function CalcB2B(){
        $items_data = $this->getItemsPrice();
        if($items_data['status'] != 200){
            return $items_data;
        }

        $data = $items_data['data'];

        $items = [];
        $shipping_costs = [];

        $sub_total = 0;
        $income_tax = 0;

        foreach ($data as $item_id => $item_data){
            // get total price per item
            $item_price = floor($item_data['price_engine']);
            $item_discount = !empty($item_data['discount']) ? floor($item_data['discount']) : 0;
            $item_qty = $item_data['qty'];

            $item_total = ($item_price - $item_discount) * $item_qty;

            // get data for cogs
            $true_price = $item_data['price'] + $item_data['vat'];
            $item_total_cogs = round($true_price * $item_qty);

            $items[$item_id] = [
                'price' => $item_price,
                'qty' => $item_qty,
                'discount' => $item_discount,
                'total' => $item_total,
                'price_id' => $item_data['price_id'],
                'margin' => $item_total - $item_total_cogs,
                'vat' => $item_data['vat']
            ];

            $sub_total += $item_total;

            if(empty($shipping_costs[$item_data['shipping_cost_id']])){
                $shipping_costs[$item_data['shipping_cost_id']] = $item_data['shipping_cost'];
            }

            if($item_data['sku_type'] == BaseModel::$_PRODUCT_TYPE_SERVICE){
                $income_tax += $item_total;
            }
        }

        $shipping_cost_total = 0;

        foreach ($shipping_costs as $shipping_cost)
        {
            $shipping_cost_total += $shipping_cost;
        }

        $tax_basis = $sub_total + $shipping_cost_total;
        $vat = floor($tax_basis * $this->base_vat);
        $income_tax = floor($income_tax * $this->income_tax);
        $grand_total = floor($tax_basis + $vat - $income_tax);

        $transaction = [
            'data' => [
                'sub_total' => $sub_total,
                'shipping_cost' => $shipping_cost_total,
                'tax_basis' => $tax_basis,
                'vat' => $vat,
                'income_tax' => $income_tax,
                'grand_total' => $grand_total
            ],
            'items' => $items
        ];

        return ['status' => 200, 'message' => 'Success get transaction', 'data' => $transaction];
    }

    /**
     * Item calculation for Vendor
     * 
     * @return array|mixed
     */
    private function CalcVendor(){
        $items_data = $this->getItemsPrice();
        if($items_data['status'] != 200){
            return $items_data;
        }

        $data = $items_data['data'];

        $sub_total = 0;
        $income_tax = 0;

        $items = [];

        $sub_total_cogs = 0;
        $income_tax_cogs = 0;

        foreach ($data as $item_key => $item_data){
            // get total price per item
            $item_price = round($item_data['price'], 0, PHP_ROUND_HALF_UP);
            $item_qty = $item_data['qty'];
            $item_total = $item_price * $item_qty;

            // get data for cogs
            $true_price = $item_data['price'] + $item_data['vat'];
            $item_total_cogs = round($true_price * $item_qty);

            $items[$item_key] = [
                'price' => $item_price,
                'qty' => $item_qty,
                'discount' => 0,
                'total' => $item_total,
                'price_id' => $item_data['price_id'],
                'margin' => 0,
                'vat' => $item_data['vat']
            ];

            if($item_data['sku_type'] == BaseModel::$_PRODUCT_TYPE_SERVICE){
                $income_tax += $item_total;
                $income_tax_cogs += $item_total_cogs;
            }

            // calc cogs
            $sub_total += $item_total;
            $sub_total_cogs += round($true_price * $item_qty, 0, PHP_ROUND_HALF_UP);
        }

        // total price normal
        $tax_basis = $sub_total;
        $vat = ($this->is_pkp) ? round($tax_basis * $this->base_vat, 0, PHP_ROUND_HALF_UP) : 0;
        $income_tax = round($income_tax * $this->income_tax, 0, PHP_ROUND_HALF_UP);
        $grand_total = $tax_basis + $vat - $income_tax;

        // total price cogs
        $tax_basis_cogs = $sub_total_cogs;
        $grand_total_cogs = $tax_basis_cogs - $income_tax;

        $rounding = ($grand_total_cogs - $grand_total);

        $transaction = [
            'data' => [
                'sub_total' => $sub_total,
                'shipping_cost' => 0,
                'tax_basis' => $tax_basis,
                'vat' => $vat,
                'income_tax' => $income_tax,
                'grand_total' => $grand_total_cogs,
                'rounding' => $rounding
            ],
            'items' => $items
        ];

        return ['status' => 200, 'message' => 'Success get transaction', 'data' => $transaction];
    }

    /**
     * Item calculation for BUMN Customer
     * 
     * @return array|mixed
     */
    private function CalcBumn(){
        $items_data = $this->getItemsPrice();
        if($items_data['status'] != 200){
            return $items_data;
        }

        $data = $items_data['data'];

        $items = [];
        $shipping_costs = [];

        $sub_total = 0;
        $income_tax = 0;

        foreach ($data as $item_id => $item_data){
            // get total price per item
            $item_price = floor($item_data['price_engine']);
            $item_discount = !empty($item_data['discount']) ? floor($item_data['discount']) : 0;
            $item_qty = $item_data['qty'];

            $item_total = ($item_price - $item_discount) * $item_qty;

            // get data for cogs
            $true_price = $item_data['price'] + $item_data['vat'];
            $item_total_cogs = round($true_price * $item_qty);

            $items[$item_id] = [
                'price' => $item_price,
                'qty' => $item_qty,
                'discount' => $item_discount,
                'total' => $item_total,
                'price_id' => $item_data['price_id'],
                'margin' => $item_total - $item_total_cogs,
                'vat' => $item_data['vat']
            ];

            $sub_total += $item_total;

            if(empty($shipping_costs[$item_data['shipping_cost_id']])){
                $shipping_costs[$item_data['shipping_cost_id']] = $item_data['shipping_cost'];
            }

            if($item_data['sku_type'] == BaseModel::$_PRODUCT_TYPE_SERVICE){
                $income_tax += $item_total;
            }
        }

        $shipping_cost_total = 0;

        foreach ($shipping_costs as $shipping_cost)
        {
            $shipping_cost_total += $shipping_cost;
        }

        $tax_basis = $sub_total + $shipping_cost_total;
        $vat = floor($tax_basis * $this->base_vat);
        $income_tax = floor($income_tax * $this->income_tax);
        $grand_total = floor($tax_basis + $vat - $income_tax);

        $transaction = [
            'data' => [
                'sub_total' => $sub_total,
                'shipping_cost' => $shipping_cost_total,
                'tax_basis' => $tax_basis,
                'vat' => $vat,
                'income_tax' => $income_tax,
                'grand_total' => $grand_total
            ],
            'items' => $items
        ];

        return ['status' => 200, 'message' => 'Success get transaction', 'data' => $transaction];
    }

    /**
     * Item calculation for B2G010
     * 
     * @return array|mixed
     */
    private function CalcB2G010(){
        return [];
    }

    /**
     * Item calculation for B2G020
     * 
     * @return array|mixed
     */
    private function CalcB2G0202(){
        return [];
    }

    /**
     * Get items price from company product data
     * 
     * @return array|mixed
     */
    private function getItemsPrice(){
        $items = $this->items;

        $data_price = [];
        foreach ($items as $key_item => $item){
            $company_product_data = $item['company_product_data'];
            $prices = $company_product_data['prices'];
            $price_rules = $company_product_data['sku']['price_rules'];
            $product = $company_product_data['product'];

            $base_price = 0;
            foreach ($price_rules as $key => $price_rule){
                if($item['qty'] >= $price_rule['min_qty'] && $item['qty'] <= $price_rule['max_qty']){
                    $keys = array_keys(array_column($prices, 'product_pricerules_id'), $price_rule['id']);
                    $base_price = [
                        'vat' => $prices[$keys[0]]['vat'],
                        'price' => $prices[$keys[0]]['price'],
                        'price_id' => $prices[$keys[0]]['id']
                    ];
                }
            }

            if($base_price == 0){
                $lastPriceRule = sizeof($price_rules) - 1;

                $keys = array_keys(array_column($prices, 'product_pricerules_id'), $price_rules[$lastPriceRule]['id']);

                $base_price = [
                    'vat' => $prices[$keys[0]]['vat'],
                    'price' => $prices[$keys[0]]['price'],
                    'price_id' => $prices[$keys[0]]['id']
                ];
            }

            $shipping_cost_data = $item['shipping_cost_data'];
            $shipping_cost = $shipping_cost_data['shipping_cost'];
            $shipping_cost_id = $shipping_cost_data['shipping_cost_id'];

            $data_price[$key_item] = [
                'price_engine' => !empty($item['price']) ? $item['price'] : 0, // price engine
                'qty' => $item['qty'],
                'discount' => !empty($item['discount']) ? $item['discount'] : 0,
                'company_product_id' => $item['company_product_id'],
                'vat' => $base_price['vat'],
                'price' => $base_price['price'],
                'shipping_cost' => $shipping_cost,
                'shipping_cost_id' => $shipping_cost_id,
                'sku_type' => $product['product_type_id'],
                'price_id' => $base_price['price_id']
            ];
        }

        return ['status' => 200, 'message' => "success get data for calculate transaction", 'data' => $data_price];
    }
}
