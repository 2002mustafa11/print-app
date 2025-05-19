<?php

namespace App\Http\Controllers;


use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }
    public function paymentProcess(Request $request)
{
    $amountEgp = $request->input('amount_egp');
    $amountCents = intval($amountEgp * 100);

    $request->merge([
        'amount_cents' => $amountCents,
        'currency' => 'EGP',
        'delivery_needed' => false,
        'items' => [
            [
                'name' => 'PDF Print',
                'amount_cents' => $amountCents,
                'description' => 'Print PDF file',
                'quantity' => 1,
            ]
        ],
        'shipping_data' => [
            'first_name' => 'Ali',
            'last_name' => 'Hassan',
            'email' => auth()->user()->email,
            'phone_number' => '01000000000',
            'street' => 'Default Street',
            'building' => '1',
            'floor' => '1',
            'apartment' => '1',
            'city' => 'Cairo',
            'state' => 'Cairo',
            'country' => 'EG',
        ],
    ]);
// dd($request);
    // 3. استدعاء الخدمة
    $response = $this->paymentGateway->sendPayment($request);

    // 4. توجيه المستخدم أو إظهار الخطأ
    if ($response['success']) {
        return redirect()->away($response['url']); // إلى بوابة الدفع
    }

    return redirect()->back()->with('error', 'فشل إرسال عملية الدفع');
}

    // public function paymentProcess(Request $request)
    // {
    //     // return $this->paymentGateway->sendPayment($request);
    //     $response = $this->paymentGateway->sendPayment($request);
    //     if ($response['success']) {
    //         return redirect()->away($response['url']); // إرسال المستخدم لبوابة الدفع
    //     }
    
    //     return redirect()->back()->with('error', 'فشل إرسال عملية الدفع');
    // }

    public function callBack(Request $request)
    {
        $response = $this->paymentGateway->callBack($request);

        if ($response) {
            return redirect()->route('upload')->with('success', 'تمت عملية الدفع بنجاح');
        }
    
        return redirect()->route('upload')->with('error', 'فشل الدفع أو تم رفضه');
    }
    
}
