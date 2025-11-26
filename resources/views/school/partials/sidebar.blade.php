<nav class="navbar-default navbar-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="main-menu">
            <li>
                <a href="{{ route('center.home') }}" class="{{ request()->is('center') ? 'active-menu' : '' }}"><i
                        class='bx bx-home'></i>Dashboard</a>
            </li>
            @permission('amendments-read')
                <li>
                    <a href="{{ route('center.candidates.index') }}"
                        class="{{ request()->is('center/candidates') ? 'active-menu' : '' }}"><i class='bx bx-pencil'></i>
                        Amend
                        Candidates</a>
                </li>
            @endpermission




            @permission('users-read')
                <li>
                    <a href="{{ route('center.users.index') }}"
                        class="{{ request()->is('center/users') ? 'active-menu' : '' }}"><i
                            class="fas fa-users"></i>Users</a>
                </li>
            @endpermission
            @permission('reports-read')
                <li>
                    <a href="{{ route('center.reports.index') }}"
                        class="{{ request()->is('center/reports') ? 'active-menu' : '' }}"><i class="fa fa-fw fa-file"></i>
                        Reports</a>
                </li>
            @endpermission


            @permission('payments-read')
                <li>
                    <a href="{{ route('center.payments.index') }}"
                        class="{{ request()->is('center/payments') ? 'active-menu' : '' }}"><i class='bx bx-money'></i>
                        Payments</a>
                </li>
            @endpermission


            <li>
                <a href="{{ route('center.documents.index') }}"
                    class="{{ request()->is('center/documents') ? 'active-menu' : '' }}"><i
                        class="fa fa-fw fa-book"></i>
                        Documents</a>
            </li>
            <li>
                <a href="{{ route('center.invigilators.index') }}"
                    class="{{ request()->is('center/invigilators') ? 'active-menu' : '' }}"><i
                        class="fa fa-fw fa-external-link"></i>
                    Invigilators</a>
            </li>
            <li>
                <a href="{{ route('center.help.index') }}"
                    class="{{ request()->is('center/help') ? 'active-menu' : '' }}"><i class='fas fa-question'></i>
                    Help</a>
            </li>


        </ul>


    </div>
</nav>



