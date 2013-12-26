openpay-php
===========

PHP client for Openpay API services (version 1.0.0)

This is a client implementing the payment services for Openpay at openpay.mx

Compatibility
-------------

PHP 5.2 or later 

Requirements
------------
PHP 5.2 or later 
cURL extension for PHP
JSON extension for PHP
Multibyte String extension for PHP

Installation
------------

To install, just:

  - Uncompress the file Openpay.v1.zip and add the folder **'Openpay'** inside your
    project.
  - Add the library to the PHP script in which the client library will be used:
```
require(dirname(__FILE__) . '/Openpay/Openpay.php');
```

> NOTE: In the example above, the library is located in a directory called 
> Openpay wich is located inside the same directory that the PHP file in which
> will be used. You must adjust the paths according your project's directory
> structure.

 
Implementation
--------------

#### Configuration #####

Before use the library will be necessary to set up your Merchant ID and
Private key. There are three options:

  - Use the methods **Openpay::setId()** and **Openpay::setApiKey()**. Just 
    pass the proper parameters to each function:
```
Openpay::setId('moiep6umtcnanql3jrxp');
Openpay::setApiKey('sk_3433941e467c4875b178ce26348b0fac');
```
	
  - Pass Merchant ID and Private Key as parameters to the method **Openpay::getInstance()**,
    which is the instance generator:
 
```
$openpay = Openpay::getInstance('moiep6umtcnanql3jrxp', 'sk_3433941e467c4875b178ce26348b0fac');
```
	
  - Configure the Marchant ID and the Private Key as well, as environment 
    variables. This method has its own advantages as this sensitive data is not
    exposed directly in any script.
    
> NOTE: please, refer to PHP documentation for further information about this method.


##### Sandbox Mode #####

Is possible that you want to test your own code when implementing Openpay and 
before charging any credit card. To this purpose use the method **OpenPay::setSandboxMode()** 
which will allow you to active/inactivate the sandbox mode.

````php
Openpay::setSandboxMode(FLAG);
````

If its necessary, you can use the method **Openpay::getSandboxMode()** to retrieve any 
time which is the sandbox mode status:

````php
// TRUE/FALSE, depending on if sandbox mode is activated or not.
Openpay::getSandboxMode(); 
````

#### PHP client library intro #####

Once configured the library, you can use it to interact with Openpay API 
services. The first step is get an instance with the generator:

````php
$openpay = Openpay::getInstance();
````

**$openpay** will be, then, a merchant root instance wich will be used to call 
any available resource in the Openpay API:

  - customers
  - cards
  - charges
  - payouts
  - fees
  - plans

You can access all of these resources as public variables of the root instance, 
so, if you want to add a new customer you will be able to do it as follows:

````php
$openpay->customers->add(PARAMETERS);
````

Every call to any resource will return an instance of such resource, in the 
example above, the call to the method **add()** in the resource **customers** will 
return an instance of Customer, and so. The only exception is when you retrieve
a list of resources using the method **getList()**, in which case an array of 
instances will be returned:

````
// a SINGLE instance of Charge will be returned
$customer = $openpay->customers->add(PARAMETERS);
$charge = $customer->charges->create(PARAMETERS);


// an ARRAY of instances of Charge will be returned
$customer = $openpay->customers->add(PARAMETERS);
$chargeList = $customer->charges->getList(PARAMETERS);
````

The resources derived from Customer resource, according to Openpay API
documentation are:

  - cards
  - bankaccounts
  - charges
  - transfers
  - payouts
  - suscriptions

Those methods which receive mora than one parameter (for example, when trying 
to add a new customer or a new customer's card), the parameters must be passed
as associatives arrays:

````php
array('PARAMETER_NAME' => VALUE, 'PARAMETER_NAME' => VALUE);
array('PARAMETER_NAME' => array('PARAMETER_NAME' => VALUE), 'PARAMETER_NAME' => VALUE);
````

> NOTE: Please refer to Openpay API docuemntation to determine wich parameters 
> are accepted, wich required and which of those are optional, in every case. 


#### Error handling ####

The Openpay API generates several types of errors depending on the situation,
to handle this, the PHP client has implemented five type of exceptions:

  - **OpenpayApiTransactionError**. This category includes those errors ocurred when 
    the transaction does not complete successfully: declined card, insufficient
    funds, inactive destination account, etc.
  - **OpenpayApiRequestError**. It refers to errors generated when a request to the
    API fail. Examples: invalid format in data request, incorrect parameters in
    the request, Openpay internal servers errors, etc.
  - **OpenpayApiConnectionError**. These exceptions are generated when the library 
    try to connect to the API but fails in the attempt. For example: timeouts, 
    domain name servers, etc.
  - **OpenpayApiAuthError**. Errors which are generated when the authentication 
    data are specified in an invalid format or, if are not fully validated on
    the Openpay server (Merchant ID or Private Key).
  - **OpenpayApiError**. This category includes all generic errors when processing
    with the client library.

All these error exceptions make available all the information returned by the 
Openpay API, with the following methods:

  - **getDescription()**: Error description generated by Openpay server.
  - **getErrorCode()**: Error code generated by Openpay server. When the error
    is generated before the request, this value is equal to zero.
  - **getCategory()**: Error category generated and determined by Openpay server.
    When the error is generated before or during the request, this value is an 
    empty string.
  - **getHttpCode()**: HTTP error code generated when request the Openpay
    server. When the error is generated before or during the request, this 
    value is equal to zero.
  - **getRequestId()**: ID generated by the Openpay server when process a 
    request. When the error is generated before the request, this value is
    an empty string.

The following is an more complete example of error catching:

````php
try {
	Openpay::setSandboxMode(true);
	
	// the following line will generate an error because the
	// private key is empty
	$openpay = Openpay::getInstance('moiep6umtcnanql3jrxp', '');

	$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
 	$customer->name = 'Juan';
 	$customer->last_name = 'Godinez';
 	$customer->save();

} catch (OpenpayApiTransactionError $e) {
	error('ERROR on the transaction: ' . $e->getMessage() . 
	      ' [error code: ' . $e->getErrorCode() . 
	      ', error category: ' . $e->getCategory() . 
	      ', HTTP code: '. $e->getHttpCode() . 
	      ', request ID: ' . $e->getRequestId() . ']');

} catch (OpenpayApiRequestError $e) {
	error('ERROR on the request: ' . $e->getMessage());

} catch (OpenpayApiConnectionError $e) {
	error('ERROR while connecting to the API: ' . $e->getMessage());

} catch (OpenpayApiAuthError $e) {
	error('ERROR on the authentication: ' . $e->getMessage());
	
} catch (OpenpayApiError $e) {
	error('ERROR on the API: ' . $e->getMessage());
	
} catch (Exception $e) {
	error('Error on the script: ' . $e->getMessage());
}
````

Examples
--------

#### Customers ####

Add a new customer to a merchant:
````php
$customerData = array(
	'name' => 'Teofilo',
	'last_name' => 'Velazco',
	'email' => 'teofilo@payments.com',
	'phone_number' => '4421112233',
	'address' => array(
		'line1' => 'Av. 5 de Febrero No. 1',
		'line2' => 'Col. Felipe Carrillo Puerto',
		'line3' => 'Zona industrial Carrillo Puerto',
		'postal_code' => '76920',
		'state' => 'Querétaro',
		'city' => 'Querétaro',
		'country_code' => 'MX'));

$openpay = Openpay::getInstance();
$customer = $openpay->customers->add($customerData);
````

Get a customer:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
````

Get the list of customers:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$customerList = $openpay->customers->getList($findData);
````

Update a customer:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$customer->name = 'Juan';
$customer->last_name = 'Godinez';
$customer->save();
````

Delete a customer:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$customer->delete();
````


#### Cards ####

**On a merchant:**

Add a card:
````php
$openpay = Openpay::getInstance();
$card = $openpay->cards->add($cardData);
````

Get a card:
````php
$openpay = Openpay::getInstance();
$card = $openpay->cards->get('k9pn8qtsvr7k7gxoq1r5');
````

Get the list of cards:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$cardList = $openpay->cards->getList($findData);
````

Delete a card:
````php
$openpay = Openpay::getInstance();
$card = $openpay->cards->get('k9pn8qtsvr7k7gxoq1r5');
$card->delete();
````

**On a customer:**

Add a card:
````php
$customerData = array(
	'name' => 'Teofilo',
	'last_name' => 'Velazco Pérez',
	'email' => 'teofilo@payments.com',
	'phone_number' => '4421112233',
	'address' => array(
		'line1' => 'Av. 5 de Febrero No. 1',
		'line2' => 'Col. Felipe Carrillo Puerto',
		'line3' => 'Zona industrial Carrillo Puerto',
		'postal_code' => '76920',
		'state' => 'Querétaro',
		'city' => 'Querétaro',
		'country_code' => 'MX'));
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$card = $customer->cards->add($cardData);
````

Get a card:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$card = $customer->cards->get('k9pn8qtsvr7k7gxoq1r5');
````

Get the list of cards:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$cardList = $customer->cards->getList($findData);
````

Delete a card
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$card = $customer->cards->get('k9pn8qtsvr7k7gxoq1r5');
$card->delete();
````

	
#### Banck Accounts ####

Add a bank account to a customer:
````php
$bankData = array(
	'clabe' => '072910007380090615',
	'alias' => 'Cuenta principal',
	'holder_name' => 'Teofilo Velazco');
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$bankaccount = $customer->bankaccounts->add($bankData);
````

Get a banck account
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$bankaccount = $customer->bankaccounts->get('b4vcouaavwuvkpufh0so');
````

Get the list of bank accounts:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$bankaccount = $customer->bankaccounts->getList($findData);
````

Delete a bank account:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$bankaccount = $customer->bankaccounts->get('b4vcouaavwuvkpufh0so');
$bankaccount->delete();
````

	
#### Charges ####

**On a Merchant:**

Make a charge on a merchant:
````php
$chargeData = array(
	'method' => 'card',
	'source_id' => 'krfkkmbvdk3hewatruem',
	'amount' => 100,
	'description' => 'Cargo inicial a mi merchant',
	'order_id' => 'ORDEN-00071');
$openpay = Openpay::getInstance();
$charge = $openpay->charges->create($chargeData);
````
	
Get a charge:
````php
$openpay = Openpay::getInstance();
$charge = $openpay->charges->get('tvyfwyfooqsmfnaprsuk');
````
	
Get list of charges:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$charge = $openpay->charges->getList($findData);
````
	
Make a refund:
````php
$refundData = array(
	'description' => 'Devolución' );
$openpay = Openpay::getInstance();
$charge = $openpay->charges->get('tvyfwyfooqsmfnaprsuk');
$charge->refund($refundData);
````

**On a Customer:**

Make a charge on a customer:
````php
$chargeData = array(
	'source_id' => 'tvyfwyfooqsmfnaprsuk',
	'method' => 'card',
	'amount' => 100,
	'description' => 'Cargo inicial a mi cuenta',
	'order_id' => 'ORDEN-00070');
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$charge = $customer->charges->create($chargeData);
````

Get a charge:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$charge = $customer->charges->get('tvyfwyfooqsmfnaprsuk');
````

Get list of charges:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$charge = $customer->charges->getList($findData);
````
	
Make a refund:
````php
$refundData = array(
	'description' => 'Reembolso' );
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$charge = $customer->charges->get('tvyfwyfooqsmfnaprsuk');
$charge->refund($refundData);
````


#### Transfers ####

Make a transfer:
````php
$transferData = array(
	'customer_id' => 'aqedin0owpu0kexr2eor',
	'amount' => 12.50,
	'description' => 'Cobro de Comisión',
	'order_id' => 'ORDEN-00061');
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$transfer = $customer->transfers->create($transferData);
````
	
Get a transfer:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$transfer = $customer->transfers->get('tyxesptjtx1bodfdjmlb');
````

Get list of transfers:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$transfer = $customer->transfers->getList($findData);
````


#### Payouts ####

**On a Merchant:**

Make a payout on a merchant:
````php
$payoutData = array(
	'method' => 'card',
	'destination_id' => 'krfkkmbvdk3hewatruem',
	'amount' => 500,
	'description' => 'Retiro de saldo semanal',
	'order_id' => 'ORDEN-00072');
$openpay = Openpay::getInstance();
$payout = $openpay->payouts->create($payoutData);
````

Get a payout:
````php
$openpay = Openpay::getInstance();
$payout = $openpay->payouts->get('t4tzkjspndtj9bnsop2i');
````
	
Get list of payouts:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$payout = $openpay->payouts->getList($findData);
````

**On a Customer:**

Make a payout on a customer:
````php
$payoutData = array(
	'method' => 'card',
	'destination_id' => 'k9pn8qtsvr7k7gxoq1r5',
	'amount' => 1000,
	'description' => 'Retiro de saldo semanal',
	'order_id' => 'ORDEN-00062');
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$payout = $customer->payouts->create($payoutData);
````
	
Get a payout:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$payout = $customer->payouts->get('tysznlyigrkwnks6eq2c');
````
	
Get list pf payouts:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$payout = $customer->payouts->getList($findData);
````


#### Fees ####

Make a fee charge
````php
$feeData = array(
	'customer_id' => 'a9ualumwnrcxkl42l6mh',
	'amount' => 12.50,
	'description' => 'Cobro de Comisión',
	'order_id' => 'ORDEN-00063');
$openpay = Openpay::getInstance();
$fee = $openpay->fees->create($feeData);
````
	
Get list of fees charged:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$fee = $openpay->fees->getList($findData);
````
	

#### Plans ####

Add a plan:
````php
$planData = array(
	'amount' => 150.00,
	'status_after_retry' => 'cancelled',
	'retry_times' => 2,
	'name' => 'Plan Curso Verano',
	'repeat_unit' => 'month',
	'trial_days' => '30',
	'repeat_every' => '1',
	'currency' => 'MXN');
$openpay = Openpay::getInstance();
$plan = $openpay->plans->add($planData);
````
	
Get a plan:
````php
$openpay = Openpay::getInstance();
$plan = $openpay->plans->get('pduar9iitv4enjftuwyl');
````
	
Get list of plans: 
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$plan = $openpay->plans->getList($findData);
````

Update a plan:
````php
$openpay = Openpay::getInstance();
$plan = $openpay->plans->get('pduar9iitv4enjftuwyl');
$plan->name = 'Plan Curso de Verano 2014';
$plan->save();
````
	
Delete a plan:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$plan = $openpay->plans->get('pduar9iitv4enjftuwyl');
$plan->delete();
````

Get list of subscriptors of a plan: 
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$plan = $openpay->plans->get($planId);
$subscription = $plan->subscriptions->getList($findData);
````


#### Subscriptions ####

Add a subscription:
````php
$subscriptionData = array(
	'trial_days' => '90',
	'plan_id' => 'pduar9iitv4enjftuwyl',
	'card_id' => 'konvkvcd5ih8ta65umie');
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$subscription = $customer->subscriptions->add($subscriptionData);
````
	
Get a subscription:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$subscription = $customer->subscriptions->get('s7ri24srbldoqqlfo4vp');
````

Get list of subscriptions:
````php
$findData = array(
	'creation[gte]' => '2013-01-01',
	'creation[lte]' => '2013-12-31',
	'offset' => 0,
	'limit' => 5);
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$subscription = $customer->subscriptions->getList($findData);
````
	
Update a subscription:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$subscription = $customer->subscriptions->get('s7ri24srbldoqqlfo4vp');
$subscription->trial_end_date = '2014-12-31';
$subscription->save();
````
	
Delete a subscription:
````php
$openpay = Openpay::getInstance();
$customer = $openpay->customers->get('a9ualumwnrcxkl42l6mh');
$subscription = $customer->subscriptions->get('s7ri24srbldoqqlfo4vp');
$subscription->delete();
````
	