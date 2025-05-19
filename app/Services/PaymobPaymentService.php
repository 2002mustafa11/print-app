<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymobPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    /**
     * Create a new class instance.
     */
    protected $api_key;
    protected $integrations_id;

    public function __construct()
    {
        $this->base_url = env("BAYMOB_BASE_URL");
        $this->api_key = env("BAYMOB_API_KEY");
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->integrations_id = [5087762,5087703];
    }

//first generate token to access api
    protected function generateToken()
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', ['api_key' => $this->api_key]);
        return $response->getData(true)['data']['token'];
    }

    public function sendPayment(Request $request):array
    {
        $this->header['Authorization'] = 'Bearer ' . $this->generateToken();
        // dd($request);
        //validate data before sending it
        $data = $request->all();
        $data['api_source'] = "INVOICE";
        $data['integrations'] = $this->integrations_id;
        $merchant_order_id = uniqid('order_');
        auth()->user()->update([
            'last_merchant_order_id' => $merchant_order_id
        ]);
        // dd(auth()->user()->id,$merchant_order_id);
        $data['merchant_order_id'] = $merchant_order_id;
        
        $response = $this->buildRequest('POST', '/api/ecommerce/orders', $data);
        // dd($response);
        //handel payment response data and return it
        // dd($response);
        if ($response->getData(true)['success']) {


            return ['success' => true, 'url' => $response->getData(true)['data']['url']];
        }

        return ['success' => false, 'url' => route('payment.failed')];
    }

    public function callBack(Request $request): bool
    {
        $response = $request->all();
        Storage::put('paymob_response.json', json_encode($request->all()));
        
        if (isset($response['success']) && $response['success'] === 'true') {
        $userId = $response['merchant_order_id'] ?? null;  
        // dd($response);
        if ($userId) {
            $user = \App\Models\User::where('last_merchant_order_id', $userId)->first();
        // dd($user);
        if ($user) {
        $amount = $response['amount_cents'] / 100; 

                $user->wallet_balance += $amount;
                $user->save();

                $user->walletTransactions()->create([
                    'type' => 'deposit',
                    'amount' => $amount,
                    'description' => 'إيداع عن طريق Paymob',
                ]);
            }
        }
            return true;
        }
        return false;

    }


}