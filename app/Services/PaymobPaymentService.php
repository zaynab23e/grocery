<?php

namespace App\Services;

// use App\Order;
use  App\Models\Order ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymobPaymentService extends BasePaymentService
{
    protected $api_key;
    protected $integrations_id;

    public function __construct()
    {
        $this->base_url = env("PAYMOB_BASE_URL");
        $this->api_key = env("PAYMOB_API_KEY");
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->integrations_id = [4898304, 4898296];
    }

    protected function generateToken()
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', ['api_key' => $this->api_key]);
        $responseData = $response->getData(true);

        if (!isset($responseData['data']['token'])) {
            throw new \Exception('Failed to generate authentication token');
        }

        return $responseData['data']['token'];
    }
    public function sendPayment(Request $request)
    {
        $token = $this->generateToken();
        $data = $request->all();
        $mode = $data['mode'] ?? 'invoice';
    
        Log::info('Paymob Mode Selected', ['mode' => $mode, 'request_data' => $data]);
    
        if ($mode === 'product') {
            // إعداد بيانات الدفع لمنتج
            $endpoint = '/api/ecommerce/products';
            $productData = [
                'auth_token' => $token,
                'product_name' => $data['product_name'] ?? 'Default Product',
                'invoice_id' => $data['invoice_id'] ?? '0',
                'amount_cents' => $data['amount_cents'] ?? '0',
                'currency' => $data['currency'] ?? 'EGP',
                'inventory' => $data['inventory'] ?? '1',
                'delivery_needed' => $data['delivery_needed'] ?? 'false',
                'integrations' => $this->integrations_id,
                'allow_quantity_edit' => $data['allow_quantity_edit'] ?? 'false',
                'product_description' => $data['product_description'] ?? 'No description',
                'merchant_product_id' => $data['merchant_product_id'] ?? null,
                'merchant_order' => $data['merchant_product_id'] ?? null,
            ];
    
            // تعيين الهيدر الخاص بالطلب
            $this->header = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];
    
            $response = $this->buildRequest('POST', $endpoint, $productData);
        }
        elseif ($mode === 'invoice') {
            // إعداد بيانات الدفع للفواتير (تأكد من صحة عنوان الendpoint والمعطيات المطلوبة)
            $endpoint = '/api/ecommerce/invoices';
            $invoiceData = [
                'auth_token'      => $token,
                'invoice_details' => $data['invoice_details'] ?? [],
                // يمكن إضافة معطيات إضافية حسب متطلبات واجهة الدفع
            ];
    
            $response = $this->buildRequest('POST', $endpoint, $invoiceData);
        }
        else {
            // في حال كان وضع الدفع غير معتمد نقوم بإرجاع رسالة خطأ مباشرة
            return ['success' => false, 'message' => 'وضع الدفع غير معتمد'];
        }
    
        $responseData = $response->getData(true);
        Log::info("Paymob Response for mode {$mode}", (array) $responseData);
    
        if ($responseData['success']) {
            // اعتمادًا على الوضع، اختيار المفتاح المناسب للحصول على رابط الدفع
            $urlKey = ($mode === 'product') ? 'product_url' : 'url';
            if (isset($responseData['data'][$urlKey])) {
                return [
                    'success'     => true,
                    'url'         => $responseData['data'][$urlKey],
                    'product_url' => $responseData['data'][$urlKey],
                ];
            } else {
                return ['success' => false, 'message' => 'لم يتم العثور على رابط الدفع في الاستجابة'];
            }
        }
    
        return [
            'success' => false,
            'url'     => route('payment.failed'),
            'message' => $responseData['data'] ?? 'خطأ غير معروف'
        ];
    }
    
    // public function sendPayment(Request $request)
    // {
    //     $token = $this->generateToken();
    //     $data = $request->all();
    //     $mode = $data['mode'] ?? 'invoice';

    //     Log::info('Paymob Mode Selected', ['mode' => $mode, 'request_data' => $data]);

    //     if ($mode === 'product') {
    //         $endpoint = '/api/ecommerce/products';
    //         $productData = [
    //             'auth_token' => $token,
    //             'product_name' => $data['product_name'] ?? 'Default Product',
    //             'invoice_id' => $data['invoice_id'] ?? '0',
    //             'amount_cents' => $data['amount_cents'] ?? '0',
    //             'currency' => $data['currency'] ?? 'EGP',
    //             'inventory' => $data['inventory'] ?? '1',
    //             'delivery_needed' => $data['delivery_needed'] ?? 'false',
    //             'integrations' => $this->integrations_id,
    //             'allow_quantity_edit' => $data['allow_quantity_edit'] ?? 'false',
    //             'product_description' => $data['product_description'] ?? 'No description',
    //             'merchant_product_id' => $data['merchant_product_id'] ?? null,
    //             'merchant_order' => $data['merchant_product_id'] ?? null,
    //         ];

    //         $this->header = [
    //             'Accept' => 'application/json',
    //             'Content-Type' => 'application/json',
    //         ];

    //         $response = $this->buildRequest('POST', $endpoint, $productData);
    //     }

    //     $responseData = $response->getData(true);
    //     Log::info("Paymob Response for mode {$mode}", (array) $responseData);

    //     if ($responseData['success']) {
    //         $urlKey = $mode === 'product' ? 'product_url' : 'url';
    //         if (isset($responseData['data'][$urlKey])) {
    //             return [
    //                 'success' => true,
    //                 'url' => $responseData['data'][$urlKey],
    //                 'product_url' => $responseData['data'][$urlKey],
    //             ];
    //         } else {
    //             return ['success' => false, 'message' => 'Payment URL not found in response'];
    //         }
    //     }

    //     return ['success' => false, 'url' => route('payment.failed'), 'message' => $responseData['data'] ?? 'Unknown error'];
    // }

    public function getTransactionDetails($transactionId)
    {
        $token = $this->generateToken();
        $endpoint = '/api/acceptance/transactions/' . $transactionId;
        $response = $this->buildRequest('GET', $endpoint, null, 'json', ['Authorization' => 'Bearer ' . $token]);
        $responseData = $response->getData(true);

        if ($responseData['success'] && isset($responseData['data'])) {
            return $responseData['data'];
        }
        throw new \Exception('Failed to get transaction details: ' . json_encode($responseData));
    }

    public function getOrderDetails($orderId)
    {
        $token = $this->generateToken();
        $endpoint = '/api/ecommerce/orders/' . $orderId;
        $response = $this->buildRequest('GET', $endpoint, null, 'json', ['Authorization' => 'Bearer ' . $token]);
        $responseData = $response->getData(true);

        if ($responseData['success'] && isset($responseData['data'])) {
            return $responseData['data'];
        }
        throw new \Exception('Failed to get order details: ' . json_encode($responseData));
    }

    public function callBack(Request $request): bool
    {
        $response = $request->all();
        Log::info('Paymob Callback', $response);

        if (isset($response['success']) && ($response['success'] === 'true' || $response['success'] === true)) {
            $transactionId = $response['id'];
            try {
                $transactionDetails = $this->getTransactionDetails($transactionId);
                Log::info('Transaction Details', $transactionDetails);
                $paymobOrderId = $transactionDetails['order']['id'];

                $orderDetails = $this->getOrderDetails($paymobOrderId);
                Log::info('Order Details', $orderDetails);
                $orderUrl = $orderDetails['order_url'];

                $order = Order::where('paymob_product_url', $orderUrl)
                        ->where('pay_status', 1)
                        ->first();

                if ($order) {
                    $order->update([
                        'pay_status' => 2,
                        'pay_type' => 2,
                        'status' => 2
                    ]);
                    Log::info('Order updated successfully', ['order_id' => $order->id]);
                    return true;
                } else {
                    Log::error('No pending order found with paymob_product_url', ['paymob_product_url' => $orderUrl]);
                    return false;
                }
            } catch (\Exception $e) {
                Log::error('Failed to process callback', ['error' => $e->getMessage()]);
                return false;
            }
        } else {
            Log::info('Payment not successful', $response);
            return false;
        }
    }
}