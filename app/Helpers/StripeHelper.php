<?php

namespace App\Helpers;

use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Stripe\Payout;
use Stripe\Stripe;

class StripeHelper
{
  public static function secretKey()
  {

    return new \Stripe\StripeClient(Helper::_get_settings('stripe_client_key_live'));
  }

  public static function createConnectAccount($email)
  {
    $stripe = self::secretKey();
    $stripeAccount = $stripe->accounts->create([
      'type'    => 'express',
      'country' => 'US',
      'email'   => $email,
      'capabilities'    => [
        'card_payments' => ['requested' => true],
        'transfers'     => ['requested' => true],
      ],
      ]);

    return $stripeAccount;
    
  }

  public static function createOnBoardingLink($accountId)
  {
    $stripe = self::secretKey();
    $accountLink = $stripe->accountLinks->create([
   
      'account' => $accountId,
      'refresh_url' => route('onboarding.refresh'),
      'return_url'  => route('/payout-details'),
      'type'        => 'account_onboarding',
   
    ]);
    
    return $accountLink->url;
  }

  public static function retrivePaymentMethod($source)
  {
    $stripe = self::secretKey();
   
    $defaultPaymentMethod = $stripe->paymentMethods->retrieve($source);

    return $defaultPaymentMethod;
  }

  public static function deleteConnectAccount($accountId)
  {
    $stripe = self::secretKey();
    $delete = $stripe->accounts->delete(
      $accountId,
      []
    );
    return $delete;
  }

  public static function getAccountDetails($accountId)
  {
    $stripe = self::secretKey();
    try {
      $account = $stripe->accounts->retrieve(
        $accountId,
        []
      );

      $data =[

        'status' => true,
        'data'   => $account->jsonSerialize() 
      ];
      return $data;
    } catch (\Throwable $th) {
      
      return  $data =[

        'status' => false,
        'data'   => null 
      ];
    }

    
  }

  public static function payConnectedAccount($accountId, $amount, $fullname)
  {
    $response = ['status' => false, 'data' => []];

    try {
      //$amount = $amount;
      $stripe = self::secretKey();
      $transfer = $stripe->transfers->create([
        'amount' => $amount * 100,
        'currency' => 'usd',
        'destination' => $accountId,
        'description' => 'Payout by ' . $fullname
      ]);

      $response['status'] = true;
      $response['msg']    = 'Payout successfully';
      $response['data']   = $transfer->jsonSerialize();
    
    } catch (\Exception $e) {
      
      $response['msg'] = $e->getMessage();
      
    }


    return $response;

  }

  public static function retriveSession($sessionId)
  {
    $stripe = self::secretKey();
    $checkoutSession = $stripe->checkout->sessions->retrieve($sessionId);
    return $checkoutSession;
  }

  public static function retrieveSetupIntent($setupIntentId)
  {
    $stripe = self::secretKey();
    $setupIntentObj = $stripe->setupIntents->retrieve($setupIntentId);
    return $setupIntentObj;
  }

  public static function retriveCustomer($customerId)
  {
    $stripe = self::secretKey();
    $retrive = $stripe->customers->retrieve(
      $customerId,
      []
    );
    return $retrive;
  }

  public static function updatePaymentMethod($paymentMethodId, $customerId)
  {
    $stripe = self::secretKey();
    $update = $stripe->customers->update(
      $customerId,
      [
        'invoice_settings' => [
        'default_payment_method' => $paymentMethodId,
        ],
      ]
    );
    return $update;
  }

  public static function attachPaymentToCustomer($paymentMethodId, $customerId)
  {
    $stripe = self::secretKey();
    $attachPaymentMethodToCustomer = $stripe->paymentMethods->attach(
      $paymentMethodId,
      ['customer' => $customerId]
    );
    return $attachPaymentMethodToCustomer;
  }

  public static function createPaymentIntent($amount, $customerId, $cardId = null)
  {
    $amountInCents = $amount * 100; 
    $stripe = self::secretKey();
    $data = [
      'amount'   => $amountInCents,
      'currency' => 'usd',
      'customer' => $customerId,
      'payment_method_types' => ['card'],
      'setup_future_usage' => 'off_session',
    ];
    $paymentIntent = $stripe->paymentIntents->create($data);
    return $paymentIntent;

  }

  public static function payUsingPaymentIntent($amount, $customerId, $paymentMethod)
  {
    $response = ['status' => false];
    try {
      $stripe = self::secretKey();
      $data = [
        'amount'   => _price_format($amount * 100),
        'currency' => 'USA',
        'customer' => $customerId,
        'payment_method' => $paymentMethod,
        'off_session'    => true,
        'confirm'        => true,
      ];
      $chargeAmount =  $stripe->paymentIntents->create($data);
      $response['status'] = true;
      $response['data'] = $chargeAmount->jsonSerialize();
    } catch (\Stripe\Exception\CardException $e) {
      $response['msg'] = "Card Error: " . $e->getMessage();
    } catch (\Stripe\Exception\RateLimitException $e) {
      $response['msg'] = "Rate Limit Error: " . $e->getMessage();
    } catch (\Stripe\Exception\InvalidRequestException $e) {
      $response['msg'] = "Invalid Request Error: " . $e->getMessage();
    } catch (\Stripe\Exception\AuthenticationException $e) {
      $response['msg'] = "Authentication Error: " . $e->getMessage();
    } catch (\Stripe\Exception\ApiConnectionException $e) {
      $response['msg'] = "API Connection Error: " . $e->getMessage();
    } catch (\Stripe\Exception\ApiErrorException $e) {
      $response['msg'] = "Stripe API Error: " . $e->getMessage();
    } catch (\Exception $e) {
      $response['msg'] = "Stripe API Error: " . $e->getMessage();
    }
    return $response;
  }


  public static function createPaymentIntentApplePay($amount, $customerId)
  {
    $stripe = self::secretKey();
    $amounts = $amount * 100;

    $data = [
      'amount'   => $amounts,
      'currency' => 'usd',
      'customer' => $customerId,
      'setup_future_usage' => 'off_session',
    ];

    $paymentIntent = $stripe->paymentIntents->create($data);
    return $paymentIntent;

  }


  public static function createPrice($amount, $productName)
  {

    $stripe = self::secretKey();
    $data = [
      'currency'    => 'cop',
      'unit_amount' => _price_format($amount * 100),
      'recurring'   => ['interval' => 'month'],
      'product_data'=> ['name' => $productName],
    ];
    $price = $stripe->prices->create($data);
    return $price;
  
  }

  public static function createSubscription($customerId, $priceID, $paymentMethodId)
  {
    $stripe = self::secretKey();

    $data = [
      'customer' => $customerId,
      'items'    => [['price' => $priceID]],
      'default_payment_method' => $paymentMethodId,
      'expand'   => ['latest_invoice.payment_intent'],
    ];
    $subscription = $stripe->subscriptions->create($data);
    return $subscription;
  
  }

  public static function subscriptionRetrive($subId)
  {
    $stripe = self::secretKey();
    $subscription = $stripe->subscriptions->retrieve($subId);
    return $subscription;
  }

  public static function subscriptionUpdate($subId, $amount)
  {
    
    $subscription = self::subscriptionRetrive($subId);
    $createPrice  = StripeHelper::createPrice($amount, 'Driver Subscription');
    $stripe = self::secretKey();
    $stripe->subscriptions->update($subId, [
      'items' => [
        [
          'id' => $subscription->items->data[0]->id,
          'price' => $createPrice->id,
        ],
      ],
    ]);
  }

  public static function cancelSubscription($subscriptionId)
  {
    $stripe = self::secretKey();
    $canceledSubscription = $stripe->subscriptions->cancel($subscriptionId);
    return $canceledSubscription;
  }

  public static function cancelSubscriptionAtEndOfPeriod($subscriptionId)
  {
    $stripe = self::secretKey();
    $subscriptionUpdate = $stripe->subscriptions->update($subscriptionId, [
      'cancel_at_period_end' => true
    ]);
    return $subscriptionUpdate;
  }

  public static function refundAmount($chargeId, $amountToRefund)
  {

    $stripe = self::secretKey();
    $refund = $stripe->refunds->create([
      'charge' => $chargeId,
      'amount' => _price_format($amountToRefund * 100),
      'reason' => 'Did not use the trip'
    ]);

    return $refund;

  }

  public static function createEphemeralKeys($customerId)
  {
    $stripe = self::secretKey();
    $ephemeralKey = $stripe->ephemeralKeys->create([
      'customer' => $customerId,
    ], [
      'stripe_version' => '2023-08-16',
    ]);

    return $ephemeralKey;
  }

  public static function confirmPaymentIntent($paymentIntentId, $paymentMethodId)
  {
    $stripe  = self::secretKey();
    $confirm = $stripe->paymentIntents->confirm(
      $paymentIntentId,
      ['payment_method' => $paymentMethodId]
    );
    return $confirm;
  }


  public static function capturePaymentIntent($paymentIntentId)
  {
    $stripe  = self::secretKey();
    $capture = $stripe->paymentIntents->capture(
      $paymentIntentId,
      []
    );
    return $capture;
  }

  public static function retrivePaymentIntent($paymentIntentId)
  {
    $stripe = self::secretKey();
    $retrievePaymentIntent = $stripe->paymentIntents->retrieve(
      $paymentIntentId,
      []
    );
    return $retrievePaymentIntent;
  }

  public static function createCharge($cardID, $amount, $customerId, $fullname)
  {
    $response = ['status' => false];
    try {
      $stripe = self::secretKey();
      $chargeAmount = $stripe->charges->create([
        'amount'      => Helper::formatPriceToInt($amount),
        'currency'    => 'usd',
        'description' => "Subscription purchased by". $fullname,
        'source'      => $cardID,
        'customer'    => $customerId,
      ]);

      $response['status'] = true;
      $response['data'] = $chargeAmount->jsonSerialize();
    } catch (\Stripe\Exception\CardException $e) {
      $response['msg'] = "Card Error: " . $e->getMessage();
    } catch (\Stripe\Exception\RateLimitException $e) {
      $response['msg'] = "Rate Limit Error: " . $e->getMessage();
    } catch (\Stripe\Exception\InvalidRequestException $e) {
      $response['msg'] = "Invalid Request Error: " . $e->getMessage();
    } catch (\Stripe\Exception\AuthenticationException $e) {
      $response['msg'] = "Authentication Error: " . $e->getMessage();
    } catch (\Stripe\Exception\ApiConnectionException $e) {
      $response['msg'] = "API Connection Error: " . $e->getMessage();
    } catch (\Stripe\Exception\ApiErrorException $e) {
      $response['msg'] = "Stripe API Error: " . $e->getMessage();
    }
    return $response;
  }

  public static function retriveToken($tokenId)
  {
    $stripe = self::secretKey();
    $token  = $stripe->tokens->retrieve(
      $tokenId,
      []
    );
    return $token;

  }

  public static function createCustomer($user, $tokenId)
  {
    $stripe = self::secretKey();
    $createCustomer = $stripe->customers->create([
      'description' => 'Create a customer',
      'email'   => $user->email,
      'name'    => $user->name,
      'source'  => $tokenId,
      'phone'   => $user->mobile_phone,
    ]);

    return $createCustomer;
  }

  public static function createCustomerApplePay($user)
  {
    $stripe = self::secretKey();

    $createCustomer = $stripe->customers->create([
  
      'description' => 'Create a customer',
      'email' => $user->email,
      'name'  => $user->name,
      'phone' => $user->mobile_phone,
  
    ]);

    return $createCustomer;
  }

  public static function getAllSources($customerId)
  {
    $stripe   = self::secretKey();
    $getCards = $stripe->customers->allPaymentMethods($customerId, 
  
  );

    return $getCards;
  }

  public static function createSource($customerId, $tokenId)
  {
    $stripe = self::secretKey();
    $createCards = $stripe->customers->createSource($customerId, ['source' => $tokenId]);
    return $createCards;
  
  }

  public static function deleteCard($customerId, $cardId)
  {
    $stripe = self::secretKey();
    $detachCard = $stripe->paymentMethods->detach($cardId, []);
    return $detachCard;

  }

  public static function setDefaultCard($customerId, $cardId)
  {
    
    $stripe = self::secretKey();
    $customer = $stripe->customers->update($customerId, [
      
      'invoice_settings' => [
      
      'default_payment_method' => $cardId,
      
      ],
    ]);

    return $customer;
  }

public static function CreateCustomerAccount($name,$email){

  $stripe = self::secretKey();
  $customer = $stripe->customers->create([
    'name'  => $name,
    'email' => $email,
  ]);

 return $customer;
 
}


public static function getDefaultCard($customerId)
{
    try {
        
        $stripe   = self::secretKey();
        $customer = $stripe->customers->retrieve($customerId);
        $defaultPaymentMethodId = $customer->invoice_settings->default_payment_method;

        if (!$defaultPaymentMethodId) {
            return response()->json([
                'status'  => false,
                'message' => 'No default card found for this customer.',
            ], 404);
        }

        $paymentMethod = $stripe->paymentMethods->retrieve($defaultPaymentMethodId);
        return [
                'id'        => $paymentMethod->id,
                'brand'     => $paymentMethod->card->brand,
                'last4'     => $paymentMethod->card->last4,
                'exp_month' => $paymentMethod->card->exp_month,
                'exp_year'  => $paymentMethod->card->exp_year,
        ];

    } catch (ApiErrorException $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}


public static function createStripeConnectLink()
{

    $clientId = Helper::_get_settings('stripe_client_ID_live');
  
    $redirectUri = asset("api/handel-stripe-account-callback");  
    $url = 'https://connect.stripe.com/oauth/authorize?' . http_build_query([
        'response_type' => 'code',
        'client_id'     => $clientId,
        'scope'         => 'read_write', 
        'redirect_uri'  => $redirectUri, 
        'state'         => auth()->user()->id,
        'country'       => 'US',
      ]);

    return $url;

}


public static function sendPayoutToConnectedAccount($connectedAccountId, $amount)
{
  Stripe::setApiKey(Helper::_get_settings('stripe_client_key_live'));  

    try {
        $payout = Payout::create([
            'amount' => $amount, 
            'currency' => 'usd', 
        ], [
            'stripe_account' => $connectedAccountId, 
        ]);

        return $payout; 
    } catch (\Stripe\Exception\UnknownApiErrorException $e) {

        return response()->json(['error' => $e->getMessage()], 400);
    }
}


}