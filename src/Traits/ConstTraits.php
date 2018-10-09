<?php

namespace Tiny\Traits;

trait ConstTraits {

    /**
     * Shipping Cost Type
     */
    public static $_SHIPPING_COST_PER_BUNDLING = 0;
    public static $_SHIPPING_COST_PER_ITEM = 1;

    /**
     * Sharedvis's Account Purpose Type
     */
    public static $_ACCOUNT_PURPOSE_TYPE_B2B = 1;
    public static $_ACCOUNT_PURPOSE_TYPE_B2G = 2;
    public static $_ACCOUNT_PURPOSE_TYPE_BUMN = 3;
    public static $_ACCOUNT_PURPOSE_TYPE_VENDOR = 4;

    /**
     * Company Buyer Type
     */
    public static $_COMPANY_TYPE_B2B = 1;
    public static $_COMPANY_TYPE_BUMN = 2;
    public static $_COMPANY_TYPE_B2G010 = 3;
    public static $_COMPANY_TYPE_B2G020 = 4;

    /**
     * Budget Meta Keys
     */
    public static $_BUDGET_META_KEYS_USER = 'user';
    public static $_BUDGET_META_KEYS_CATEGORY = 'category';
    public static $_BUDGET_META_KEYS_SKU = 'sku';
    public static $_BUDGET_META_KEYS_DEPARTMENT = 'department';

    /**
     * Cart Status Constant
     */
    public static $_STATUS_CART_NEW = 0;
    public static $_STATUS_CART_ORDERED = 1;

    /**
     * Shipment type Constant
     */
    public static $_INDIRECT_SHIPMENT = 1;

    /**
     * MSDS (Material Safety Data Sheet)
     */
    public static $_MSDS_TYPE_SOLID = 0;
    public static $_MSDS_TYPE_EXPLOSIVE = 1;
    public static $_MSDS_TYPE_GAS = 2;
    public static $_MSDS_TYPE_LIQUID = 3;
    public static $_MSDS_TYPE_FRESH = 4;
    public static $_MSDS_TYPE_METAL = 5;

    /**
     * Product Type
     */
    public static $_PRODUCT_TYPE_GOOD = 1;
    public static $_PRODUCT_TYPE_SERVICE = 2;

    /**
     * SKU Origin
     */
    public static $_SKU_ORIGIN_IMPORT = 1;
    public static $_SKU_ORIGIN_LOCAL = 2;

    /**
     * SKU Catalog Type
     */
    public static $_SKU_CATALOG_INHOUSE = 1;
    public static $_SKU_CATALOG_ICECAT = 2;

    /**
     * VAT Value
     */
    public static $_VAT_CALC_VALUE = 1.1;

    /**
     * Transaction Valid Days
     */

    public static $_VALID_DAYS = 3;
    public static $_RFQ_VALID_DAYS = 2;
    public static $_QUOTATION_VALID_DAYS = 5;
    public static $_POV_VALID_DAYS = 1;

    /**
     * Transaction Type Constant
     */
    public static $_TYPE_COSTUMER = 1;
    public static $_TYPE_VENDOR   = 2;

    /**
     * Budget Logs Transaction Type Constant
     */
    public static $_TYPE_BUDGET_PURCHASING = 1;
    public static $_TYPE_BUDGET_ADJ_DOWN   = 2;
    public static $_TYPE_BUDGET_ADJ_UP     = 3;

    /**
     * Warranty Type Constant
     */
    public static $_WARRANTY_NO_WARRANTY = 0;
    public static $_WARRANTY_DISTRIBUTOR = 1;
    public static $_WARRANTY_OFFICIAL = 2;
    public static $_WARRANTY_INTERNATIONAL = 3;

    /**
     * Input type
     */
    public static $_INPUT_TYPE_FREETEXT = 1;
    public static $_INPUT_TYPE_OPTION = 2;
    public static $_INPUT_TYPE_NUMBER = 3;
    public static $_INPUT_TYPE_MULTI_OPTION = 4;

    /**
     * Other Statuses
     */
    public static $_STATUS_ACTIVE = 99;
    public static $_STATUS_INACTIVE = 98;
    public static $_STATUS_DELETED = 97;
    public static $_STATUS_PENDING = 96;

    /**
     * Item Statuses
     */
    public static $_STATUS_ITEM_WAITING = 10;
    public static $_STATUS_ITEM_FULFILLED = 11;
    public static $_STATUS_ITEM_UNFULFILLED = 12;
    public static $_STATUS_ITEM_CHANGED = 13;
    public static $_STATUS_ITEM_FULFILLED_CHANGED = 14;
    public static $_STATUS_ITEM_UNFULFILLED_CHANGED = 15;

    /**
     * Payment status
     */
    public static $_STATUS_PAYMENT_WAITING = 1;
    public static $_STATUS_PAYMENT_CONFIRMED= 2;
    public static $_STATUS_PAYMENT_REJECTED = 3;
    public static $_STATUS_PAYMENT_ON_QUEUE = 4;

    /**
     * Invoice status
     */
    public static $_STATUS_INVOICE_PROFORMA = 1;
    public static $_STATUS_INVOICE_UNPAID = 2;
    public static $_STATUS_INVOICE_PAID = 3;
    public static $_STATUS_INVOICE_SETTLED = 4;
    public static $_STATUS_INVOICE_OVERDUE = 5;

    /**
     * PR Statuses
     */
    public static $_STATUS_PR_NEW = 1;
    public static $_STATUS_PR_ON_PROGRESS = 2;
    public static $_STATUS_PR_APPROVED = 3;
    public static $_STATUS_PR_REJECTED = 4;
    public static $_STATUS_PR_EXPIRED = 5;

    /**
     * PO Statuses
     */
    public static $_STATUS_PO_NEW = 1;     // New, Waiting
    public static $_STATUS_PO_NEED_CONFIRM = 2;    // Need Confirm, Waiting Confirmation, On Progress
    public static $_STATUS_PO_ON_PROGRESS = 3;    // On Progress, Dalam Proses
    public static $_STATUS_PO_CONFIRMED = 4;    // Order Accepted, Confirmed
    public static $_STATUS_PO_READY_TO_SHIP = 5;    // Ready To Be Shipped
    public static $_STATUS_PO_REJECTED = 6;    // Rejected
    public static $_STATUS_PO_CANCELED = 7;    // Canceled
    public static $_STATUS_PO_PARTIALLY_SHIPPED = 8;   // Partially Shipped
    public static $_STATUS_PO_FULLY_SHIPPED = 9;   // Fully Shipped
    public static $_STATUS_PO_DELIVERED = 10;   // Delivered, Barang Diterima
    public static $_STATUS_PO_CLOSED = 11;     // Closed, Selesai
    public static $_STATUS_PO_ON_QUEUE = 12;     // On Queue

    /**
     * Quotation Statuses
     */
    public static $_STATUS_QUOTATION_NEW = 1;  // New, Waiting Response
    public static $_STATUS_QUOTATION_ACCEPTED = 2;     // Accepted
    public static $_STATUS_QUOTATION_SUBMITTED = 3;    // Submitted, PR Created
    public static $_STATUS_QUOTATION_REJECTED = 4;     // Rejected
    public static $_STATUS_QUOTATION_REJECTED_BY_MBIZ = 10; // Rejected by Sharedvis
    public static $_STATUS_QUOTATION_REJECTED_BY_CUSTOMER = 11; // Rejected by Customer
    public static $_STATUS_QUOTATION_CANCELLED = 12; // Cancelled (Rejected)
    public static $_STATUS_QUOTATION_CANCELLED_BY_MBIZ = 13; // Cancelled (Rejected) by Sharedvis
    public static $_STATUS_QUOTATION_EXPIRED_BY_MBIZ = 5;  // Expired, Expired By MBIZ
    public static $_STATUS_QUOTATION_EXPIRED_BY_CUSTOMER = 6;  // Expired, Expired By Customer
    public static $_STATUS_QUOTATION_ORDERED = 7;  // PO Created, Ordered
    public static $_STATUS_QUOTATION_PR_CREATED = 8;  // PR Created
    public static $_STATUS_QUOTATION_ON_QUEUE = 9;  // On progress

    /**
     * RFQ Statuses
     */
    public static $_STATUS_RFQ_PRE_NEW = 13;    // New, Waiting Response
    public static $_STATUS_RFQ_NEW = 1;    // New, Waiting Response
    public static $_STATUS_RFQ_CONFIRMED = 2;  // Terkonfirmasi, Need MBIZ COnfirmation
    public static $_STATUS_RFQ_ON_PROGRESS = 3;    // On Progress, Dalam Proses
    public static $_STATUS_RFQ_SUBMITTED = 4;  // Quotation Submitted
    public static $_STATUS_RFQ_PARTIALLY_ACCEPTED = 5; // Partially Accepted
    public static $_STATUS_RFQ_ACCEPTED = 6;   // Accepted
    public static $_STATUS_RFQ_REJECTED_BY_MBIZ = 7;   // Rejected By Sharedvis
    public static $_STATUS_RFQ_REJECTED_BY_VENDOR = 8;     // Rejected By Vendor
    public static $_STATUS_RFQ_EXPIRED_BY_MBIZ = 9;    // Expired By MBIZ
    public static $_STATUS_RFQ_EXPIRED_BY_VENDOR = 10; // Expired By Vendor
    public static $_STATUS_RFQ_CANCELED = 11;  // Canceled, DIbatalkan
    public static $_STATUS_RFQ_ON_QUEUE = 12;  // On Progress

    /**
     * Status Reasons
     */
    public static $_STATUS_REASON_OUT_OF_STOCK = 1;
    public static $_STATUS_REASON_DISCONTINUED = 2;
    public static $_STATUS_REASON_WRONG_PRICE = 3;
    public static $_STATUS_REASON_OTHER = 4;

    /**
     * Term of payment options
     */
    public static $_TOP_OPTION_0 = 0;
    public static $_TOP_OPTION_7 = 7;
    public static $_TOP_OPTION_14 = 14;
    public static $_TOP_OPTION_20 = 20;
    public static $_TOP_OPTION_30 = 30;
    public static $_TOP_OPTION_45 = 45;
    public static $_TOP_OPTION_60 = 60;

    /**
     * Adjustment Accounts
     */
    public static $_ADJUSTMENT_SHIPPING_FINE = 1;
    public static $_ADJUSTMENT_INFAQ = 2;
    public static $_ADJUSTMENT_BANK_EXPENSE = 3;
    public static $_ADJUSTMENT_BOS_FUND = 4;
    public static $_ADJUSTMENT_NOT_PPH_22_OBJECT = 5;
    public static $_ADJUSTMENT_TAX_MISCALCULATION = 6;
    public static $_ADJUSTMENT_OVERPAYMENT = 7;
    public static $_ADJUSTMENT_ENTERTAINT_EXPENSE = 8;
    public static $_ADJUSTMENT_OTHERS = 99;

    /**
     * Shipping Group Statuses
     */
    public static $_STATUS_SHIPPING_PARTIALLY_SHIPPED = 8;   // Partially Shipped
    public static $_STATUS_SHIPPING_FULLY_SHIPPED = 9;   // Fully Shipped
    public static $_STATUS_SHIPPING_DELIVERED = 10;   // Delivered, Barang Diterima

    /**
     * Status File Management
     */
    public static $_MANAGEMENT_FILE_READY = 99;
    public static $_MANAGEMENT_FILE_ON_PROGRESS = 98;
    public static $_MANAGEMENT_FILE_FINISH = 97;
    public static $_MANAGEMENT_FILE_FAILED = 96;
    public static $_MANAGEMENT_FILE_WAITING= 95;

    /**
     * File Management type request (TR)
     */
    public static $_MANAGEMENT_FILE_TR_DOWNLOAD = 1;
    public static $_MANAGEMENT_FILE_TR_UPLOAD = 2;

    /**
     * File Management type task (TT)
     */
    public static $_MANAGEMENT_FILE_TT_CREATE = 1; //mass create company_product
    public static $_MANAGEMENT_FILE_TT_UPDATE = 2; //mass update company_product
    public static $_MANAGEMENT_FILE_TT_REPORT_RFQ = 3;
    public static $_MANAGEMENT_FILE_TT_REPORT_QUOTATION = 4;
    public static $_MANAGEMENT_FILE_TT_REPORT_PR = 5;
    public static $_MANAGEMENT_FILE_TT_REPORT_PO = 6;
    public static $_MANAGEMENT_FILE_TT_REPORT_PAYMENT = 7;
    public static $_MANAGEMENT_FILE_TT_REPORT_INVOICE = 8;
    public static $_MANAGEMENT_FILE_TT_REPORT_USERS = 9;
    public static $_MANAGEMENT_FILE_TT_REPORT_COMPANY_PRODUCT = 10;
    public static $_MANAGEMENT_FILE_TT_REPORT_SHIPPING_ADDRESS = 11;
    public static $_MANAGEMENT_FILE_TT_REPORT_BILLING_ADDRESS = 12;

    /**
     * Mass Upload
     */
    public static $_MASS_UPLOAD_MAX_RECORDS = 20000;

    /**
     * Landing Page
     */
    public static $_LANDING_PAGE_STATIC = 1;
    public static $_LANDING_PAGE_PROMO = 2;
    public static $_LANDING_PAGE_SELLER = 3;

    /**
     * Client Demand Status
     */
    public static $_STATUS_CDN_NEED_RESPONSE_CPD = 1;
    public static $_STATUS_CDN_ON_PROGRESS_CPD = 2;
    public static $_STATUS_CDN_ACCEPTED = 3;
    public static $_STATUS_CDN_NEED_FEEDBACK_CA = 4;
    public static $_STATUS_CDN_CANCELLED = 5;
    public static $_STATUS_CDN_DONE = 6;

    /**
     * Product Demand Status
     */
    public static $_STATUS_CDN_PRODUCT_NEED_RESPONSE_CONTENT = 1;
    public static $_STATUS_CDN_PRODUCT_ON_PROGRESS_CONTENT = 2;
    public static $_STATUS_CDN_PRODUCT_SKU_READY = 3;
    public static $_STATUS_CDN_PRODUCT_SKU_LIVE = 4;
    public static $_STATUS_CDN_PRODUCT_NEED_FEEDBACK_CPD = 5;
    public static $_STATUS_CDN_PRODUCT_NEED_FEEDBACK_CONTENT = 6;

    /**
     * Product Demand SKU Status
     */
    public static $_STATUS_CDN_PRODUCT_SKU_SKU_PENDING = 1;
    public static $_STATUS_CDN_PRODUCT_SKU_SKU_NOT_LIVE = 2;
    public static $_STATUS_CDN_PRODUCT_SKU_SKU_LIVE = 3;

    /**
     * Client Demand Category
     */
    public static $_CDN_CATEGORY_GG = 1;
    public static $_CDN_CATEGORY_OTOMOTIVE = 2;
    public static $_CDN_CATEGORY_MRO = 3;
    public static $_CDN_CATEGORY_SERVICES = 4;
    public static $_CDN_CATEGORY_ELECTRONIC = 5;
    public static $_CDN_CATEGORY_LIFESTYLE = 6;
    public static $_CDN_CATEGORY_IT = 7;

    /**
     * Client Demand Log
     */
    public static $_CDN_LOG_EDIT = 1;
    public static $_CDN_LOG_FEEDBACK_CA = 2;
    public static $_CDN_LOG_FEEDBACK_CPD = 3;
    public static $_CDN_LOG_FEEDBACK_CONTENT = 4;
    public static $_CDN_LOG_CANCELLED = 5;

    /**
     * Client Demand Cancellation Reason
     */
    public static $_CDN_CANCEL_INSUFFICIENT_BUDGET_REASON = 1;
    public static $_CDN_CANCEL_PRODUCT_COMPLETION_TOO_LONG_REASON = 2;
    public static $_CDN_CANCEL_DEMAND_COULD_NOT_BE_COMPLETED_REASON = 3;
    public static $_CDN_CANCEL_OTHER = 4;

    /**
     * Client Demand Feedback CA Reason
     */
    public static $_CDN_FEEDBACK_CA_PRODUCT_DETAIL_NOT_COMPLETE_REASON = 1;
    public static $_CDN_FEEDBACK_CA_PRODUCT_DETAIL_NOT_MATCH_REASON = 2;
    public static $_CDN_FEEDBACK_CA_IMG_NOT_MEET_STANDARD_REASON = 3;
    public static $_CDN_FEEDBACK_CA_OTHER = 4;

    /**
     * Client Demand Feedback CPD Reason
     */
    public static $_CDN_FEEDBACK_CPD_PRODUCT_DETAIL_NOT_COMPLETE_REASON = 1;
    public static $_CDN_FEEDBACK_CPD_PRODUCT_DETAIL_NOT_MATCH_REASON = 2;
    public static $_CDN_FEEDBACK_CPD_IMG_NOT_MEET_STANDARD_REASON = 3;
    public static $_CDN_FEEDBACK_CPD_INSUFFICIENT_WARRANTY_INFO_REASON = 4;
    public static $_CDN_FEEDBACK_CPD_OTHER = 5;

     /**
     * Client Demand Feedback Content Reason
     */
    public static $_CDN_FEEDBACK_CONTENT_SKU_ID_AND_PRODUCT_NAME_NOTMATCH_REASON = 1;
    public static $_CDN_FEEDBACK_CONTENT_WRONG_SKU_ID_REASON = 2;
    public static $_CDN_FEEDBACK_CONTENT_OTHER = 3;

    /**
     * MOU Status
     */
    public static $_STATUS_MOU_BUYER_DRAFT = 1;
    public static $_STATUS_MOU_BUYER_WAITING = 2;
    public static $_STATUS_MOU_BUYER_ACTIVE = 3;
    public static $_STATUS_MOU_BUYER_INACTIVE = 4;
    public static $_STATUS_MOU_BUYER_CLOSED = 5;

    /**
     * MOU Transaction Status
     */
    public static $_STATUS_MOU_BUYER_TRANSACTION_IN_PROGRESS = 1;
    public static $_STATUS_MOU_BUYER_TRANSACTION_SETTLED = 2;
    public static $_STATUS_MOU_BUYER_TRANSACTION_PR_CREATED = 3;

    /**
     * Get Available Company Type
     */
    public static function getCompanyTypes(){
        return [
            self::$_COMPANY_TYPE_B2B => "B2B",
            self::$_COMPANY_TYPE_BUMN => "BUMN",
            self::$_COMPANY_TYPE_B2G010 => "B2G - 010",
            self::$_COMPANY_TYPE_B2G020 => "B2G - 020"
        ];
    }

    /**
     * Get Available Budget Meta Keys
     *
     * @return array
     */
    public static function getBudgetMetaKeys()
    {
        return [
            self::$_BUDGET_META_KEYS_USER,
            self::$_BUDGET_META_KEYS_CATEGORY,
            self::$_BUDGET_META_KEYS_SKU,
            self::$_BUDGET_META_KEYS_DEPARTMENT
        ];
    }

    /**
     * Get options for Budget Transaction Type
     * @return array
     */
    public static function getBudgetLogTypes()
    {
        return [
            self::$_TYPE_BUDGET_PURCHASING  => 'Purchasing',
            self::$_TYPE_BUDGET_ADJ_DOWN    => 'Adjustment down',
            self::$_TYPE_BUDGET_ADJ_UP      => 'Adjustment up'
        ];
    }

    /**
     * Get options for Term of Payment
     * @return array
     */
    public static function getTopOptions()
    {
        return [
            self::$_TOP_OPTION_0 => '0 Days',
            self::$_TOP_OPTION_7 => '7 Days',
            self::$_TOP_OPTION_14 => '14 Days',
            self::$_TOP_OPTION_20 => '20 Days',
            self::$_TOP_OPTION_30 => '30 Days',
            self::$_TOP_OPTION_45 => '45 Days',
            self::$_TOP_OPTION_60 => '60 Days'
        ];
    }

    /**
     * Get all status reasons
     * @return array
     */
    public static function getStatusReasons($status = 0)
    {
        $statuses = [
            self::$_STATUS_REASON_OUT_OF_STOCK => [
                'sv' => 'Out of Stock',
                'v'  => 'Stok Habis'
            ],
            self::$_STATUS_REASON_DISCONTINUED => [
                'sv' => 'Discontinued',
                'v'  => 'Produk Yang Dihentikan'
            ],
            self::$_STATUS_REASON_WRONG_PRICE => [
                'sv' => 'Wrong Price',
                'v'  => 'Salah Harga'
            ],
            self::$_STATUS_REASON_OTHER => [
                'sv' => 'Other',
                'v'  => 'Lainnya'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get all statuses
     * @return array
     */
    public static function getStatuses($status = 0)
    {
        $type = '';
        $caller = strtolower(static::class);

        if (strpos($caller, 'item') !== false) { // item should filter first
            $type = "Item";
        } elseif (strpos($caller, 'purchaseorder') !== false) {
            $type = "PurchaseOrder";
        } elseif (strpos($caller, 'quotation') !== false) {
            $type = "Quotation";
        } elseif (strpos($caller, 'rfq') !== false) {
            $type = "Rfq";
        } elseif (strpos($caller, 'purchaserequest') !== false) {
            $type = "PurchaseRequest";
        } elseif (strpos($caller, 'invoice') !== false) {
            $type = "Invoice";
        } elseif (strpos($caller, 'cart') !== false) {
            $type = "Cart";
        } elseif (strpos($caller, 'payment') !== false) {
            $type = "Payment";
        } elseif (strpos($caller, 'shipping') !== false) {
            $type = "Shipping";
        } else if (strpos($caller, 'cdn') !== false) {
            $type = "Cdn";
        } else if (strpos($caller, 'clientproductdemand') !== false) {
            $type = "ClientProductDemand";
        } else if (strpos($caller, 'moubuyer') !== false) {
            $type = "MouBuyer";
        }

        switch ($type)
        {
            case "PurchaseOrder"            : return self::getPOStatuses($status);
            case "Quotation"                : return self::getQuotationStatuses($status);
            case "Rfq"                      : return self::getRfqStatuses($status);
            case "PurchaseRequest"          : return self::getPRStatuses($status);
            case "Invoice"                  : return self::getInvoiceStatuses($status);
            case "Cart"                     : return self::getCartStatuses($status);
            case "Payment"                  : return self::getPaymentStatuses($status);
            case "Shipping"                 : return self::getShippingStatuses($status);
            case "Item"                     : return self::getItemStatuses($status);
            case "Cdn"                      : return self::getCDNStatuses($status);
            case "ClientProductDemand"      : return self::getProductDemandStatuses($status);
            case "MouBuyer"                 : return self::getMouBuyerStatuses($status);
            default                 : return [
                self::$_STATUS_ACTIVE => "Active",
                self::$_STATUS_INACTIVE => "Inactive",
                self::$_STATUS_DELETED => "Deleted",
                self::$_STATUS_PENDING => "Pending",
            ];
        }
    }

    /**
     * Get array of string status
     * c: customer
     * v: vendor
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getItemStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_ITEM_WAITING => [
                'sc' => 'Waiting',
                'sv' => 'Waiting',
                'c' => 'Menunggu',
                'v' => 'Menunggu'
            ],
            self::$_STATUS_ITEM_FULFILLED => [
                'sc' => 'Fulfilled',
                'sv' => 'Fulfilled',
                'c' => 'Terima',
                'v' => 'Terima'
            ],
            self::$_STATUS_ITEM_UNFULFILLED => [
                'sc' => 'Unfulfilled',
                'sv' => 'Unfulfilled',
                'c' => 'Tolak',
                'v' => 'Tolak'
            ],
            self::$_STATUS_ITEM_CHANGED => [
                'sc' => 'Changed',
                'sv' => 'Changed',
                'c' => 'Diganti',
                'v' => 'Diganti'
            ],
            self::$_STATUS_ITEM_FULFILLED_CHANGED => [
                'sc' => 'Fulfilled Changed',
                'sv' => 'Fulfilled Changed',
                'c' => 'Diganti',
                'v' => 'Diganti'
            ],
            self::$_STATUS_ITEM_UNFULFILLED_CHANGED => [
                'sc' => 'Unfulfilled Changed',
                'sv' => 'Unfulfilled Changed',
                'c' => 'Diganti',
                'v' => 'Diganti'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of string status
     * c: customer
     * sc: sourcing customer
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getPaymentStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_PAYMENT_WAITING => [
                'sc' => 'Waiting',
                'c'  => 'Menunggu'
            ],
            self::$_STATUS_PAYMENT_CONFIRMED => [
                'sc' => 'Confirmed',
                'c'  => 'Terkonfirmasi'
            ],
            self::$_STATUS_PAYMENT_REJECTED => [
                'sc' => 'Rejected',
                'c'  => 'Ditolak'
            ],
            self::$_STATUS_PAYMENT_ON_QUEUE => [
                'sc' => 'On Queue',
                'c'  => 'Sedang Diproses'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of string status
     * c: customer
     * sc: sourcing customer
     * sv: sourcing vendor
     * v:  vendor
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getCartStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_CART_NEW => [
                'sc' => 'New',
                'c'  => 'Baru',
                'v'  => 'Baru',
                'sv' => 'New'
            ],
            self::$_STATUS_CART_ORDERED => [
                'sc' => 'Ordered',
                'c'  => 'Sudah Dipesan',
                'v'  => 'Sudah Dipesan',
                'sv' => 'Ordered'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of string status
     * c: customer
     * sc: sourcing customer
     * sv: sourcing vendor
     * v:  vendor
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getInvoiceStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_INVOICE_PROFORMA => [
                'sc' => 'Proforma',
                'c'  => 'Proforma',
                'v'  => 'Proforma',
                'sv' => 'Proforma'
            ],
            self::$_STATUS_INVOICE_UNPAID => [
                'sc' => 'Unpaid',
                'c'  => 'Menunggu Pembayaran',
                'v'  => 'Belum Dibayar',
                'sv' => 'Unpaid'
            ],
            self::$_STATUS_INVOICE_PAID => [
                'sc' => 'Paid',
                'c'  => 'Telah Dibayar',
                'v'  => 'Telah Dibayar',
                'sv' => 'Paid'
            ],
            self::$_STATUS_INVOICE_SETTLED => [
                'sc' => 'Settled',
                'c'  => 'Selesai'
            ],
            self::$_STATUS_INVOICE_OVERDUE => [
                'sc' => 'Overdue',
                'c'  => 'Jatuh Tempo',
                'v'  => 'Telat Bayar',
                'sv' => 'Overdue'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of string status
     * c: customer
     * sc: sourcing customer
     * No vendor data, because PR only belongs to customer
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getPRStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_PR_NEW => [
                'sc' => 'New',
                'c'  => 'Baru'
            ],
            self::$_STATUS_PR_ON_PROGRESS => [
                'sc' => 'On Progress',
                'c'  => 'Butuh Persetujuan'
            ],
            self::$_STATUS_PR_APPROVED => [
                'sc' => 'Approved',
                'c'  => 'Disetujui'
            ],
            self::$_STATUS_PR_REJECTED => [
                'sc' => 'Rejected',
                'c'  => 'Ditolak'
            ],
            self::$_STATUS_PR_EXPIRED => [
                'sc' => 'Expired',
                'c'  => 'Kadaluarsa'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of string status
     * v: Vendor
     * c: customer
     * sv: sourcing vendor
     * sc: sourcing customer
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getPOStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_PO_NEW => [
                'v'  => 'Baru',
                'sv' => 'Waiting',
                'sc' => 'New',
                'c'  => 'Baru'
            ],     // New, Waiting
            self::$_STATUS_PO_NEED_CONFIRM => [
                'v'  => 'Baru',
                'sv' => 'Waiting',
                // 'v'  => 'Menunggu Konfirmasi Sharedvis',
                // 'sv' => 'Need Sharedvis Confirmation',
            ],    // Need Confirm, Waiting Confirmation, On Progress
            self::$_STATUS_PO_ON_PROGRESS => [
                'c'  => 'Dalam Proses',
                'sc' => 'On Progress',
            ],    // Dalam Proses, On Progress
            self::$_STATUS_PO_CONFIRMED => [
                'v'  => 'Disetujui',
                'sv' => 'Order Accepted',
                'sc' => 'Order Accepted',
                'c'  => 'Dalam Proses'
            ],    // Order Accepted, Confirmed
            self::$_STATUS_PO_READY_TO_SHIP => [
                'v'  => 'Siap Untuk Dikirim',
                'sv' => 'Ready To Be Shipped',
            ],    // Ready To Be Shipped
            self::$_STATUS_PO_REJECTED => [
                'v'  => 'Ditolak',
                'sv' => 'Rejected',
                'sc' => 'Rejected',
                'c'  => 'Ditolak'
            ],    // Rejected
            self::$_STATUS_PO_CANCELED => [
                'v'  => 'Dibatalkan',
                'sv' => 'Cancelled',
            ],    // Canceled
            self::$_STATUS_PO_PARTIALLY_SHIPPED => [
                'v'  => 'Dalam Pengiriman',
                'sv' => 'On Shipping',
                'sc' => 'On Shipping',
                'c'  => 'Dalam Pengiriman'
            ],   // Partially Shipped
            self::$_STATUS_PO_FULLY_SHIPPED => [
                'v'  => 'Dalam Pengiriman',
                'sv' => 'On Shipping',
                'sc' => 'On Shipping',
                'c'  => 'Dalam Pengiriman'
            ],   // Fully Shipped
            self::$_STATUS_PO_DELIVERED => [
                'v'  => 'Terkirim',
                'sv' => 'Received',
                'sc' => 'Delivered',
                'c'  => 'Barang Diterima'
            ],   // Delivered, Barang Diterima
            self::$_STATUS_PO_CLOSED => [
                'v'  => 'Selesai',
                'sv' => 'Closed',
                'sc' => 'Closed',
                'c'  => 'Selesai'
            ],     // Closed, Selesai
            self::$_STATUS_PO_ON_QUEUE => [
                'sc' => 'On Queue',
                'c'  => 'Sedang Diproses'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of Quotation status string
     * v: Vendor
     * c: customer
     * sv: sourcing vendor
     * sc: sourcing customer
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getQuotationStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_QUOTATION_NEW => [
                'sv' => 'Need Confirmation',
                'v'  => 'Baru'
            ],  // New, Waiting Response
            self::$_STATUS_QUOTATION_ACCEPTED => [
                'sv' => 'Accepted',
                'v'  => 'Disetujui'
            ],     // Accepted
            self::$_STATUS_QUOTATION_SUBMITTED => [
                'sc' => 'Submitted',
                'c'  => 'Diterima'
            ],    // Submitted
            self::$_STATUS_QUOTATION_REJECTED => [
                'sv' => 'Rejected',
                'v'  => 'Ditolak',
                'sc' => 'PR Rejected',
                'c'  => 'PR Ditolak'
            ],     // Rejected
            self::$_STATUS_QUOTATION_REJECTED_BY_MBIZ => [
                'sv' => 'Rejected By Sharedvis',
                'v'  => 'Ditolak'
            ],  // Rejected By Sharedvis
            self::$_STATUS_QUOTATION_REJECTED_BY_CUSTOMER => [
                'sv' => 'Rejected By Customer',
                'v'  => 'Dibatalkan',
                'sc' => 'Rejected By Customer',
                'c'  => 'Ditolak'
            ],  // Rejected By Customer
            self::$_STATUS_QUOTATION_CANCELLED => [
                'sv' => 'Rejected',
                'v'  => 'Dibatalkan'
            ],  // Cancelled (Rejected)
            self::$_STATUS_QUOTATION_CANCELLED_BY_MBIZ => [
                'sv' => 'Rejected By Sharedvis',
                'v'  => 'Dibatalkan'
            ],  // Cancelled( Rejected) by Mbiz
            self::$_STATUS_QUOTATION_EXPIRED_BY_MBIZ => [
                'sv' => 'Expired By Sharedvis',
                'v'  => 'Expired'
            ],  // Expired, Expired By Sharedvis
            self::$_STATUS_QUOTATION_EXPIRED_BY_CUSTOMER => [
                'sv' => 'Expired By Customer',
                'v'  => 'Expired',
                'sc' => 'Expired By Customer',
                'c'  => 'Expired'
            ],  // Expired, Expired By Customer
            self::$_STATUS_QUOTATION_ORDERED => [
                'sv' => 'PO Created',
                'v'  => 'Dipesan',
                'sc' => 'PO Created',
                'c'  => 'PO Terbuat'
            ],  // PO Created, Ordered
            self::$_STATUS_QUOTATION_PR_CREATED => [
                'sc' => 'PR Created',
                'c'  => 'PR Terbuat'
            ],  // PR Created
            self::$_STATUS_QUOTATION_ON_QUEUE => [
                'sc' => 'On Queue',
                'c'  => 'Sedang Diproses'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of RFQ Status String
     * v: Vendor
     * c: customer
     * sv: sourcing vendor
     * sc: sourcing customer
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getRfqStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_RFQ_PRE_NEW => [
                'sc' => 'New',
                'c'  => 'Menunggu',
            ],    // New, Menunggu
            self::$_STATUS_RFQ_NEW => [
                'sc' => 'Waiting Response',
                'sv' => 'Waiting Response',
                'c'  => 'Menunggu',
                'v'  => 'Baru'
            ],    // New, Waiting Response
            self::$_STATUS_RFQ_ON_PROGRESS => [
                'sc' => 'On Progress',
                'c'  => 'Dalam Proses',
            ],    // On Progress, Dalam Proses
            self::$_STATUS_RFQ_SUBMITTED => [
                'sc' => 'Quotation Submitted',
                'c'  => 'Quotation Diterima',
            ],  // Quotation Submitted
            self::$_STATUS_RFQ_PARTIALLY_ACCEPTED => [
                'sv' => 'Partially Accepted',
                'v'  => 'Terkonfirmasi'
            ], // Partially Accepted
            self::$_STATUS_RFQ_ACCEPTED => [
                'sv' => 'Accepted',
                'v'  => 'Terkonfirmasi'
            ],   // Accepted
            self::$_STATUS_RFQ_REJECTED_BY_MBIZ => [
                'sv' => 'Rejected By Sharedvis',
                'sc' => 'Rejected',
                'v'  => 'Dibatalkan',
                'c'  => 'Ditolak'
            ],   // Rejected By Sharedvis
            self::$_STATUS_RFQ_REJECTED_BY_VENDOR => [
                'sv' => 'Rejected By Vendor',
                'v'  => 'Ditolak'
            ],     // Rejected By Vendor
            self::$_STATUS_RFQ_EXPIRED_BY_MBIZ => [
                'sv' => 'Expired By Sharedvis',
                'sc' => 'Expired',
                'v'  => 'Expired',
                'c'  => 'Ditolak'
            ],    // Expired By Sharedvis
            self::$_STATUS_RFQ_EXPIRED_BY_VENDOR => [
                'sv' => 'Expired',
                'v'  => 'Expired'
            ], // Expired By Vendor
            self::$_STATUS_RFQ_CANCELED => [
                'sv' => 'Cancelled',
                'v'  => 'Dibatalkan',
                'sc' => 'Cancelled',
                'c'  => 'Dibatalkan'
            ],  // Canceled, DIbatalkan
            self::$_STATUS_RFQ_ON_QUEUE => [
                'sc' => 'On Queue',
                'c'  => 'Sedang Diproses',
                'sv' => 'On Queue',
                'v'  => 'Sedang Diproses',
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get array of string status
     * v: Vendor
     * c: customer
     * sv: sourcing vendor
     * sc: sourcing customer
     *
     * @param $status
     * @return array|mixed
     */
    protected static function getShippingStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_SHIPPING_PARTIALLY_SHIPPED => [
                'v'  => 'Dalam Pengiriman',
                'sv' => 'Shipped',
                'sc' => 'Shipped',
                'c'  => 'Dalam Pengiriman'
            ],   // Partially Shipped
            self::$_STATUS_SHIPPING_FULLY_SHIPPED => [
                'v'  => 'Dalam Pengiriman',
                'sv' => 'Shipped',
                'sc' => 'Shipped',
                'c'  => 'Dalam Pengiriman'
            ],   // Fully Shipped
            self::$_STATUS_SHIPPING_DELIVERED => [
                'v'  => 'Diterima',
                'sv' => 'Received',
                'sc' => 'Received',
                'c'  => 'Diterima'
            ]
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }
    /**
     * Get Warranty Types
     */
    public static function getWarrantyTypes()
    {
        return [
            self::$_WARRANTY_NO_WARRANTY    => "Tanpa Garansi",
            self::$_WARRANTY_DISTRIBUTOR    => "Garansi Distributor",
            self::$_WARRANTY_OFFICIAL       => "Garansi Resmi",
            self::$_WARRANTY_INTERNATIONAL  => "Garansi Internasional"
        ];
    }

    /**
     * Get Adjustment Accounts
     */
    public static function getAdjustmentAccounts($account_id = null)
    {
        $accounts = [
            self::$_ADJUSTMENT_SHIPPING_FINE => "Shipping Fine",
            self::$_ADJUSTMENT_INFAQ => "Infaq",
            self::$_ADJUSTMENT_BANK_EXPENSE => "Bank Expense",
            self::$_ADJUSTMENT_BOS_FUND => "BOS Fund",
            self::$_ADJUSTMENT_NOT_PPH_22_OBJECT => "Not PPH 22 Object",
            self::$_ADJUSTMENT_TAX_MISCALCULATION => "Tax Miscalculation",
            self::$_ADJUSTMENT_OVERPAYMENT => "Overpayment",
            self::$_ADJUSTMENT_ENTERTAINT_EXPENSE => "Entertaint Expense",
            self::$_ADJUSTMENT_OTHERS => "Others",
        ];

        if (!empty($account_id))
        {
            if (isset($accounts[$account_id]))
                return $accounts[$account_id];
            else
                return null;
        }

        return $accounts;
    }

    /**
     * Get MSDS Types
     */
    public static function getMSDSTypes()
    {
        return [
            self::$_MSDS_TYPE_SOLID     => "Solid",
            self::$_MSDS_TYPE_EXPLOSIVE => "Explosive",
            self::$_MSDS_TYPE_GAS       => "Gas",
            self::$_MSDS_TYPE_LIQUID    => "Liquid",
            self::$_MSDS_TYPE_FRESH     => "Fresh",
            self::$_MSDS_TYPE_METAL     => "Metal"
        ];
    }

    /**
     * Get Product Type
     */
    public static function getProductType()
    {
        return [
            self::$_PRODUCT_TYPE_GOOD => "Good",
            self::$_PRODUCT_TYPE_SERVICE => "Service"
        ];
    }

    /**
     * Get String SKU Origin
     */
    public static function getSkuOrigin()
    {
        return [
            self::$_SKU_ORIGIN_IMPORT => "Import",
            self::$_SKU_ORIGIN_LOCAL => "Local"
        ];
    }

    /**
     * Get String SKU Catalog Type
     */
    public static function getSkuCatalog()
    {
        return [
            self::$_SKU_CATALOG_ICECAT => "Icecat",
            self::$_SKU_CATALOG_INHOUSE => "Inhouse"
        ];
    }

    /**
     * Get Sharedvis Billing Address Information
     */
    public static function getMBizBillingAddress(){
        return [
            'billing_country'           => "Indonesia",
            'billing_province'          => "DKI Jakarta",
            'billing_city'              => "Kota Adm. Jakarta Selatan",
            'billing_district'          => "Karet Kuningan",
            'billing_zipcode'           => "12920",
            'billing_address'           => "Jalan HR Rasuna Said Kav. B 12",
            'billing_phone'             => "021 29110888",
            'billing_phone_prefix'      => "+62",
        ];
    }

    /**
     * @description This function for generating formatted id in quotation etc
     * @param $companyID    integer (company id)
     * @param $userType     integer (buyer or seller)
     * @param $rev_no       string (rfq_no, quotation_no, etc - not a number!!)
     * @param $opt          string (adding string to back of generated number)
     * @return string   formatted number
     */
    public static function generateNumber($companyID = 0, $userType = 0, $rev_no = 0, $opt = "", $count = 0){
        $arrClass   = explode("\\", strtolower(static::class));
        $type       = array_pop($arrClass);
        $date       = date("ymd");
        $revision_number = explode("-REV", $rev_no);

        if($type == "sku"){
            $revision_number = $rev_no;
        }
        else{
            $revision_number = $revision_number[0];
        }

        $added_name_front = $added_name_back = $userType_change = "";

        if($userType == self::$_TYPE_VENDOR){
            $userType_change = "V";
        }
        elseif ($userType == self::$_TYPE_COSTUMER){
            $userType_change = "C";
        }

        $revision = false;

        switch ($type)
        {
            case "rfq":
            case "rfqrevisions":
                if(!$count){
                    $count = self::getCount($userType, $companyID, $type, false);
                }

                if($type == "rfqrevisions"){
                    $count = self::getCount($userType, $companyID, $type, true, $revision_number);

                    $revision        = true;
                    $added_name_back = "-REV".$count;
                }

                $type = "RFQ";
                break;

            case "quotation":
            case "quotations":
            case "quotationrevisions":
                if(!$count){
                    $count = self::getCount($userType, $companyID, $type, false);
                }

                if($type == "quotationrevisions"){
                    $count = self::getCount($userType, $companyID, $type, true, $revision_number);

                    $revision        = true;
                    $added_name_back = "-REV".$count;
                }
                $type = "QN";
                break;

            case "purchaserequests":
            case "purchaserequestrevisions":
                if(!$count){
                    $count = self::getCount($userType, $companyID, $type, false);
                }

                if($type == "purchaserequestrevisions"){
                    $count = self::getCount($userType, $companyID, $type, true, $revision_number);

                    $revision        = true;
                    $added_name_back = "-REV".$count;
                }

                $type = "PR";
                break;

            case "purchaseorders":
            case "purchaseorderrevisions":
            case "purchaseordersrevisions":
                if(!$count){
                    $count = self::getCount($userType, $companyID, $type, false);
                }

                if($type == "purchaseorderrevisions"){
                    $count = self::getCount($userType, $companyID, $type, true, $revision_number);

                    $revision        = true;
                    $added_name_back = "-REV".$count;
                }

                $type = "PO";
                break;

            case "invoices":
            case "invoicerevisions":
                if(!$count){
                    $count = self::getCount($userType, $companyID, $type, false);
                }

                if($type == "invoicerevisions"){
                    $count = self::getCount($userType, $companyID, $type, true, $revision_number);

                    $revision        = true;
                    $added_name_back = "-REV".$count;
                }

                $type = "INV";
                break;

            case "payment":
            case "payments":
                if(!$count){
                    $count = self::getCount($userType, $companyID, $type, false);
                }

                $type  = "IDP";
                break;

            case "budget":
                $budget_count = self::count([
                    'conditions' => "company_buyer_id = :company_buyer_id:",
                    'bind'       => ['company_buyer_id' => $companyID]
                ]);

                $count = $budget_count + 1;
                $type  = "BG";
                break;

            case "sku":
                $sku = self::count([
                    'conditions' => "product_id = {$revision_number}"
                ]);
                $totalSku = $sku + 1;
                $number = $revision_number.$totalSku;

                $name = substr("SKU000000000000", 0, (strlen($number) * -1)).$number;
                break;

            case "reference":
                $number = mt_rand(10000,99999);
                $name = substr("AGT-P".$companyID."-00000", 0, (strlen($number) * -1)).$number;
                break;

            case "request":
                $number = mt_rand(10000,99999);
                $name = substr("R".$companyID."-".$date."-00000", 0, (strlen($number) * -1)).$number;
                break;

            case "shipping":
                $number = mt_rand(10000,99999);
                $name = substr("S00000", 0, (strlen($number) * -1)).$number;
                break;

            case "shippinggroups":
                $po_number = $opt;
                $shipping_group_conditions = "delivery_number LIKE :dn_like:";
                $shipping_group_bind       = ['dn_like' => 'DN-' . $po_number . "-%"];

                $shipping_group = self::count([
                    "conditions" => $shipping_group_conditions,
                    "bind"       => $shipping_group_bind
                ]);

                $dn_count = $shipping_group + 1;
                $name     = 'DN-' . $po_number . '-' . $dn_count;
                break;

            default:
                $name = $type . " not defined";
        }

        if(empty($name) && !$revision){
            $name = $added_name_front . $type."-".$userType_change.$companyID."-".$date."-" . $count . $added_name_back;
        }

        if($revision){
            $name = $revision_number . $added_name_back;
        }

        return $name;
    }

    /**
     * @description This function for count needed to add for generate formatted number
     * @param $userType             integer ( vendor or customer, see constrait )
     * @param $companyID            integer ( company id )
     * @param $data_type            string ( rfq, quotation, etc)
     * @param bool $is_revision     boolean ( true or false )
     * @param $revision_number      string (rfq_no, quotation_no, etc - not a number!!)
     * @return integer              total type (rfq, quotation, etc) in it's company
     */
    public static function getCount($userType, $companyID, $data_type, $is_revision = false, $revision_number = ""){
        // time for the day
        $firstDay   = gmdate("Y-m-d 00:00:00", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $lastDay    = gmdate("Y-m-d 23:59:59", mktime(23, 59, 59, date('m'), date('d'), date('Y')));

        // time for the year
        $firstYear   = gmdate("Y-01-01 00:00:00", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $lastYear    = gmdate("Y-12-t 23:59:59", mktime(23, 59, 59, date('m'), date('d'), date('Y')));

        if($userType == self::$_TYPE_VENDOR){
            $conditions = "company_seller_id = :company_id: AND created_at BETWEEN :first: AND :last:";
        }
        elseif ($userType == self::$_TYPE_COSTUMER){
            $conditions = "company_buyer_id = :company_id: AND company_seller_id = 0 AND created_at BETWEEN :first: AND :last:";
        }

        if(!$is_revision){
            switch ($data_type){
                case "rfq":
                case "quotation":
                case "quotations":
                case "purchaserequests":
                case "purchaseorders":
                case "invoices":
                    $bind = [
                        'company_id'  => $companyID,
                        'first'    => $firstDay,
                        'last'     => $lastDay
                    ];

                    break;
                case "payment":
                case "payments":
                    $bind = [
                        'company_id' => $companyID,
                        'first'    => $firstYear,
                        'last'     => $lastYear
                    ];
                    break;
                default:
                    return 0;
            }

            $count_model = self::count([
                'conditions' => $conditions,
                'bind' => $bind
            ]);

            $count = $count_model + 1;
        }
        else{
            switch ($data_type){
                case "rfqrevisions":
                    $field = 'rfq_no';
                    break;
                case "quotationrevisions":
                    $field = 'quotation_no';
                    break;
                case "purchaserequestrevisions":
                    $field = 'purchase_request_no';
                    break;
                case "purchaseorderrevisions":
                case "purchaseordersrevisions":
                    $field = 'purchase_order_no';
                    break;
                case "invoicerevisions":
                    $field = 'invoice_no';
                    break;
                default:
                    return 0;
            }

            $count_revision = self::count([
                "conditions" => "{$field} LIKE '%:revision_no:-REV%'",
                "bind" => [
                    "revision_no" => $revision_number
                ]
            ]);

            $count           = $count_revision + 1;
        }

        return $count;
    }

    /**
     * Get status file management
     */
    public static function getManagementFileStatus(){
        return [
            self::$_MANAGEMENT_FILE_READY => "Siap Dieksekusi",
            self::$_MANAGEMENT_FILE_ON_PROGRESS => "Proses",
            self::$_MANAGEMENT_FILE_FINISH => "Selesai",
            self::$_MANAGEMENT_FILE_FAILED => "Gagal",
            self::$_MANAGEMENT_FILE_WAITING => "Menunggu"
        ];
    }

    /**
     * Get landing page type
     */
    public static function getLandingPageType(){
        return[
            self::$_LANDING_PAGE_STATIC => "pages",
            self::$_LANDING_PAGE_PROMO => "promo",
            self::$_LANDING_PAGE_SELLER => "seller",
        ];
    }

    /**
     * Get Client Demand Statuses
     */
    protected static function getCDNStatuses($status = 0) {
        $statuses = [
            self::$_STATUS_CDN_NEED_RESPONSE_CPD    => "Need Response CPD",
            self::$_STATUS_CDN_ON_PROGRESS_CPD      => "On Progress CPD",
            self::$_STATUS_CDN_ACCEPTED             => "CDN Accepted",
            self::$_STATUS_CDN_NEED_FEEDBACK_CA     => "Need Feedback CA",
            self::$_STATUS_CDN_CANCELLED            => "CDN Cancelled",
            self::$_STATUS_CDN_DONE                 => "CDN Done",
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get Product Demand Statuses
     */
    protected static function getProductDemandStatuses($status = 0) {
        $statuses = [
            self::$_STATUS_CDN_PRODUCT_NEED_RESPONSE_CONTENT    => "Need Response Content",
            self::$_STATUS_CDN_PRODUCT_ON_PROGRESS_CONTENT      => "On Progress Content",
            self::$_STATUS_CDN_PRODUCT_SKU_READY                => "SKU Ready",
            self::$_STATUS_CDN_PRODUCT_SKU_LIVE                 => "SKU Live",
            self::$_STATUS_CDN_PRODUCT_NEED_FEEDBACK_CPD        => "Need Feedback CPD",
            self::$_STATUS_CDN_PRODUCT_NEED_FEEDBACK_CONTENT    => "Need Feedback Content",
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get Product Demand SKU Statuses
     */
    public static function getProductDemandSkuStatuses($status = 0)
    {
        $statuses = [
            self::$_STATUS_CDN_PRODUCT_SKU_SKU_LIVE         => "Live",
            self::$_STATUS_CDN_PRODUCT_SKU_SKU_NOT_LIVE     => "Not Live",
            self::$_STATUS_CDN_PRODUCT_SKU_SKU_PENDING      => "Pending",
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * Get Client Demand Categories
     */
    public static function getCDNCategories() {
        return [
            self::$_CDN_CATEGORY_GG             => "GG",
            self::$_CDN_CATEGORY_OTOMOTIVE      => "Otomotif",
            self::$_CDN_CATEGORY_MRO            => "MRO",
            self::$_CDN_CATEGORY_SERVICES       => "Services",
            self::$_CDN_CATEGORY_ELECTRONIC     => "Elektronik",
            self::$_CDN_CATEGORY_LIFESTYLE      => "Lifestyle",
            self::$_CDN_CATEGORY_IT             => "IT",
        ];
    }

    /**
     * Get Client Demand Log Types
     */
    public static function getCDNLogTypes() {
        return [
            self::$_CDN_LOG_EDIT             => "Edit",
            self::$_CDN_LOG_FEEDBACK_CA      => "Feedback to CA",
            self::$_CDN_LOG_FEEDBACK_CPD     => "Feedback to CPD",
            self::$_CDN_LOG_FEEDBACK_CONTENT => "Feedback to Content",
            self::$_CDN_LOG_CANCELLED        => "Cancel",
        ];
    }

    /**
     * Get Client Demand Feedback/Cancellation Reason
     */
    public static function getCDNReasons($log_type, $reason_id = 0) {
        $ca_feedback_strings = [
            self::$_CDN_FEEDBACK_CA_PRODUCT_DETAIL_NOT_COMPLETE_REASON => "Product detail is not complete",
            self::$_CDN_FEEDBACK_CA_PRODUCT_DETAIL_NOT_MATCH_REASON => "Product detail does not match",
            self::$_CDN_FEEDBACK_CA_IMG_NOT_MEET_STANDARD_REASON => "Image does not meet standards",
            self::$_CDN_FEEDBACK_CA_OTHER => "Other"
        ];

        $cpd_feedback_strings = [
            self::$_CDN_FEEDBACK_CPD_PRODUCT_DETAIL_NOT_COMPLETE_REASON => "Product detail is not complete",
            self::$_CDN_FEEDBACK_CPD_PRODUCT_DETAIL_NOT_MATCH_REASON => "Product detail does not match",
            self::$_CDN_FEEDBACK_CPD_IMG_NOT_MEET_STANDARD_REASON => "Warranty information is not complete",
            self::$_CDN_FEEDBACK_CPD_INSUFFICIENT_WARRANTY_INFO_REASON => "Image does not meet standards",
            self::$_CDN_FEEDBACK_CPD_OTHER => "Other"
        ];

        $content_feedback_strings = [
            self::$_CDN_FEEDBACK_CONTENT_SKU_ID_AND_PRODUCT_NAME_NOTMATCH_REASON => "SKU ID and product name do not match",
            self::$_CDN_FEEDBACK_CONTENT_WRONG_SKU_ID_REASON => "Wrong SKU ID",
            self::$_CDN_FEEDBACK_CONTENT_OTHER => "Other"
        ];

        $cancellation_strings = [
            self::$_CDN_CANCEL_INSUFFICIENT_BUDGET_REASON => "Insufficient client budget",
            self::$_CDN_CANCEL_PRODUCT_COMPLETION_TOO_LONG_REASON => "Product completion time is too long",
            self::$_CDN_CANCEL_DEMAND_COULD_NOT_BE_COMPLETED_REASON => "Demand could not be completed",
            self::$_CDN_CANCEL_OTHER => "Other"
        ];

        $reasons = [
            self::$_CDN_LOG_FEEDBACK_CA      => $ca_feedback_strings,
            self::$_CDN_LOG_FEEDBACK_CPD     => $cpd_feedback_strings,
            self::$_CDN_LOG_FEEDBACK_CONTENT => $content_feedback_strings,
            self::$_CDN_LOG_CANCELLED        => $cancellation_strings
        ];

        return $reason_id > 0 ? $reasons[$log_type][$reason_id] : $reasons[$log_type];
    }


    /**
     * To Get MOU Buyer Status String
     * @param  integer $status
     * @return array   $statuses
     */
    public static function getMouBuyerStatuses($status = 0) {
        $statuses = [
            self::$_STATUS_MOU_BUYER_DRAFT => [
                "sc" => "Draft"
            ],
            self::$_STATUS_MOU_BUYER_WAITING => [
                "sc" => "Waiting",
                "c" => "Butuh Persetujuan"
            ],
            self::$_STATUS_MOU_BUYER_ACTIVE => [
                "sc" => "Active",
                "c" => "Aktif"
            ],
            self::$_STATUS_MOU_BUYER_INACTIVE => [
                "sc" => "Inactive",
                "c" => "Tidak Aktif"
            ],
            self::$_STATUS_MOU_BUYER_CLOSED => [
                "sc" => "Closed",
                "c" => "Selesai"
            ],
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses;
    }

    /**
     * To get MOU Buyer Transaction Status String
     * @param  integer $status
     * @return array   $statuses
     */
    public static function getMouBuyerTransactionStatuses($status = 0) {
        $statuses = [
            self::$_STATUS_MOU_BUYER_TRANSACTION_IN_PROGRESS => "Dalam Proses",
            self::$_STATUS_MOU_BUYER_TRANSACTION_SETTLED => "Selesai",
            self::$_STATUS_MOU_BUYER_TRANSACTION_PR_CREATED => "Dalam Proses",
        ];

        return isset($statuses[$status]) ? $statuses[$status] : $statuses; 
    }
}
