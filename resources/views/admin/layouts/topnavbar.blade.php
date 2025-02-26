<div class="container body">
  <div class="main_container admin_left_side">
    <div class="col-md-3 left_col">
      <div class="left_col scroll-view">
        <div class="navbar nav_title logo_area" style="border: 0;">
          <img class="logo_image" src="{{asset(App\Helpers\Helper::_get_settings('logo')) }}" alt="">
          <img class="logo_icon_image" src="{{ asset(App\Helpers\Helper::_get_settings('logoicon') ?? 'setting/logo-icon.png') }}" alt="">

        </div>
        <div class="clearfix"></div>
        <div class="profile clearfix">
          <div class="profile_pic">
            <img  src="{{ asset(auth()->user()->profile_image ?? 'admin_assets/production/images/img.jpg')}}" alt="..." class="img-circle profile_img">
          </div>
          <div class="profile_info">  
            <span>Welcome, {{auth()->user()->first_name}}</span>
            <h2></h2>
          </div>
        </div>
        <br />


        <style>
          .logo_image{
            width: 166px;
            margin-left: 21px;
          }

          .nav-sm  .logo_image{
            display: none;
          }
          .nav-md  img.logo_icon_image {
    display: none;
}

.nav-sm  img.logo_icon_image {
    display: block;
}
img.logo_icon_image {
    width: 40px;
    margin: 0 15px;
}
.left_col.scroll-view {
    padding-top: 20px;
}
.profile_pic img{
  max-width: 100px;
  max-height: 100px;
}

.admin_left_side{
  position: sticky;
  top: 0;
  z-index: 1;
}

.left_col.scroll-view {
    padding-top: 20px;
    overflow-y: auto;
    overflow-x: hidden;
    height: 100vh;
}

/* width */
.left_col.scroll-view::-webkit-scrollbar {
  width: 5px;
}

/* Track */
.left_col.scroll-view::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px rgb(240, 240, 240); 
  border-radius: 10px;
}
 
/* Handle */
.left_col.scroll-view::-webkit-scrollbar-thumb {
  background: rgb(90, 90, 90); 
  border-radius: 10px;
}


        </style>