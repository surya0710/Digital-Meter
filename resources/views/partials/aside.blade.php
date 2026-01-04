<aside class="page-sidebar">
   <div class="left-arrow" id="left-arrow">
      <i data-feather="arrow-left"></i>
   </div>
   <div class="main-sidebar" id="main-sidebar">
      <ul class="sidebar-menu" id="simple-bar">
         <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i>
            <a class="sidebar-link" href="{{ route('dashboard') }}">
               <svg class="stroke-icon">
                  <use href="{{ asset('assets/css/iconly-sprite.svg#Home-dashboard') }}"></use>
               </svg>
               <h6>Dashboards</h6>
            </a>
         </li>
         <li class="sidebar-list">
            <a class="sidebar-link {{ request()->is('users*') ? 'active' : '' }}"
               href="javascript:void(0)">
               
               <svg class="stroke-icon">
                     <use href="{{ asset('assets/css/iconly-sprite.svg#Profile') }}"></use>
               </svg>

               <h6 class="f-w-600">Manage User</h6>
               <i class="iconly-Arrow-Right-2 icli"></i>
            </a>

            <ul class="sidebar-submenu" style="{{ strpos(request()->url(), 'users') !== false ? 'display:block' : 'display:none' }}">
               <li>
                     <a href="{{ route('users.list') }}">List</a>
               </li>
            </ul>
         </li>

         <li class="sidebar-list">
            <a class="sidebar-link {{ request()->is('devices*') ? 'active' : '' }}" href="javascript:void(0)">
               
               <svg class="stroke-icon">
                     <use href="{{ asset('assets/css/iconly-sprite.svg#Folder') }}"></use>
               </svg>

               <h6 class="f-w-600">Devices</h6>
               <i class="iconly-Arrow-Right-2 icli"></i>
            </a>

            <ul class="sidebar-submenu" style="{{ strpos(request()->url(), 'devices') !== false ? 'display:block' : 'display:none' }}">
               <li>
                     <a href="{{ route('devices.list') }}">List</a>
               </li>
            </ul>
         </li>
         
      </ul>
   </div>
   <div class="right-arrow" id="right-arrow">
      <i data-feather="arrow-right"></i>
   </div>
</aside>