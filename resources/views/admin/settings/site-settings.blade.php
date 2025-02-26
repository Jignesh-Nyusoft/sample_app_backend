@extends('admin.layouts.default')
@section('content')

<div class="right_col" role="main">
   <div class="x_panel">
        <font size="6">Site Settings</font>
   </div>

   <div class="">
      <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
         
          <div class="x_content">

            <ul class="nav nav-tabs bar_tabs" style="font-size:1.2vw" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">General Settings</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Payment Settings</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Other Settings</a>
              </li>

              <li class="nav-item">
               <a class="nav-link" id="env-tab" data-toggle="tab" href="#env" role="tab" aria-controls="env" aria-selected="false">SMTP Settings</a>
             </li>
             <li class="nav-item">
               <a class="nav-link" id="contact-tab" data-toggle="tab" href="#sms" role="tab" aria-controls="contact" aria-selected="false">Sms Settings</a>
             </li>

             <li class="nav-item">
               <a class="nav-link" id="contact-tab" data-toggle="tab" href="#uber" role="tab" aria-controls="contact" aria-selected="false">Uber Connect Settings</a>
             </li>
            </ul>

            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
               <form id="Site-setting-form" class="form-horizontal" method="post" action="{{route('admin.site-settings.store')}}" enctype='multipart/form-data'>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                  <fieldset>
                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">

                              <div class="x_title">
                                 <h2>Site Settings</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>

                              <div class="x_content">
                                 <br/>
                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Site Name <span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6  ">
                                          <input class="form-control" type="text" value="{{ App\Helpers\Helper::get_settings('site_name')}}" placeholder="Site Name" name="site_name">
                                       </div>
                                    </div>

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Admin Email <span class="red">*</span> </label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('admin_email')}}" placeholder="Admin Email" name="admin_email">
                                       </div>
                                    </div>

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Admin Mobile No.</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('admin_mobile_no')}}" placeholder="Admin Mobile No" name="admin_mobile_no">
                                       </div>
                                    </div>

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Site Email</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('site_email')}}" placeholder="Site Email" name="site_email">
                                       </div>
                                    </div>
            
                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Customer Care Mobile No.</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('customer_care_mobile_no')}}" placeholder="Customer Care Mobile No" name="customer_care_mobile_no">
                                       </div>
                                    </div>

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Customer Care Email</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('customer_care_email')}}" placeholder="Customer Care Email" name="customer_care_email">
                                       </div>
                                    </div>

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align ">Copyright Text</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('copyright_text')}}" placeholder="Copyright Text" name="copyright_text">
                                       </div>
                                    </div>

                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">

                        <div class="col-md-6 ">
                           <div class="x_panel">

                              <div class="x_title">
                                 <h2>Social Links</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>

                              <div class="x_content">
                                 <br />
                                 {{-- <form class="form-label-left input_mask"> --}}

                                    <div class="col-md-12 col-sm-12  form-group has-feedback">
                                       <input type="text" class="form-control has-feedback-left" id="inputSuccess2" value="{{App\Helpers\Helper::get_settings('facebook_url')}}"  name="facebook_url" placeholder="Facebook URL">
                                       <span class="fa fa-facebook-official form-control-feedback left" aria-hidden="true"></span>
                                    </div>

                                    <div class="col-md-12 col-sm-12  form-group has-feedback">
                                       <input type="text" class="form-control has-feedback-left" id="inputSuccess3" value="{{App\Helpers\Helper::get_settings('twitter_url')}}" name="twitter_url" placeholder="Twitter URL">
                                       <span class="fa fa-twitter-square form-control-feedback left" aria-hidden="true"></span>
                                    </div>

                                    <div class="col-md-12 col-sm-12  form-group has-feedback">
                                       <input type="text" class="form-control has-feedback-left" id="inputSuccess2" value="{{App\Helpers\Helper::get_settings('instagram_url')}}" name="instagram_url" placeholder="Instagram URL">
                                       <span class="fa fa-instagram form-control-feedback left" aria-hidden="true"></span>
                                    </div>

                                    <div class="col-md-12 col-sm-12  form-group has-feedback">
                                       <input type="text" class="form-control has-feedback-left" id="inputSuccess3" value="{{App\Helpers\Helper::get_settings('pinterest_url')}}" name="pinterest_url" placeholder="Pinterest URL">
                                       <span class="fa fa-pinterest-square form-control-feedback left" aria-hidden="true"></span>
                                    </div>

                                    <div class="col-md-12 col-sm-12  form-group has-feedback">
                                       <input type="text" class="form-control has-feedback-left" id="inputSuccess2" value="{{App\Helpers\Helper::get_settings('linkedin_url')}}" name="linkedin_url" placeholder="LinkedIn URL">
                                       <span class="fa fa-linkedin-square form-control-feedback left" aria-hidden="true"></span>
                                    </div>

                                    <div class="col-md-12 col-sm-12  form-group has-feedback">
                                       <input type="text" class="form-control has-feedback-left" id="inputSuccess3" value="{{App\Helpers\Helper::get_settings('youtube_url')}}" name="youtube_url" placeholder="Youtube URL">
                                       <span class="fa fa-youtube-play form-control-feedback left" aria-hidden="true"></span>
                                    </div>

                                 {{-- </form> --}}
                              </div>
                           </div>

                        </div>

                        <div class="col-md-6 ">
                           <div class="x_panel">
                              <div class="x_title">
                                 <h2>Site Images</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>
                              <div class="x_content">
                                 <br />
                                 {{-- <form class="form-horizontal form-label-left"> --}}

                                    <div class="form-group row">
                                       <label class="control-label col-md-3 col-sm-3">Logo</label>
                                       <div class="col-md-9 col-sm-9 ">
            
                                          @if(!empty(App\Helpers\Helper::get_settings('logo')))
                                          <img src="{{ url(App\Helpers\Helper::get_settings('logo')) }}" class="img-fluid img-user" alt="LOGO" title="Company Logo">

                                          @else
                                             <img src="{{ asset('admin_assets/admin/img/default_image.png')}}" height="60px" >
                                          @endif
            
                                          <input class="form-control" type="file" name="logo" >
                                       </div>
                                    </div>

                                    <div class="form-group row">
                                       <label class="control-label col-md-3 col-sm-3">Favicon </label>
                                       <div class="col-md-9 col-sm-9 ">
                                          @if(!empty(App\Helpers\Helper::get_settings('favicon')))
                                             <img src="{{ url(App\Helpers\Helper::get_settings('favicon')) }}" height="100px" class="img-fluid img-user" alt="Favicon">
                                          @else
                                             <img src="{{ asset('admin_assets/admin/img/default_image.png')}}" height="60px" >
                                          @endif
            
                                          <input class="form-control" type="file" name="favicon" >
                                       </div>
                                    </div>
            
                                    <div class="form-group row">
                                       <label class="control-label col-md-3 col-sm-3">Footer Logo </label>
                                       <div class="col-md-9 col-sm-9 ">
                                          @if(!empty(App\Helpers\Helper::get_settings('footer_logo')))
                                             <img src="{{ url(App\Helpers\Helper::get_settings('footer_logo')) }}" height="100px" class="img-fluid img-user" alt="Footer Logo">
                                          @else
                                             <img src="{{ asset('admin_assets/admin/img/default_image.png')}}" height="60px" >
                                          @endif
            
                                          <input class="form-control" type="file" name="footer_logo" >
                                       </div>
                                    </div>

                                 {{-- </form> --}}
                              </div>
                           </div>
                        </div>
                     
                     </div>

                     <div class="row">
                        <div class="col-md-12 col-sm-12 ">
                           <div class="x_panel">

                              <div class="x_title">
                                 <h2>SEO Settings</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>

                              <div class="x_content">
                                 <br />
                                 {{-- <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left"> --}}

                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align  ">Meta Title</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('meta_title')}}" placeholder="Meta Title" name="meta_title">
                                       </div>
                                    </div>
            
                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align  ">Meta Description</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <textarea class="form-control" rows="5" placeholder="Meta Description Max 255 characters..." name="meta_description">{{App\Helpers\Helper::get_settings('meta_description')}}</textarea>
                                       </div>
                                    </div>
            
                                    <div class="item form-group">
                                       <label class="col-form-label col-md-3 col-sm-3 label-align  ">Meta Keywords</label>
                                       <div class="col-md-6 col-sm-6 ">
                                          <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('meta_keywords')}}" placeholder="Meta Keywords" name="meta_keywords">
                                       </div>
                                    </div>

                                 {{-- </form> --}}
                              </div>
                           </div>
                        </div>
                     </div>
                  </fieldset>                    

                     <div class="ln_solid"></div>

                     <fieldset>
                        <div class="form-group row">
                              <div class="col-md-12 col-sm-12">
                                 <a href="{{route('admin.dashboard')}}" class="btn btn-warning">Discard</a>
                                 <button class="btn btn-success submit" type="submit">Save</button>
                              </div>
                        </div>
                     </fieldset>

               </form>
              </div>

            {{------------------------------------------- Section - 1 END -----------------------------------------------}}
            {{------------------------------------------- Section - 2 Start -----------------------------------------------}}

              <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
               <div class="row">

                  <form id="payment-setting-form" class="form-horizontal ajax_form" method="post" action="{{route('admin.site-settings.store.payment')}}">
                     <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                     <fieldset>

                        <div class="col-md-6 ">
                           <div class="x_panel">

                              <div class="x_title">
                                 <h2>Stripe details</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>

                              <div class="x_content">
                                 <br />
                                 {{-- <form class="form-label-left input_mask"> --}}
                                    <fieldset>

                                       <div class="form-group row">
                                          <label class="col-form-label col-md-3 col-sm-3 ">Publishable Key <span class="red">(Stored Encrypted)</span></label>
                                          <div class="col-md-9 col-sm-9 ">
                                             <input type="text" class="form-control" value="{{App\Helpers\Helper::get_settings('publishable_key','true')}}" name="publishable_key" placeholder="Publishable Key ">
                                          </div>
                                       </div>

                                       <div class="form-group row">
                                          <label class="col-form-label col-md-3 col-sm-3 ">Secret Key  &nbsp;&nbsp; <span class="red">(Stored Encrypted)</span></label>
                                          <div class="col-md-9 col-sm-9 ">
                                             <input type="text" class="form-control" value="{{App\Helpers\Helper::get_settings('secret_key','true')}}" name="secret_key" placeholder="Secret Key ">
                                          </div>
                                       </div>

                                    </fieldset>

                                 {{-- </form> --}}
                              </div>
                           </div>

                        </div>

                        <div class="col-md-6 ">
                           <div class="x_panel">
                              <div class="x_title">
                                 <h2>Commission & Tax details</h2>
                                 <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                                 </ul>
                                 <div class="clearfix"></div>
                              </div>
                              <div class="x_content">
                                 <br />
                                 {{-- <form class="form-horizontal form-label-left"> --}}

                                    {{-- <div class="form-group row">
                                       <label class="control-label col-md-3 col-sm-3 ">Paypal Mode</label>
                                       <div class="col-md-9 col-sm-9 ">
                                          <select class="form-control" name="paypal_mode">
                                             <option value="">Select</option>
                                             <option {{(App\Helpers\Helper::get_settings('paypal_mode') == 'sandbox') ? 'selected' : ''}} value="sandbox">Sandbox</option>
                                             <option {{(App\Helpers\Helper::get_settings('paypal_mode') == 'live') ? 'selected' : ''}} value="live">Live</option>
                                          </select>
                                       </div>
                                    </div> --}}

                                    <div class="form-group row">
                                       <label class="col-form-label col-md-3 col-sm-3 ">Service fee (for buyer) (%) before 100$<span class="red"></span></label>
                                       <div class="col-md-9 col-sm-9 ">
                                          <input type="text" class="form-control" value="{{App\Helpers\Helper::get_settings('seller_commission_before_total_amount')}}" name="seller_commission_after_total_amount" placeholder="Paypal Client ID">
                                       </div>
                                    </div>

                                    <div class="form-group row">
                                       <label class="col-form-label col-md-3 col-sm-3 ">Service fee (for buyer) (%) After 100$<span class="red"></span></label>
                                       <div class="col-md-9 col-sm-9 ">
                                          <input type="text" class="form-control" value="{{App\Helpers\Helper::get_settings('seller_commission_after_total_amount')}}" name="seller_commission_after_total_amount" placeholder="Paypal Secret ">
                                       </div>
                                    </div>

                                    

                                    <div class="form-group row">
                                       <label class="col-form-label col-md-3 col-sm-3 ">Service free(for customer) (%)<span class="red"></span></label>
                                       <div class="col-md-9 col-sm-9 ">
                                          <input type="text" class="form-control" value="{{App\Helpers\Helper::get_settings('service_fee_commission')}}" name="seller_commission_after_total_amount" placeholder="Paypal Secret ">
                                       </div>
                                    </div>

                                    <div class="form-group row">
                                       <label class="col-form-label col-md-3 col-sm-3 ">Sales Tax (%)<span class="red"></span></label>
                                       <div class="col-md-9 col-sm-9 ">
                                          <input type="text" class="form-control" value="{{App\Helpers\Helper::get_settings('sales_tax')}}" name="seller_commission_after_total_amount" placeholder="Paypal Secret ">
                                       </div>
                                    </div>

                                 {{-- </form> --}}
                              </div>
                           </div>
                        </div>

                     </fieldset>

                     <div class="ln_solid"></div>

                     <fieldset>
                        <div class="form-group row">
                              <div class="col-md-12 col-sm-12">
                                 <a href="{{route('admin.dashboard')}}" class="btn btn-warning">Discard</a>
                                 <button class="btn btn-success submit" type="submit">Save</button>
                              </div>
                        </div>
                     </fieldset>

                  </form>

               </div>
              </div>


            {{------------------------------------------- Section - 2 END -----------------------------------------------}}
            {{------------------------------------------- Section - 3 Start -----------------------------------------------}}
              <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
               <div class="row">
                  <div class="col-md-12 col-sm-12 ">
                     <div class="x_panel">

                        <div class="x_title">
                           <h2>Google Key Settings</h2>
                           <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                           </ul>
                           <div class="clearfix"></div>
                        </div>

                        <form id="other-setting-form" class="form-horizontal ajax_form" method="post" action="{{route('admin.site-settings.store.other')}}">
                           <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                           <div class="x_content">
                              <br />
                              {{-- <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left"> --}}
                              <fieldset>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Google Map Key <span class="red">(Stored Encrypted)</span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('google_map_key','true')}}" placeholder="Google Map Key" name="google_map_key">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Google Captcha Key <span class="red">(Stored Encrypted)</span> </label>
                                    <div class="col-md-6 col-sm-6 ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('google_captcha_key','true')}}"  name="google_captcha_key" placeholder="Google Captcha Key">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">Google Captcha Secret <span class="red">(Stored Encrypted)</span></label>
                                    <div class="col-md-6 col-sm-6 ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('google_captcha_secret','true')}}" placeholder="Google Captcha Secret" name="google_captcha_secret">
                                    </div>
                                 </div>

                              </fieldset>

                              {{-- </form> --}}
                              <div class="ln_solid"></div>

                                 <fieldset>
                                    <div class="form-group row">
                                          <div class="col-md-12 col-sm-12">
                                             <a href="{{route('admin.dashboard')}}" class="btn btn-warning">Discard</a>
                                             <button class="btn btn-success submit" type="submit">Save</button>
                                          </div>
                                    </div>
                                 </fieldset>

                           </div>
                        </form>
                     </div>
                  </div>
               </div>
              </div>


            {{------------------------------------------- Section - 3 END -----------------------------------------------}}

            {{------------------------------------------- Section - 4 Start ---------------------------------------------}}

            {{------------------------------------------- Section - 2 END SMS -----------------------------------------------}}
            {{------------------------------------------- Section - 3 Start SMS -----------------------------------------------}}
            <div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="contact-tab">
               <div class="row">
                  <div class="col-md-12 col-sm-12 ">
                     <div class="x_panel">

                        <div class="x_title">
                           <h2>SMS Key Settings</h2>
                           <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                           </ul>
                           <div class="clearfix"></div>
                        </div>

                        <form id="other-setting-form" class="form-horizontal ajax_form" method="post" action="{{route('admin.site-settings.store.other')}}">
                           <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                           <div class="x_content">
                              <br />
                              {{-- <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left"> --}}
                              <fieldset>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">TWILIO SID<span class="red"></span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('TWILIO_SID')}}" placeholder="TWILIO SID" name="TWILIO_SID">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">TWILIO AUTH TOKEN <span class="red"></span> </label>
                                    <div class="col-md-6 col-sm-6 ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('TWILIO_AUTH_TOKEN')}}"  name="TWILIO_AUTH_TOKEN" placeholder="TWILIO AUTH TOKEN">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">TWILIO PHONE NUMBER <span class="red"></span></label>
                                    <div class="col-md-6 col-sm-6 ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('TWILIO_PHONE_NUMBER')}}" placeholder="TWILIO PHONE NUMBER" name="TWILIO_PHONE_NUMBER">
                                    </div>
                                 </div>

                              </fieldset>

                              {{-- </form> --}}
                              <div class="ln_solid"></div>

                                 <fieldset>
                                    <div class="form-group row">
                                          <div class="col-md-12 col-sm-12">
                                             <a href="{{route('admin.dashboard')}}" class="btn btn-warning">Discard</a>
                                             <button class="btn btn-success submit" type="submit">Save</button>
                                          </div>
                                    </div>
                                 </fieldset>

                           </div>
                        </form>
                     </div>
                  </div>
               </div>
              </div>


            {{------------------------------------------- Section - 3 END -----------------------------------------------}}

            {{------------------------------------------- Section - 4 Start ---------------------------------------------}}


            {{------------------------------------------- Section - 2 END SMS -----------------------------------------------}}
            {{------------------------------------------- Section - 3 Start Uber -----------------------------------------------}}
            <div class="tab-pane fade" id="uber" role="tabpanel" aria-labelledby="contact-tab">
               <div class="row">
                  <div class="col-md-12 col-sm-12 ">
                     <div class="x_panel">

                        <div class="x_title">
                           <h2>Uber Connect Settings</h2>
                           <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                           <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                           </ul>
                           <div class="clearfix"></div>
                        </div>

                        <form id="other-setting-form" class="form-horizontal ajax_form" method="post" action="{{route('admin.site-settings.store.other')}}">
                           <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                           <div class="x_content">
                              <br/>
                           <fieldset>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">UBER CLIENT ID<span class="red"></span> </label>
                                    <div class="col-md-6 col-sm-6  ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('UBER_CLIENT_ID')}}" placeholder="UBER CLIENT ID" name="UBER_CLIENT_ID">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">UBER CLIENT SECRET <span class="red"></span> </label>
                                    <div class="col-md-6 col-sm-6 ">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('UBER_CLIENT_SECRET')}}"  name="UBER_CLIENT_SECRET" placeholder="UBER CLIENT SECRET">
                                 </div>
                              
                              </div>
                              </fieldset>

                              {{-- </form> --}}
                              <div class="ln_solid"></div>

                                 <fieldset>
                                    <div class="form-group row">
                                          <div class="col-md-12 col-sm-12">
                                             <a href="{{route('admin.dashboard')}}" class="btn btn-warning">Discard</a>
                                             <button class="btn btn-success submit" type="submit">Save</button>
                                          </div>
                                    </div>
                                 </fieldset>

                           </div>
                        </form>
                     </div>
                  </div>
               </div>
              </div>
            {{------------------------------------------- Section - 3 END -----------------------------------------------}}
            {{------------------------------------------- Section - 4 Start ---------------------------------------------}}
           
            <div class="tab-pane fade" id="env" role="tabpanel" aria-labelledby="env-tab">
               <div class="row">
                  <div class="col-md-12 col-sm-12 ">
                     <div class="x_panel">

                        <div class="x_title">
                           <h2>Email SMTP Settings</h2>
                           <ul class="nav navbar-right panel_toolbox" style="min-width: 0%">
                              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                           </ul>
                           <div class="clearfix"></div>
                        </div>

                        <form id="other-setting-form" class="form-horizontal ajax_form" method="post" action="{{route('admin.site-settings.store.env')}}">
                           <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                           <div class="x_content">
                              <br />
                              <fieldset>

                                 {{-- <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align ">MAIL MAILER</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{ get_settings('mail_mailer') }}" placeholder="MAIL MAILER" name="mail_mailer">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">MAIL DRIVER</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{ get_settings('mail_driver') }}" placeholder="MAIL DRIVER" name="mail_driver">
                                    </div>
                                 </div> --}}

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">SMTP HOST</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{App\Helpers\Helper::get_settings('smtp_host') }}" placeholder="SMTP HOST" name="smtp_host">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">SMTP PORT</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{ App\Helpers\Helper::get_settings('smtp_port') }}" placeholder="SMTP PORT" name="smtp_port">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">SMTP USERNAME</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{ App\Helpers\Helper::get_settings('smtp_username') }}" placeholder="SMTP USERNAME" name="smtp_username">
                                    </div>
                                 </div> 

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">SMTP PASSWORD <span class="red"><br> (<b>Stored Encrypted</b>)</span></label>
                                      {{-- <label>(<b>Encrypted</b>)</label> --}}
                                    <div class="col-md-6 col-sm-6"> 
                                       <input class="form-control" type="text" value="{{ App\Helpers\Helper::get_settings('smtp_password','true') }}" placeholder="SMTP PASSWORD" name="smtp_password"> 
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">MAIL ENCRYPTIO</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{ App\Helpers\Helper::get_settings('mail_encryption') }}" placeholder="MAIL ENCRYPTION" name="mail_encryption">
                                    </div>
                                 </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">SMTP FROM EMAIL</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{ App\Helpers\Helper::get_settings('smtp_from_email') }}" placeholder="SMTP FROM EMAIL" name="smtp_from_email">
                                    </div>
                                 </div>


                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align">SMTP FROM NAME</label>
                                    <div class="col-md-6 col-sm-6">
                                       <input class="form-control" type="text" value="{{ App\Helpers\Helper::get_settings('smtp_from_name') }}" placeholder="SMTP FROM NAME" name="smtp_from_name">
                                    </div>
                                 </div>

                              </fieldset>

                              <div class="ln_solid"></div>

                                 <fieldset>
                                    <div class="form-group row">
                                          <div class="col-md-12 col-sm-12">
                                             <a href="{{route('admin.dashboard')}}" class="btn btn-warning">Discard</a>
                                             <button class="btn btn-success submit" type="submit">Save</button>
                                          </div>
                                    </div>
                                 </fieldset>

                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>

            {{------------------------------------------- Section - 4 END -----------------------------------------------}}


            </div>
          </div>
        </div>
      </div>
   </div>

</div>

@stop
@section('footer_scripts')
@stop
