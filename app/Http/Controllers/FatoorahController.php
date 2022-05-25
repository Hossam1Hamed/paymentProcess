<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Services\FatoorahService;
use PhpParser\Node\Expr\PostDec;
use App\Models\Transaction; 
class FatoorahController extends Controller
{
    
    private $fatoorahServices;
    public function __construct(FatoorahService $fatoorahServices)
    {   
        $this->fatoorahServices = $fatoorahServices;
    }
    public function payOrder($user_id){
        // $apiURL = 'https://apitest.myfatoorah.com';
        // $apiKey = 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL';
        $user_data = User::findOrFail($user_id);
        
        $postFields =[
            'NotificationOption' => 'Lnk', //'SMS', 'EML', or 'ALL'
            'InvoiceValue'       => '50',
            'CustomerName'       => $user_data->name,
            'CallBackUrl'        => "http://127.0.0.1:8000/api/callBack",
            // 'CallBackUrl'        => "https://google.com",
            'ErrorUrl'           => "https://youtube.com",
        ];

        return $this->fatoorahServices->sendPayment($postFields);
        
    }

    public function callBack(Request $request){
        // dd($request);
        
        $postFields =[];
        $postFields['Key']=$request->paymentId;
        $postFields['KeyType']='paymentId';
        
        $data =$this->fatoorahServices->getPaymentStatus($postFields); 

        $transData =[
            'invoice_id' => $data['Data']['InvoiceId'],
            'status' => $data['Data']['InvoiceStatus'],
            'user_name' => $data['Data']['CustomerName'],
        ];
        Transaction::create($transData);
        return "ok";
    }

    
}
