@extends('admin.layouts.default')
@section('content')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css">
    <div class="right_col" role="main">
        <div class="col-md-12 col-sm-12  ">

            <div class="x_panel">

                <div class="x_content">

                    <h2>Customer Details - {{ $order->user->id }}</h2>

                    <div class="container">
                        <form class="form">


                            <div class="row">

                                <div class="col">
                                    <label for="">First Name</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->user->first_name ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>
                                <div class="col">
                                    <label for="">Last Name</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->user->last_name ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>
                                <div class="col">
                                    <label for="">Mobile Number</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->user->mobile ?? '' }}" placeholder="Please Enter order number">
                                </div>

                                <div class="col">
                                    <label for="">Email</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->user->email ?? '' }}" placeholder="Please Enter order number">
                                </div>


                                <div class="col">
                                    <label for="">Zip Code</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->user->zip_code ?? '' }}" placeholder="Please Enter order number">
                                </div>

                            </div>
                    </div>
                    <br>

                    <h2>Delivery Address</h2>


                    <div class="row">

                        <div class="col">
                            <label for="">zip code</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->deliveryaddress->zip_code ?? '' }}"
                                placeholder="Please Enter order number">
                        </div>
                        <div class="col">
                            <label for="">Country</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->deliveryaddress->country ?? '' }}"
                                placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">State</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->deliveryaddress->state ?? '' }}" placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">City</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->deliveryaddress->city ?? '' }}" placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">Full Address</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->deliveryaddress->address ?? '' }}"
                                placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">Street Address</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->deliveryaddress->street ?? '' }}"
                                placeholder="Please Enter order number">
                        </div>

                    </div>
                </div>
                </form>
            </div>


            <div class="x_panel">

                <div class="x_content">

                    <h2>Seller Details - {{ $order->seller->id }}</h2>

                    <div class="container">
                        <form class="form">


                            <div class="row">
                                <div class="col">
                                    <label for="">First Name</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->seller->first_name ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>
                                <div class="col">
                                    <label for="">Last Name</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->seller->last_name ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>
                                <div class="col">
                                    <label for="">Mobile Number</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->seller->mobile ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>

                                <div class="col">
                                    <label for="">Email</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->seller->email ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>

                                <div class="col">
                                    <label for="">Zip Code</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->seller->zip_code ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>

                            </div>
                            <br>
                            {{-- <h2>Pickup Address</h2> --}}
                    </div>
                </div>
                </form>
            </div>


            <div class="x_panel">

                <div class="x_content">

                    <h2>Shipping Details</h2>

                    <div class="container">
                        <form class="form">


                            <div class="row">
                                <div class="col">
                                    <label for="">Quote Id</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->orderShipment?->quote_id ?? '' }}"
                                        placeholder="Uber connect Quote Id ">
                                </div>
                                <div class="col">
                                    <label for="">Delivery Id</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->orderShipment?->delivery_id ?? '' }}"
                                        placeholder="Uber Connect Delivery Id">
                                </div>
                                <div class="col">
                                    <label for="">Batch Id</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->orderShipment?->batch_id ?? '' }}"
                                        placeholder="Uber Connect Batch Id">
                                </div>

                                <div class="col">
                                    <label for="">Amount</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->orderShipment?->amount ?? '' }}"
                                        placeholder="Shipping Amount">
                                </div>

                                <div class="col">
                                    <label for="">Status</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->orderShipment?->status ?? '' }}"
                                        placeholder="Shipping Status">
                                </div>

                                <div class="col">
                                    <label for="">Delivery Date</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->orderShipment?->created_at ?? '' }}"
                                        placeholder="Delivery Date">
                                </div>

                            </div>
                            <br>
                    </div>
                </div>
                </form>
            </div>



            <div class="x_panel">
                <div class="x_content">
                    <h2>Order Details - {{ $order->order_number }}</h2>
                    <div class="container">
                        <form class="form">
                            <div class="row">
                                <div class="col">
                                    <label for="">Order Number</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->order_number ?? '' }}" placeholder="Please Enter order number">
                                </div>

                                <div class="col">
                                    <label for="">Order By</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->user->first_name ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>

                                <div class="col">
                                    <label for="">Seller</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->user->first_name ?? '' }}"
                                        placeholder="Please Enter order number">
                                </div>
                            </div>
                    

                    <div class="row">
                        <div class="col">
                            <label for="">Net Amount</label>
                            <input readonly type="text" class="form-control" value="{{ $order->net_amount ?? '' }}"
                                placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">Order Total</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->total_amount ?? '' }}" placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">Product Price</label>
                            <input readonly type="text" class="form-control" value="{{ $data->price ?? '' }}"
                                placeholder="Please Enter order number">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label for="">Discount Amount</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->discount_price ?? '' }}" placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">Payment Status</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->payment_status ?? '0' }}" placeholder="Please Enter order number">
                        </div>

                        <div class="col">
                            <label for="">Delivery Status</label>
                            <input readonly type="text" class="form-control"
                                value="{{ $order->delivery_status ?? '' }}" placeholder="Please Enter order number">
                        </div>

                    </div>
               
                </form>
                    </div>
                </div>
            </div>

            <div class="x_panel">

                <div class="x_content">

                    <h2>Commission Details</h2>

                    <div class="container">
                        <form class="form">
                            <div class="row">

                                <div class="col">
                                    <label for="">Seller tax</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->seller_tax ?? '0' }}" placeholder="Please Enter order number">
                                </div>

                                <div class="col">
                                    <label for="">Seller Commission</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->seller_tax_amount ?? '0' }}"
                                        placeholder="Please Enter order number">
                                </div>

                                <div class="col">
                                    <label for="">Service Fee</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $order->service_fee ?? '0' }}"
                                        placeholder="Please Enter order number">
                                </div>
                            </div>
                    </div>

                </div>
                </form>
            </div>


            <div class="x_panel">
                <div class="x_content">
                    <h2>Product Details - {{ $data->product_name ?? null }}</h2>

                    <div class="container">
                        <form class="form">
                            <div class="row">

                                <div class="col">
                                    <label for="">Product name</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->product_name ?? '' }}" placeholder="Please Enter Product Name">
                                </div>

                                <div class="col">
                                    <label for="">Category Name</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->category->name ?? '' }}"
                                        placeholder="Please Enter Category Name">
                                </div>

                                <div class="col">
                                    <label for="">Brand Name</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->brand->name ?? '' }}" placeholder="Please Enter Brand Name">
                                </div>
                            </div>


                            <div class="row">
                                <div class="col">
                                    <label for="">Size</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->size->name ?? '' }}" placeholder="Please Enter Size ">
                                </div>

                                <div class="col">
                                    {{-- <div class="row">

                                        <div class="col-2">
                                            
                                        </div>

                                        <div class="col-2">
                                            
                                        </div>

                                    </div> --}}
                                    <label for="colorInput"> Color <span
                                        style="background-color: {{ $data->Color->color_code ?? null }}; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span></label> 
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->Color->name ?? 'No Data' }}" placeholder="Please Enter Color">

                                </div>
                                <div class="col">
                                    <label for="">Material</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->material->name ?? 'No Data' }}"
                                        placeholder="Please Enter Material Name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label for="">Cloth type</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->cloth_type ?? '' }}" placeholder="Please Select Cloth Type">
                                </div>

                                <div class="col">
                                    <label for="colorInput"> Suitable For </label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->suitable->name ?? 'No Data' }}"
                                        placeholder="Please Enter Suitable For">

                                </div>

                                <div class="col">
                                    <label for="">Condition</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->condition->name ?? 'No Data' }}"
                                        placeholder="Please Enter Condition">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label for="">Stock</label>
                                    <input readonly type="text" class="form-control" value="{{ $data->stock ?? '' }}"
                                        placeholder="Please Enter Stock">
                                </div>

                                <div class="col">
                                    <label for="colorInput">Price </label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->price ?? 'No Data' }}" placeholder="Please Enter Price">
                                </div>

                                <div class="col">
                                    <label for="colorInput">Status</label>
                                    <input readonly type="text" class="form-control"
                                        value="{{ $data->status ?? 'No Data' }}" placeholder="Please Select Status">
                                </div>
                            </div>


                            <div class="row mb-3">
                                <div class="col">

                                
                                <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                                <textarea readonly class="form-control" id="exampleFormControlTextarea1" rows="3">{{ $data->description ?? '' }}</textarea>
                            </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label for="">Default Image</label> <br>
                                    <img style="width: 198px; border: double;"
                                        src="{{ asset(isset($data->image)) ?? null }}" alt="">
                                </div>
                            </div>

                            <br>

                            <div class="row">
                                <div class="col">
                                    <label for="">More Images</label> <br>
                                    @if (isset($data->ProductImages))
                                        @forelse ($data->ProductImages as $images)
                                            <img style="width: 146px; border: double;" src="{{ asset($images->image) }}"
                                                alt="">
                                        @empty
                                            No Data
                                        @endforelse
                                    @endif

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>
    </div>

@stop
@section('footer_scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@stop


<style>
    .form .row {
    margin-top: 10px;
}
label[for="colorInput"] {
    display: flex;
    gap: 5px;
    align-items: center;
}
@media only screen and (max-width: 767px){
    .form .col {
    width: 100%;
    flex-basis: 100%;
    margin-top: 10px;
}
}


</style>