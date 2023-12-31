<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Stkrequest;
use App\Models\C2brequest;

class PaymentController extends Controller
{
    public function token(){
        $consumerKey='50uI3Y8I9uWdXEUvAlV6cYEBgedRtRx5';
        $consumerSecret='P0sQRgefR7J4n5zK';
        $url='https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response=Http::withBasicAuth($consumerKey,$consumerSecret)->get($url);
        return $response['access_token'];
    }

    public function initiateStkPush(){
        $accessToken=$this->token();
        $url='https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $PassKey='bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $BusinessShortCode=174379;
        $Timestamp=Carbon::now()->format('YmdHis');
        $password=base64_encode($BusinessShortCode.$PassKey.$Timestamp);
        $TransactionType='CustomerPayBillOnline';
        $Amount=1;
        $PartyA=254715377782;
        $PartyB=174379;
        $PhoneNumber=254715377782;
        $CallbackUrl='https://3880-41-90-179-60.ngrok-free.app/payments/stkcallback';
        $AccountReference='Arge';
        $TransactionDesc='Payment for goods';

        try{
        $response=Http::withToken($accessToken)->post($url,[
            'BusinessShortCode'=>$BusinessShortCode,
            'Password'=>$password,
            'Timestamp'=>$Timestamp,
            'TransactionType'=>$TransactionType,
            'Amount'=>$Amount,
            'PartyA'=>$PartyA,
            'PartyB'=>$PartyB,
            'PhoneNumber'=>$PhoneNumber,
            'CallBackURL'=>$CallbackUrl,
            'AccountReference'=>$AccountReference,
            'TransactionDesc'=>$TransactionDesc
        ]);
        }catch(Throwable $e){
            return $e->getMessage();
        }
        //return $response;
        $res=json_decode($response);

        $ResponseCode=$res->ResponseCode;
        if($ResponseCode==0){
            $MerchantRequestID=$res->MerchantRequestID;
            $CheckoutRequestID=$res->CheckoutRequestID;
            $CustomerMessage=$res->CustomerMessage;

            //save to database
            $payment= new Stkrequest;
            $payment->phone=$PhoneNumber;
            $payment->amount=$Amount;
            $payment->reference=$AccountReference;
            $payment->description=$TransactionDesc;
            $payment->MerchantRequestID=$MerchantRequestID;
            $payment->CheckoutRequestID=$CheckoutRequestID;
            $payment->status='Requested';
            $payment->save();

            return $CustomerMessage;
        }

    }

    public function stkCallback(){
        $data=file_get_contents('php://input');
        Storage::disk('local')->put('stk.txt',$data);

        $response=json_decode($data);

        $ResultCode=$response->Body->stkCallback->ResultCode;

        if($ResultCode==0){
            $MerchantRequestID=$response->Body->stkCallback->MerchantRequestID;
            $CheckoutRequestID=$response->Body->stkCallback->CheckoutRequestID;
            $ResultDesc=$response->Body->stkCallback->ResultDesc;
            $Amount=$response->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            $MpesaReceiptNumber=$response->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            //$Balance=$response->Body->stkCallback->CallbackMetadata->Item[2]->Value;
            $TransactionDate=$response->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $PhoneNumber=$response->Body->stkCallback->CallbackMetadata->Item[3]->Value;

            $payment=Stkrequest::where('CheckoutRequestID',$CheckoutRequestID)->firstOrfail();
            $payment->status='Paid';
            $payment->TransactionDate=$TransactionDate;
            $payment->MpesaReceiptNumber=$MpesaReceiptNumber;
            $payment->ResultDesc=$ResultDesc;
            $payment->save();

        }else{

        $CheckoutRequestID=$response->Body->stkCallback->CheckoutRequestID;
        $ResultDesc=$response->Body->stkCallback->ResultDesc;
        $payment=Stkrequest::where('CheckoutRequestID',$CheckoutRequestID)->firstOrfail();
        
        $payment->ResultDesc=$ResultDesc;
        $payment->status='Failed';
        $payment->save();

        }

    }
    }