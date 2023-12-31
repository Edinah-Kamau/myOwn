<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Won Bids</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="{{asset('css/admin_style.css')}}">
   <link rel="stylesheet" href="{{asset('css/delete.css')}}">

   <style>
   #wrapper {
      border: 1px solid white;
      margin: 30px;
      align: center;
      display: block;
      border: 1px solid white;
      height: 500px;
      width: 100%;
      overflow: hidden; /* Add overflow property to prevent content from overflowing */
   }

   #first {
      height: 500px;
      width: 698px;
      position: static;
      display: inline-block;
      padding-right: 10px;
      float: left;
      margin-right:10px;
      border: 1px solid white;
      overflow: hidden; /* Add overflow property to prevent content from overflowing */
   }

   #second {
      height: 500px;
      display: inline-block;
      position: static;
      float: left;
      border: 1px solid white;
      padding-right:10px;
      width: calc(100% - 728px); /* Calculate the remaining width after considering the width of the first div and its margin */
      word-wrap: break-word; /* Add word-wrap property to break long words and wrap text within the second div */
   }

   .modal-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
      z-index: 9999; /* Make sure it appears on top of other elements */
      display: flex;
      align-items: center;
      justify-content: center;
   }

   .modal-body {
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      max-width: 400px;
      width: 100%;
   }
   </style>
   
   </head>

   @include('bidderHeader');

   <body>

       <h3 style="text-align:center; font-size:50px;">Won Bids</h3>

       @foreach($won as $bid)
      
       <div id="wrapper">

       <div id="first">
           <img height="500px;" width="698px;" src="{{ asset('uploads/files/'. $bid->posted->image) }}">
       </div>

       <div id="second">
         <p style="color:#721c08; font-size:15px; text-align:left;"> <span style="color:#721c08; font-size:15px;"> Congratulations {{Auth::user()->name}},&#x1F525;&#x1F389; </span></p>
         <p style="color:#721c08; font-size:15px; text-align:left;"> <span style="color:#721c08; font-size:15px;"> You have won the bid! Would you like to purchase this art piece?  </span></p>
         <br>
         <p style="color:#666; font-size:15px; text-align:left;"> Art Piece: <span style="color:black; font-size:15px;"> {{ $bid->posted->product_name }}</span></p>
         <p style="color:#666; font-size:15px; text-align:left;"> Description: <span style="color:black; font-size:15px;"> {{ $bid->posted->description }}</span></p>
         <p style="color:#666; font-size:15px; text-align:left;">Requested Units: <span style="color:black; font-size:15px;"> {{ $bid->requested_units }}</span></p>
         <p style="color:#666; font-size:15px; text-align:left;">Bidded Amount:  <span style="color:black; font-size:15px;">Ksh.  {{ $bid->amount }}</span></p>
         <p style="color:#666; font-size:15px; text-align:left;">Seller Email:  <span style="color:black; font-size:15px;">  {{ $bid->posted->seller_email }}</span></p>
         
   

   <button> <a href="#modal" role="button" class="delete-btn" style="text-decoration:none; align-content:left; background-color:#721c08;">Decline</a></button>
   <button class="delete-btn" style="text-decoration:none; align-content:left; background-color:#725108;">
   <a href="{{ route('checkout', ['productName' => $bid->posted->product_name, 'email' => Auth::user()->email, 'description' => $bid->posted->description, 'image' => $bid->posted->image, 'units' => $bid->requested_units, 'amount' => $bid->amount, 'sellerEmail' => $bid->posted->seller_email]) }}" style="text-decoration:none; color: white;">Proceed to checkout</a>
</button>




<!-- Modal -->
<div class="modal-wrapper" id="modal">
      <div class="modal-body card">
         <div class="modal-header">
            
            <br></br>
            <a href="#!" role="button" class="close" aria-label="close this modal" style="color: red;">
               <svg viewBox="0 0 24 24">
                  <path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z" />
               </svg>&nbsp;&nbsp;
            </a>
         </div>

         <img style="margin-left:30%;" height="200px;" width="200px;" src="{{ asset('uploads/files/' .$bid->posted->image) }}">

         <p style="text-align:center; font-size:15px; color:purple;">Arge Auction Shop</p>

         <p style="text-align:center; font-size:15px;">Are You sure you want to decline ? </p>

         <button> <a href="{{route('expire.status', ['user_id' => $bid->posted->product_id , 'status_code' => 'Cannot Buy']) }}" style="text-decoration: none; background-color:#721c08;" class="delete-btn" >Decline</i></a></button>
         
      </div>
      
      <a href="#!" class="outside-trigger"></a>
   </div>


   
   </div>

   
</div>








   @endforeach

   </body>
</html>