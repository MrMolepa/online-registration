<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
        <nav>
            <ul class="nav">
                <li>
                    <a href="{{ route('admin.home') }}"><i class="lnr lnr-home"></i> <span>Dashboard</span></a>
                </li>

                @permission('candidates-read')
                    <li>
                        <a href="#subPages" data-toggle="collapse" class="collapsed"><i class="lnr lnr-pencil"></i>
                            <span>Candidates</span><i class="icon-submenu lnr lnr-chevron-left"></i> </a>
                        <div id="subPages" class="collapse ">
                            <ul class="nav">
                                <li><a href="{{ route('admin.candidate-registation.index') }}"
                                        class="">Registration</a>
                                </li>
                                <li><a href="{{ route('admin.candidates.entries.index') }}" class="">Candidates
                                        Entries</a>
                                </li>
                                <li><a href="{{ route('admin.candidate-profile.index') }}" class="">Candidates
                                        Profile</a>
                                </li>
                            </ul>
                        </div>

                    </li>
                @endpermission


                @permission('subjects-read')
                    <li>
                        <a href="{{ route('admin.subjects.index') }}"><i class="lnr lnr lnr-book"></i>
                            <span>Subjects</span></a>
                    </li>
                @endpermission


                @permission('timetable-read')
                    <li>
                        <a href="#timetable" data-toggle="collapse" class="collapsed"><i class="lnr lnr-calendar-full"></i>
                            <span>Timetable</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                        <div id="timetable" class="collapse ">
                            <ul class="nav">
                                <li>
                                    <a href="{{ route('admin.timetable.index') }}" class="">
                                        <span>Timetable</span></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endpermission

                <li>
                    <a href="#subPageInvigilation" data-toggle="collapse" class="collapsed"><i
                            class="lnr lnr-apartment"></i>
                        <span>Invigilators</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                    <div id="subPageInvigilation" class="collapse ">
                        <ul class="nav">

                            <li>
                                <a href="{{ route('admin.invigilations.types.index') }}" class="">Invigilator
                                    Type</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.invigilations.candidatesrange.index') }}"
                                    class="">Candidate Range</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.invigilations.roles.index') }}" class="">Invigilator
                                    Roles</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.invigilations.paymentmethods.index') }}" class="">Payment
                                    Methods</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.invigilations.contracts.index') }}"
                                    class="">Inviglator's Contracts</a>
                            </li>

                        </ul>
                    </div>
                </li>
                @permission('centers-read')
                    <li>
                        <a href="#subPageCentre" data-toggle="collapse" class="collapsed"><i class="lnr lnr-apartment"></i>
                            <span>Centres</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                        <div id="subPageCentre" class="collapse ">
                            <ul class="nav">

                                <li>
                                    <a href="{{ route('admin.centers.index') }}" class="">Centres</a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endpermission

                <li>
                    <a href="#ducuments" data-toggle="collapse" class="collapsed"><i class="lnr lnr-pencil"></i>
                        <span>Documents</span><i class="icon-submenu lnr lnr-chevron-left"></i> </a>
                    <div id="ducuments" class="collapse ">
                        <ul class="nav">
                            <li><a href="{{ route('admin.document-categories.index') }}" class="">Categories</a>
                            </li>

                            <li><a href="{{ route('admin.documents.index') }}" class="">Documents</a>
                            </li>
                        </ul>
                    </div>

                </li>
                <li>
                    <a href="{{ route('admin.certificates.index') }}"><i class="lnr lnr-printer"></i>
                        <span>Statement Of Results</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.over-print.index') }}"><i class="lnr lnr-printer"></i>
                        <span> Over Print</span></a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class=""><i class="lnr lnr-users"></i>
                        <span>Users</span></a>
                </li>
                @permission('users-read')
                @endpermission
                <li>
                    <a href="#service" data-toggle="collapse" class="collapsed"><i
                            class="lnr lnr-briefcase"></i><span>E-Services</span> <i
                            class="icon-submenu lnr lnr-chevron-left"></i></a>
                    <div id="service" class="collapse ">
                        <ul class="nav">
                            <li><a href="{{ route('admin.service-sales.index') }}" class="">Paid Service</a>
                            </li>
                            <li><a href="{{ route('admin.services.index') }}" class="">Services</a>
                            </li>

                        </ul>
                    </div>

                </li>
                @permission('payments-read')
                    <li>
                        <a href="#subfees" data-toggle="collapse" class="collapsed"><i
                                class="lnr lnr-cart"></i><span>Finance</span> <i
                                class="icon-submenu lnr lnr-chevron-left"></i></a>
                        <div id="subfees" class="collapse ">
                            <ul class="nav">
                                <li><a href="{{ route('admin.fee-estamates.index') }}" class="">Fee Estamates</a>
                                </li>

                                <li><a href="{{ route('admin.payments-verification.index') }}" class="">
                                        Payments Verification</a></li>
                                <li><a href="{{ route('admin.payment-history.index') }}" class="">Payments
                                        History</a>
                                </li>
                                <li><a href="{{ route('admin.fees.index') }}" class="">Fee
                                        Charges</a></li>
                                <li><a href="{{ route('admin.finantial-report.index') }}" class="">Reports</a></li>

                            </ul>
                        </div>

                    </li>

                    <li>
                        <a href="{{ route('admin.processes.index') }}"><i class="lnr lnr-layers"></i>
                            <span>Processes</span></a>
                    </li>
                @endpermission
                <li>
                    <a href="#privilege-role" data-toggle="collapse" class="collapsed"><i class="lnr lnr-lock"></i>
                        <span>Privileges & Roles</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                    <div id="privilege-role" class="collapse ">
                        <ul class="nav">

                            <li>
                                @permission('roles-read')
                                    <a href="{{ route('admin.roles.index') }}" class=""> <span>Roles
                                        </span></a>
                                @endpermission
                                @permission('permissions-read')
                                    <a href="{{ route('admin.permissions.index') }}" class="">
                                        <span>Permissions
                                        </span></a>
                                @endpermission
                            </li>
                        </ul>
                    </div>
                </li>
                @permission('logs-read')
                    <li>
                        <a href="{{ route('admin.logs.index') }}" class=""><i class="lnr lnr-bookmark"></i>
                            <span>Logs</span></a>

                    </li>
                @endpermission
                @permission('settings-read')
                    <li>
                        <a href="#setting" data-toggle="collapse" class="collapsed"><i class="lnr lnr-cog"></i>
                            <span>Setting</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
                        <div id="setting" class="collapse ">
                            <ul class="nav">
                                <li><a href="{{ route('admin.setting.index') }}" class="">System</a></li>
                                <li><a href="{{ route('admin.publications.index') }}" class="">Publications</a>
                                </li>
                            </ul>
                        </div>

                    </li>
                @endpermission
                @permission('backup-read')
                    <li>
                        <a href="{{ route('admin.backup.index') }}" class=""><i class="lnr lnr-database"></i>
                            <span>Backup &
                                Restore</span></a>
                    </li>
                @endpermission
            </ul>

        </nav>
    </div>
</div>
