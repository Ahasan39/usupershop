<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
  </head>
    <style>
      body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
      }
        h1 {
          color: #c82939;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-weight: 900;
          font-size: 40px;
          margin-bottom: 10px;
        }
        p {
          color: #404F5E;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-size:20px;
          margin: 0;
        }
      i {
        color: #c82939;
        font-size: 100px;
        line-height: 200px;
        margin-left:-15px;
      }
      .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
      }
    </style>
    <body>
        <div class="card">
            <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
              <i class="crossmark">❌</i>
            </div>
            <h1>Payment Failed</h1> 
            <p>Your payment has failed. Please try again or contact support.</p>
            <p>Redirecting in <span id="countdown">3</span> seconds...</p>
        </div>
    
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
            let count = 3;
            let countdown = setInterval(function() {
                count--;
                $("#countdown").text(count);
                if (count <= 0) {
                    clearInterval(countdown);
                    @if (isset($returnData['payment_type']) && $returnData['payment_type'] != NULL)
                      @if ($returnData['payment_type'] === 'customer_order')
                        @auth
                          @if (auth()->user()->usertype === 'customer')
                            window.location.href = "{{ route('dashboard') }}";
                          @elseif (auth()->user()->usertype === 'seller' || auth()->user()->usertype === 'vendor')
                            window.location.href = "{{ route('seller.dashboard') }}";
                          @elseif (auth()->user()->usertype === 'dropshipper')
                            window.location.href = "{{ route('dropshipper.dashboard') }}";
                          @else
                            window.location.href = "{{ route('frontend.home') }}";
                          @endif
                        @else
                          window.location.href = "{{ route('frontend.home') }}";
                        @endauth
                      @elseif ($returnData['payment_type'] === 'user_subscription')
                        @auth
                          @if (auth()->user()->usertype === 'seller' || auth()->user()->usertype === 'vendor')
                            window.location.href = "{{ route('seller.dashboard') }}";
                          @elseif (auth()->user()->usertype === 'dropshipper')
                            window.location.href = "{{ route('dropshipper.dashboard') }}";
                          @else
                            window.location.href = "{{ route('frontend.home') }}";
                          @endif
                        @else
                          window.location.href = "{{ route('frontend.home') }}";
                        @endauth
                      @endif
                    @else
                      @auth
                        @if (auth()->user()->usertype === 'customer')
                          window.location.href = "{{ route('dashboard') }}";
                        @elseif (auth()->user()->usertype === 'seller' || auth()->user()->usertype === 'vendor')
                          window.location.href = "{{ route('seller.dashboard') }}";
                        @elseif (auth()->user()->usertype === 'dropshipper')
                          window.location.href = "{{ route('dropshipper.dashboard') }}";
                        @else
                          window.location.href = "{{ route('frontend.home') }}";
                        @endif
                      @else
                        window.location.href = "{{ route('frontend.home') }}";
                      @endauth
                    @endif
                }
            }, 1000);
        </script>
    </body>
    
    
</html>