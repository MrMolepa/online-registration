<nav class="navbar navbar-default navbar-fixed-top">
    <div class="brand">
        <a href="index.php">Examinations Council of Lesotho</a>
    </div>
    <div class="container-fluid">
        <div class="navbar-btn">
            <button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-arrow-left-circle"></i></button>
        </div>
        <div id="navbar-menu">
            <ul class="nav navbar-nav navbar-right">


                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="lnr lnr-question-circle"></i>
                        <span>Help</span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="help.php">Basic Use</a></li>
                        <li><a href="help.php">Working With Data</a></li>
                        <li><a href="help.php">Security</a></li>
                        <li><a href="help.php">Troubleshooting</a></li>
                    </ul>
                </li>

                <li class="dropdown">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                        @if (auth()->user()->profile)
                            <img src="{{ asset('uploads/profile/' . auth()->user()->profile) }}"
                                class="img-circle" alt="Avatar">
                        @else
                            <img src="{{ asset('adminAssets/assets/img/profile.png') }}" class="img-circle"
                                alt="Avatar">
                        @endif
                        <span>{{ auth()->user()->email }}</span> <i class="icon-submenu lnr lnr-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin.users.editprofile')}}"><i class="lnr lnr-user"></i> <span>Profile</span></a></li>
                        <li><a href="{{ route('admin.users.editpassword')}}"><i class="lnr lnr-cog"></i> <span>Settings</span></a></li>
                        <li><a href="{{ route('admin.logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                    class="lnr lnr-exit"></i> <span>Logout</span></a></li>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
