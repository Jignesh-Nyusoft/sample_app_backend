
<style>

#imgOut {
    display: none; /* Start hidden */
    /* max-width: 100%; */
    height: auto;
}
</style>
<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
  <div class="menu_section">
    <h3>General</h3>
    <ul class="nav side-menu">
      <li><a href="{{ url('/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
      <li><a><i class="fa fa-users"></i> Users <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
          <li><a href="{{ route('admin.users.create') }}">Create New User</a></li> 
          <li><a href="{{ route('admin.users') }}">All User List</a></li>
          
        </ul>
      </li>
      <li><a><i class="fa fa-file"></i> Product Management <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
          {{-- <li><a href="{{ route('product.create') }}">Create New User</a></li>  --}}
          <li><a href="{{ url('product-index-view') }}">All Products List</a></li>
          {{-- <li><a href="{{ url('product-approve-index') }}">Product Approve Request</a></li> --}}
        </ul>
      </li>

      <li>
        <a><i class="fa fa-file" style="font-size:24px"></i> Coupon Management <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
          <li class="{{ Request::is('admin/pages','admin/coupons/edit/*') ? 'active' : '' }}">
            </li>
            <li>
            <a href="{{ route('coupons.index') }}">All Coupon</a>
          </li>
          <li >
            <a href="{{ route('coupons.create') }}">Create Coupon</a>
          </li>
          </ul>
      </li>

      <li>
        <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Product Attributes <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
          <li class="">
          </li>
          <li>
          <li>
          <a>
            <i class="fa fa-file-code-o" style="font-size:21px"></i> Category <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
              <li class="{{ Request::is('admin/pages','admin/categories/edit/*') ? 'active' : '' }}">
              </li>
              <li>
              <a href="{{ route('categories.index') }}">All Category</a>
              </li>
              <li>
              {{-- <a href="{{ route('categories.create') }}">Create Category</a> --}}
        
        </li>
        </ul>
        </li>        
        </li>
        
          <li>
            <li>
              <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Color <span class="fa fa-chevron-down"></span></a>
              <ul class="nav child_menu">
                <li class="{{ Request::is('admin/pages','admin/color/edit/*') ? 'active' : '' }}">
                  
                </li>
      
                <li>
                  <a href="{{ route('color.index') }}">All Colors</a>
                </li>
                <li >
                  <a href="{{ route('color.create') }}">Create Color</a>
                </li>
              </ul>
            </li>
        </li>

        <li>
          <li>
            <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Size <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li class="{{ Request::is('admin/pages','admin/size/edit/*') ? 'active' : '' }}">
              </li>
              <li>
              <a href="{{ route('size.index') }}">All sizes</a>
              </li>
              <li>
                <a href="{{ route('size.create') }}">Create Size</a>
              </li>
            </ul>
          </li>

      </li>

         <li>
         <li>
         <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Condition <span class="fa fa-chevron-down"></span></a>
         <ul class="nav child_menu">
            <li class="{{ Request::is('admin/pages','admin/condition/edit/*') ? 'active' : '' }}">
            </li>
            <li>
            <a href="{{ route('condition.index') }}">All Conditions</a>
            </li>
            <li>
              <a href="{{ route('condition.create') }}">Create Condition</a>
        </li>
        </ul>
        </li>
    </li>


      <li>
        <li>
          <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Material <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
            <li class="{{ Request::is('admin/pages','admin/material/edit/*') ? 'active' : '' }}">              
            </li>
            <li>
            <li>
              <a href="{{ route('material.index') }}">All Material</a>
            </li>
            <li >
              <a href="{{ route('material.create') }}">Create Material</a>
            </li>
          </ul>
        </li>
    </li>


        <li>
        <li>
        <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Suitable <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
          <li class="{{ Request::is('admin/pages','admin/suitable/edit/*') ? 'active' : '' }}">
            {{-- <a href="#">All Pages</a> --}}
            </li>
            <li>
            <a href="{{ route('suitable.index') }}">All Suitable</a>
          </li>
          <li >
            <a href="{{ route('suitable.create') }}">Create Suitable</a>
          </li>
        </ul>
      </li>
  </li>

  <li>
    <li>
    <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Brand <span class="fa fa-chevron-down"></span></a>
      <ul class="nav child_menu">
      <li class="{{ Request::is('admin/pages','admin/brands/edit/*') ? 'active' : '' }}">
        </li>
        <li>
        <a href="{{ route('brands.index') }}">All Brand</a>
      </li>
      <li >
        <a href="{{ route('brands.create') }}">Create brand</a>
    </li>
    </ul>
    </li>
    </li>

    <li>
      <li>
      <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Banner <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
        <li class="{{ Request::is('admin/pages','admin/banners/edit/*') ? 'active' : '' }}">
          </li>
          <li>
          <a href="{{ route('banners.index') }}">All Banner</a>
        </li>
        <li >
          <a href="{{ route('banners.create') }}">Create Banner</a>
      </li>
      </ul>
      </li>
      </li>
      </ul>
      </li>


      <li>
        <a><i class="fa fa-cart-plus" style="font-size:21px"></i> Order Management <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
          <li class="{{ Request::is('admin/pages','admin/pages/edit/*') ? 'active' : '' }}">
            
          </li>
          <li>
          <a href="{{ url('order-index-view/All') }}">All Orders</a>
          </li>
          <li>
            <a href="{{ url('order-index-view/recent') }}">Recent Orders</a>
          </li>
          
          <li>
            <a href="{{ url('order-index-view/pending') }}">Pending Orders</a>
          </li>
          <li>
            <a href="{{ url('order-index-view/processing') }}">Processing Orders</a>
          </li>

          <li>
            <a href="{{ url('order-index-view/shipped') }}">Shipped Orders</a>
          </li>

          <li>
            <a href="{{ url('order-index-view/delivered') }}">Delivered Orders</a>
          </li>

          <li>
            <a href="{{ route('review.index') }}">Review List</a>
          </li>
        </ul>
      </li>


      <li>
        <a><i class="fa fa-file-code-o" style="font-size:21px"></i> Courier Partners <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
        <li class="{{ Request::is('admin/pages','admin/pages/edit/*') ? 'active' : '' }}">
            
          </li>
          <li>
          <a href="{{ route('courier.index') }}">Courier Partner List</a>
          </li>

        </ul>
      </li>

            <li>
        <a><i class="fa fa-file-code-o" style="font-size:21px"></i> CMS Pages <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
        <li class="{{ Request::is('admin/pages','admin/pages/edit/*') ? 'active' : '' }}">
            
          </li>
          <li>
          <a href="{{ route('cms.index') }}">All Pages</a>
          </li>
          <li >
          <a href="{{ route('cms.create') }}">Create Cms</a>
          </li>
        </ul>
      </li>
      <li>
        <a><i class="fa fa-question" style="font-size:24px"></i> Contact Us <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
          <li class="{{ Request::is('admin/contact','admin/contact/edit/*') ? 'active' : '' }}"> <a href="{{ route('contact.index') }}">Contact Us List</a> </li>
        </ul>
      </li>
      
      <li>
        <a><i class="fa fa-question" style="font-size:24px"></i> FAQ's <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
          <li class="{{ Request::is('admin/faqs/create') ? 'active' : '' }}"> <a href="{{ route('admin.faqs.create') }}">Add FAQ's</a> </li>
          <li class="{{ Request::is('admin/faqs','admin/faqs/edit/*') ? 'active' : '' }}"> <a href="{{ route('admin.faqs') }}">All FAQ's</a> </li>
        </ul>
      </li>


      <li class="{{ Request::is('admin/site-settings') ? 'active' : '' }}">
        <a href="{{route('admin.site-settings')}}" title="Site Settings">
          <em class="fa fa-cogs"></em>
          <span>Site Settings</span>
        </a>
      </li>

    </ul>
  </div>
  <div class="menu_section">
    {{-- <h3>Live On</h3> --}}
    {{-- <ul class="nav side-menu">
        <li><a><i class="fa fa-bug"></i> Additional Pages <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
            <li><a href="e_commerce.html">E-commerce</a></li>
            <li><a href="projects.html">Projects</a></li>
            <li><a href="project_detail.html">Project Detail</a></li>
            <li><a href="contacts.html">Contacts</a></li>
            <li><a href="profile.html">Profile</a></li>
          </ul>
        </li>
        <li><a><i class="fa fa-windows"></i> Extras <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
            <li><a href="page_403.html">403 Error</a></li>
            <li><a href="page_404.html">404 Error</a></li>
            <li><a href="page_500.html">500 Error</a></li>
            <li><a href="plain_page.html">Plain Page</a></li>
            <li><a href="login.html">Login Page</a></li>
            <li><a href="pricing_tables.html">Pricing Tables</a></li>
          </ul>
        </li>
        <li><a><i class="fa fa-sitemap"></i> Multilevel Menu <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
            <li><a href="#level1_1">Level One</a>
              <li><a>Level One<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <li class="sub_menu"><a href="level2.html">Level Two</a>
                  </li>
                  <li><a href="#level2_1">Level Two</a>
                  </li>
                  <li><a href="#level2_2">Level Two</a>
                  </li>
                </ul>
              </li>
              <li><a href="#level1_2">Level One</a>
              </li>
            </ul>
          </li>                  
        <li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>
      </ul> --}}
    </div>

  </div>
  <!-- /sidebar menu -->
  <!-- /menu footer buttons -->
  {{-- <div class="sidebar-footer hidden-small">
    <a href="{{ route('admin.site-settings') }}" data-toggle="tooltip" data-placement="top" title="Settings">
      <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
      <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="Lock">
      <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
      <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
    </a>
  </div> --}}
  <!-- /menu footer buttons -->
</div>
</div>

<!-- top navigation -->
<div class="top_nav">
  <div class="nav_menu">
    <div class="nav toggle">
      <a id="menu_toggle"><i class="fa fa-bars"></i></a>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Profile</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body row">
            @if($errors->any())
    {!! implode('', $errors->all('<div>:message</div>')) !!}
@endif
            <form action="{{route('ProfileUpdate')}}" method="Post" enctype='multipart/form-data'>
              @csrf
              <div class="form-group ">
                <label for="recipient-name" class="col-form-label">First Name:</label>
                <input name="first_name" type="text" value="{{auth()->user()->first_name}}" class="form-control" id="recipient-name">
              </div>
              <div class="form-group">
                <label for="recipient-name" class="col-form-label">Last Name:</label>
                <input name="last_name" type="text" value="{{auth()->user()->last_name}}" class="form-control" id="recipient-name">
              </div>
              <div class="form-group row">
                <div class="col-2">
                <label for="recipient-name" class="col-form-label">Code:</label>
                <input name="country_code" type="text" value="{{auth()->user()->country_code}}" class="form-control" id="recipient-name">
              </div>
              <div class="col-10">
                <label for="recipient-name" class="col-form-label">Mobile:</label>
                <input name="mobile" type="text" value="{{auth()->user()->mobile}}" class="form-control" id="recipient-name">
              </div>
              </div>
              <div class="form-group">
                <label for="recipient-name" class="col-form-label">Email:</label>
                <input name="email" type="text" value="{{auth()->user()->email}}" class="form-control" id="recipient-name">
              </div>
              <div class="form-group">
                <label for="message-text" class="col-form-label">Bio:</label>
                <textarea name="bio" class="form-control" id="message-text">{{auth()->user()->bio}}</textarea>
              </div>

              
              <div class="item form-group">
                <label class="col-form-label col-md-3 col-sm-3 label-align">Profile Image<span class="red">*</span></label>
                <div class="col-md-6 col-sm-6">
                  <input name="image" type="file" class="custom-file-input" id="imgInp">
                  @if($errors->has('image'))
                    <div class="error" style="color: red">{{ $errors->first('image') }}</div>
                  @endif
                <label class="custom-file-label" for="imgInp">Choose file...</label>
                </div>
              <div class="item form-group"> 
              <div class="col-md-6 col-sm-6" style="height: 27px;">
                      <img style="border-radius: 57px; height: 54px; width: 54px;" id="imgOut" src="{{url(auth()->user()->profile_image ?? '')}}" alt="" />
              </div>
              </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
      </form>
      </div>
    </div>

    <nav class="nav navbar-nav">
      <ul class=" navbar-right">
        <li class="nav-item dropdown open" style="padding-left: 15px;">
          <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset(auth()->user()->profile_image ?? 'admin_assets/production/images/img.jpg')}}" alt="">
          <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
            <a type="button" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo" class="dropdown-item"  href="javascript:;"> Profile</a>
            <a class="dropdown-item"  href="{{route('admin.logout')}}">logout</a>
        </div>
        </li>
      </ul>
    </nav>
  </div>
</div>
<!-- /top navigation -->
</div>

<script>
  const fileIn = document.getElementById('imgInp');
  const fileOut = document.getElementById('imgOut'); 
  
  const readUrl = (event) => {
    if (event.target.files && event.target.files[0]) {
      let reader = new FileReader();
      reader.onload = (e) => {
        fileOut.src = e.target.result;
        fileOut.style.display = 'block'; 
      };
      reader.readAsDataURL(event.target.files[0]);
    } else {
      if (!fileOut.src || fileOut.src.includes('data:image')) {
        fileOut.src = '{{ url(auth()->user()->profile_image ?? '') }}'; 
      }
      fileOut.style.display = 'block'; 
    }
  };
  
  window.addEventListener('load', () => {
    if (fileOut.src) {
      fileOut.style.display = 'block'; 
    }
  });
  
  fileIn.addEventListener('change', readUrl);
  </script>