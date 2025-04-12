<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Services\PaymobPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentController extends Controller
{
    //touch app/Services/PaymobPaymentService.php

    protected $paymentGateway;

    public function __construct()
    {
        $this->paymentGateway = new PaymobPaymentService();
    }

    public function initiatePayment(Request $request)
    {
        $paymentResponse = $this->paymentGateway->sendPayment($request);

        if ($paymentResponse['success']) {
            $order = Order::find($request->input('merchant_order_id'));
            if (!$order) {
                Log::error('Order not found for payment initiation', ['order_id' => $request->input('merchant_order_id')]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            $order->paymob_product_url = $paymentResponse['product_url'];
            $order->save();

            Log::info('Paymob product URL saved to order', [
                'order_id' => $order->id,
                'paymob_product_url' => $order->paymob_product_url
            ]);

            return redirect($paymentResponse['url']);
        }

        Log::error('Payment initiation failed', $paymentResponse);
        return redirect()->route('payment.failed')->with('error', $paymentResponse['message'] ?? 'Payment initiation failed');
    }

    public function paymentProcess(Request $request)
    {
        return $this->paymentGateway->sendPayment($request);
    }

    public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    {
        $response = $this->paymentGateway->callBack($request);
        if ($response) {
            Log::info('Payment successful', ['order_id' => $request->input('merchant_order_id')]);
            return redirect()->route('payment.success');
        }
        Log::error('Payment failed', $request->all());
        return redirect()->route('payment.failed');
    }

    public function success()
    {
        return response()->json(['status' => 'success', 'message' => 'Payment successful']);
    }

 
public function failed()
{
    return response()->json(['status' => 'failed', 'message' => 'Payment failed']);
}
}