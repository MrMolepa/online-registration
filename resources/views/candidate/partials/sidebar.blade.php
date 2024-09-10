<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('candidate.home') }}">
                <i class="mdi mdi-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('candidate.profile.index') }}">
                <i class="mdi menu-icon mdi-account-circle"></i>
                <span class="menu-title">Candidate Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('candidate.profile.kin') }}">
                <i class="mdi mdi-account-network menu-icon"></i>
                <span class="menu-title">Next of Kin</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('candidate.payment') }}">
                <i class="mdi mdi-cash-multiple  menu-icon"></i>
                <span class="menu-title">Payments</span>
            </a>
        </li>
    </ul>
</nav>
