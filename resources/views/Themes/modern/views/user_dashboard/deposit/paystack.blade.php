@extends('user_dashboard.layouts.app')
@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">Deposit Fund</h3>
            </div>
            <!-- Page title end-->
            <div class="row mt-4">
                <div class="col-xl-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">Create Deposit</h3>
                        <p class="text-gray-500 text-16"> Enter your deposit amount, currency and payment method</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-xl-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active"><a href="#">Create</a></li>
                                    <li class="breadcrumb-first"><a href="#">Confirmation</a></li>
                                    <li class="active">Success</li>
                                </ol>
                            </div>
                            <div class="bg-secondary mt-5 shadow p-35">
                                <div id="alert-holder"></div>
                                @include('user_dashboard.layouts.common.alert')
								<div>
                                    <h1>hllo</h1>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('user_dashboard.layouts.common.help')
@endsection
@section('js')
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        // change this to your public key so you 
        // will no more be prompted
        var public_key = 'pk_test_9d97cf0be86b0758ece444694d57a8db41a4be59';
        
        /*
         * check if the public key set is valid
         * 
         * @return bool
         */
        function isValidPublicKey(){
          var publicKeyPattern = new RegExp('^pk_(?:test|live)_','i');
          return publicKeyPattern.test(public_key);
        }
        
        
        /* 
         * Validate before opening Paystack popup
         */
        function buyCreditPaystack(){
            let amount =  '<?php echo  $data['amount'] ; ?>'
            let baseamount =  Number(amount * 100)
            let currency =    '<?php echo  $data['currencyCode'] ; ?>'
            let email  = '<?php echo  auth()->user()->email ; ?>'
            let firstname = '<?php echo  auth()->user()->first_name ; ?>'
            let lastname = '<?php echo  auth()->user()->last_name ; ?>'
        
            let meta = {}
            meta.user_id = '<?php echo  auth()->user()->id ; ?>'
            meta.description = 'Buy Authoran Credit'
            meta.txntype = 'buycredit'

          payWithPaystack( email, baseamount, firstname, lastname, currency, meta);
        }
      
        /* Get a random reference number based on the current time
         * 
         * gotten from http://stackoverflow.com/a/2117523/671568
         * replaced UUID with REF
         */
        function generateREF(){
          var d = new Date().getTime();
          if(window.performance && typeof window.performance.now === "function"){
            d += performance.now(); //use high-precision timer if available
          }
          var ref = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = (d + Math.random()*16)%16 | 0;
            d = Math.floor(d/16);
            return (c=='x' ? r : (r&0x3|0x8)).toString(16);
          });
          return ref;
        }
        
      
        /* Show the paystack payment popup
         * 
         * source: https://developers.paystack.co/docs/paystack-inline
         */
        function payWithPaystack(email, baseamount, firstname, lastname, currency, meta){
          var handler = PaystackPop.setup({
            key:       public_key,
            email:     email,
            firstname: firstname,
            lastname:  lastname,
            amount:    baseamount,
            currency: currency,
            ref:       generateREF(), 
            metadata: meta,
            callback:  function(response){
              // payment was received
                document.getElementById('alert-holder').innerHTML = '<div class="alert bg-gray-100">' + 'Please wait while we verify your subscription' + '<img class="mx-auto" src="/public/images/preloaders/48x48.gif">' + '</div>';
                    verifyTransaction(response)
            },
            onClose:  function(){
              // Visitor cancelled payment
              var msg = 'Cancelled. Please click the \'Pay\' button to try again';
              document.getElementById('alert-holder').innerHTML = '<div class="alert alert-danger">' + msg + '</div>';
            }
          });
          handler.openIframe();
        }


        function verifyTransaction(payload){
            const http = new XMLHttpRequest()
            let url = 'paystack-confirm-payment'
            http.open('POST', url)

            http.setRequestHeader("Content-Type", "application/json");
            http.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            
            // send JSON data to the remote server
            http.send(JSON.stringify(payload))

            http.onload = (res) => {

                 if (http.status === 200) {
                        data = JSON.parse(http.response)
                        var msg = '<b>Success:</b> The subscription reference is ' + data.data.subscription_code;
                        document.getElementById('alert-holder').innerHTML = '<div class="alert bg-gray-100">' + msg + '<img class="mx-auto" src="/public/images/preloaders/48x48.gif">' + '</div>';

                    
                        setTimeout(function(){
                           var msg = 'You will be redirected to your dashboard in 2secs';
                           document.getElementById('alert-holder').innerHTML = '<div class="alert bg-gray-100">' + msg + '<img class="mx-auto" src="/public/images/preloaders/48x48.gif">' + '</div>'
                        }, 2000)
                        setTimeout(function(){
                            window.location = '/wallet-list'
                        }, 5000)

                } else if (http.status === 404) {
                    console.log("No transaction found")
                }

            }


            http.onerror = function() {
              console.log("Network error occurred")
            }

        }

        buyCreditPaystack()

    </script>
@endsection