<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mahloula Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset("admin/assets")}}/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{asset("admin/assets")}}/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
   <link rel="stylesheet" href="{{asset("admin/assets")}}/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="{{asset("admin/assets")}}/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="{{asset("admin/assets")}}/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="{{asset("admin/assets")}}/vendors/owl-carousel-2/owl.theme.default.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{asset("admin/assets/css/style.css")}}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{asset("favicon.ico")}}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5+5hb7V3Tkrm/34r9EJl6R+oTOeQuDYOnNaw+Gpm" crossorigin="anonymous">

    @livewireStyles
  </head>
<body>

    <body>
        <div class="container-scroller">
          <div class="row p-0 m-0 proBanner" id="proBanner">
            <div class="col-md-12 p-0 m-0">
              <div class="card-body card-body-padding d-flex align-items-center justify-content-between">
                <div class="ps-lg-1">
                  <div class="d-flex align-items-center justify-content-between">
                    <a href="https://www.bootstrapdash.com/product/corona-free/?utm_source=organic&utm_medium=banner&utm_campaign=buynow_demo" target="_blank" class="btn me-2 buy-now-btn border-0">Get Pro</a>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <button id="bannerClose" class="btn border-0 p-0">
                    <i class="mdi mdi-close text-white me-0"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>


<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top w-100 ">
      <a class="sidebar-brand " href="{{ url("admin/redirect") }}"><img src="{{ asset('admin/assets/images/Logo.png') }}"
              alt="logo" /></a>
      <a class="sidebar-brand brand-logo-mini"href="{{ url("admin/redirect") }}"><img
              src="{{ asset('admin/assets/images/Logo.png') }}" alt="logo" /></a>
  </div>
  <ul class="nav">
      <li class="nav-item profile">
          <div class="profile-desc">
              <div class="profile-pic">
                  <div class="count-indicator">
                      <img class="img-xs rounded-circle " src="{{ asset('admin/assets/images/faces/face15.jpg') }}"
                          alt="">
                      <span class="count bg-success"></span>
                  </div>
                  <div class="profile-name">
                      {{-- @if ($user)
                          <h5 class="mb-0 font-weight-normal">{{ $user->name }}</h5>
                      @else
                          <h5 class="mb-0 font-weight-normal">Guest</h5>
                      @endif --}}

                      <span>Gold Member</span>
                  </div>
              </div>
              <a href="#" id="profile-dropdown" data-bs-toggle="dropdown"><i
                      class="mdi mdi-dots-vertical"></i></a>
              <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list"
                  aria-labelledby="profile-dropdown">
                  <a href="#" class="dropdown-item preview-item">
                      <div class="preview-thumbnail">
                          <div class="preview-icon bg-dark rounded-circle">
                              <i class="mdi mdi-settings text-primary"></i>
                          </div>
                      </div>
                      <div class="preview-item-content">
                          <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                      </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="#" class="dropdown-item preview-item">
                      <div class="preview-thumbnail">
                          <div class="preview-icon bg-dark rounded-circle">
                              <i class="mdi mdi-onepassword  text-info"></i>
                          </div>
                      </div>
                      <div class="preview-item-content">
                          <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                      </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="#" class="dropdown-item preview-item">
                      <div class="preview-thumbnail">
                          <div class="preview-icon bg-dark rounded-circle">
                              <i class="mdi mdi-calendar-today text-success"></i>
                          </div>
                      </div>
                      <div class="preview-item-content">
                          <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                      </div>
                  </a>
              </div>
          </div>
      </li>
      <li class="nav-item nav-category">
          <span class="nav-link">Employees</span>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('admin.employee.waiting') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Waiting Employee</span>
          </a>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('admin.employee.update') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Updated Employee</span>
          </a>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('admin.employee.accept') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Accepted Employee</span>
          </a>
      </li>
      <hr>
      <li class="nav-item nav-category">
        <span class="nav-link">Users</span>
    </li>
    <li class="nav-item menu-items">
        <a class="nav-link" href="{{ route('admin.user.show') }}">
            <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
            </span>
            <span class="menu-title">All Users</span>
        </a>
    </li>
      <hr>
      <li class="nav-item nav-category">
        <span class="nav-link">Projects</span>
    </li>
    <li class="nav-item menu-items">
        <a class="nav-link" href="{{ route('admin.project.show') }}">
            <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
            </span>
            <span class="menu-title">All Projects</span>
        </a>
    </li>
    <hr>
      <li class="nav-item nav-category">
        <span class="nav-link">sections</span>
        </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('section.create') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">add section</span>
          </a>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{route('section.show')}}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">all sections </span>
          </a>
      </li>
      <hr>
      <li class="nav-item nav-category">
          <span class="nav-link">services</span>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('service.create') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">add service</span>
          </a>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('service.show') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">All services</span>
          </a>
      </li>
      <hr>
      <li class="nav-item nav-category">
          <span class="nav-link">sponsers</span>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('sponser.create') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">add sponser</span>
          </a>
      </li>
      <li class="nav-item menu-items">
          <a class="nav-link" href="{{ route('sponser.show') }}">
              <span class="menu-icon">
                  <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">All sponsers</span>
          </a>
      </li>


      <hr>


  </ul>
</nav>

<div class="container-fluid page-body-wrapper">

    <nav class="navbar p-0 fixed-top d-flex flex-row">
        <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
            <a class="navbar-brand brand-logo-mini" href="index.html"><img
                    src="{{ asset('admin/assets') }}/images/logo-mini.svg" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="mdi mdi-menu"></span>
            </button>

            <ul class="navbar-nav navbar-nav-right">
                {{-- <li class="nav-item dropdown d-none d-lg-block">
                    <a class="nav-link btn btn-success create-new-button" id="createbuttonDropdown"  aria-expanded="false" href="{{ route('admin.create') }}">+ Add new Admin</a>

                </li>
                <li class="nav-item nav-settings d-none d-lg-block">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-view-grid"></i>
                    </a>
                </li> --}}
                {{-- <li class="nav-item dropdown border-left">
                    <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-email"></i>
                        <span class="count bg-success"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                        aria-labelledby="messageDropdown">
                        <h6 class="p-3 mb-0">Messages</h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <img src="{{ asset('admin/assets') }}/images/faces/face4.jpg" alt="image"
                                    class="rounded-circle profile-pic">
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject ellipsis mb-1">Mark send you a message</p>
                                <p class="text-muted mb-0"> 1 Minutes ago </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <img src="{{ asset('admin/assets') }}/images/faces/face2.jpg" alt="image"
                                    class="rounded-circle profile-pic">
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject ellipsis mb-1">Cregh send you a message</p>
                                <p class="text-muted mb-0"> 15 Minutes ago </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <img src="{{ asset('admin/assets') }}/images/faces/face3.jpg" alt="image"
                                    class="rounded-circle profile-pic">
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject ellipsis mb-1">Profile picture updated</p>
                                <p class="text-muted mb-0"> 18 Minutes ago </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <p class="p-3 mb-0 text-center">4 new messages</p>
                    </div>
                </li> --}}
                {{-- <li class="nav-item dropdown border-left">
                    <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                        data-bs-toggle="dropdown">
                        <i class="mdi mdi-bell"></i>
                        <span class="count bg-danger"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                        aria-labelledby="notificationDropdown">
                        <h6 class="p-3 mb-0">Notifications</h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-dark rounded-circle">
                                    <i class="mdi mdi-calendar text-success"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">Event today</p>
                                <p class="text-muted ellipsis mb-0"> Just a reminder that you have an event today </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-dark rounded-circle">
                                    <i class="mdi mdi-settings text-danger"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">Settings</p>
                                <p class="text-muted ellipsis mb-0"> Update dashboard </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-dark rounded-circle">
                                    <i class="mdi mdi-link-variant text-warning"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">Launch Admin</p>
                                <p class="text-muted ellipsis mb-0"> New admin wow! </p>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <p class="p-3 mb-0 text-center">See all notifications</p>
                    </div>
                </li> --}}
                <li class="nav-item dropdown">
                    <a class="nav-link" id="profileDropdown" href="#" data-bs-toggle="dropdown">
                        <div class="navbar-profile">
                            <img class="img-xs rounded-circle" src="{{ asset('admin/assets') }}/images/faces/face15.jpg"
                                alt="">


                            <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                        aria-labelledby="profileDropdown">

                        <a class="dropdown-item preview-item">
                         
                            <div class="preview-item-content  text-center">
                                    
                                    <a class="preview-subject mb-1 btn btn-outline-danger text-center"
                                    href="{{route('logout')}}">Log out</a>
                            </div>
                            
                        </a>
                      
                    </div>
                </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                data-toggle="offcanvas">
                <span class="mdi mdi-format-line-spacing"></span>
            </button>
        </div>
    </nav>


    <div class="main-panel">
        <div class="content-wrapper">
         {{-- @include("body") --}}
         @yield("content")

        </div>



  <footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Mahloula 2024</span>
      <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a href="https://mahllola.online" target="_blank"></a> from mahllola.online</span>
    </div>
  </footer>
  <!-- partial -->
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- plugins:js -->
<script src="{{asset("admin/assets")}}/vendors/js/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{asset("admin/assets")}}/vendors/chart.js/Chart.min.js"></script>
<script src="{{asset("admin/assets")}}/vendors/progressbar.js/progressbar.min.js"></script>
<script src="{{asset("admin/assets")}}/vendors/jvectormap/jquery-jvectormap.min.js"></script>
<script src="{{asset("admin/assets")}}/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="{{asset("admin/assets")}}/vendors/owl-carousel-2/owl.carousel.min.js"></script>
<script src="{{asset("admin/assets")}}/js/jquery.cookie.js" type="text/javascript"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{asset("admin/assets")}}/js/off-canvas.js"></script>
<script src="{{asset("admin/assets")}}/js/hoverable-collapse.js"></script>
<script src="{{asset("admin/assets")}}/js/misc.js"></script>
<script src="{{asset("admin/assets")}}/js/settings.js"></script>
<script src="{{asset("admin/assets")}}/js/todolist.js"></script>
<!-- endinject -->
<!-- Custom js for this page -->
<script src="{{asset("admin/assets")}}/js/dashboard.js"></script>
@livewireScripts
<!-- End custom js for this page -->
</body>
</html>
