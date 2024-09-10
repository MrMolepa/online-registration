<nav class="navbar navbar-default top-navbar" role="navigation">
    <div class="navbar-header">

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <a class="navbar-brand" href="./"><strong> ECoL</strong>
            <img src="{{ asset('school/assets/img/logo.png') }}" class="logo" alt="">
        </a>

        <div id="sideNav" href="">
            <i class="bx bx-left-arrow-circle icon"></i>
        </div>
    </div>

    <ul class="nav navbar-top-links navbar-right">

        <!-- /.dropdown -->
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-calendar" aria-hidden="true"></i> <?php echo date('l,F d Y'); ?>
            </a>

            <!-- /.dropdown-alerts -->
        </li>
        <!-- /.dropdown -->
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">

                @if (auth()->user()->profile)
                    <img src="{{ asset('uploads/profile/' . auth()->user()->profile) }}" class="img-circle"
                        alt="Avatar" width="20px">

                @else
                    <i class="fa fa-user fa-fw"></i>

                @endif</i>
                @if (auth()->user()->user_type == 'center')
                    {{ auth()->user()->center_name }}
                @else
                    {{ auth()->user()->email }}

                    @endif<i class="fa fa-caret-down"></i>

            </a>
            <ul class="dropdown-menu dropdown-user">

                <li><a href="{{ route('center.users.editprofile') }}"><i class="fa fa-user fa-fw"></i>Profile</a>
                </li>
                @if (auth()->user()->user_type != 'center')
                    <li><a href="{{ route('center.users.settings')}}"><i class="fas fa-cog fa-fw"></i> Settings</a>
                    </li>
                @endif
                <li class="divider"></li>
                <li>
                    <a href="{{ route('center.logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="fas fa-sign-out-alt fa-fw"></i> <span>Logout</span></a>
                </li>
                <form id="logout-form" action="{{ route('center.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
        </li>
    </ul>
    <!-- /.dropdown-user -->
    </li>
    <!-- /.dropdown -->
    </ul>
</nav>
