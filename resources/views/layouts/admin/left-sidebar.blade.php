 <section>
     <!-- Left Sidebar -->
     <aside id="leftsidebar" class="sidebar">
         <!-- User Info -->
         <div class="user-info">
             <a href="{{ route('dashboard') }}">
                 <div class="image">
                     <img src="{{ url('assets/images/user.png') }}" width="48" height="48" alt="User" />
                 </div>
             </a>
             <div class="info-container">
                 <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     {{ Auth::user()->name }}</div>
                 <div class="email">{{ Auth::user()->email }}</div>
                 <div class="btn-group user-helper-dropdown">
                     <i class="material-icons" data-toggle="dropdown" aria-haspopup="true"
                         aria-expanded="true">keyboard_arrow_down</i>
                     <ul class="dropdown-menu pull-right">
                         <li role="separator" class="divider"></li>
                         <li><a href="javascript:void(0);" data-toggle="modal" data-target="#changePasswordModal"><i
                                     class="material-icons">lock</i>{{ __('Change password') }}</a></li>
                         <li role="separator" class="divider"></li>
                         <li><a href="javascript:void(0);"
                                 onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();"><i
                                     class="material-icons">input</i>{{ __('Sign Out') }}</a></li>
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                             @csrf
                         </form>
                     </ul>
                 </div>
             </div>

         </div>
         <!-- #User Info -->
         <!-- Menu -->
         <div class="menu">
             <ul class="list">
                 <li class="header">{{ __('MAIN NAVIGATION') }}</li>
                 <li>
                     <a href="{{ route('dashboard') }}">
                         <i class="material-icons">home</i>
                         <span>{{ __('Home') }}</span>
                     </a>
                 </li>
                 @if (auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                     <li class="{{ Helper::setActive('admin/users') }}">
                         <a href="javascript:void(0);" class="menu-toggle">
                             <i class="material-icons">group</i>
                             <span>{{ __(key: 'users') }}</span>
                         </a>
                         <ul class="ml-menu">
                             <li class="{{ Helper::setActive('admin/users') }}">
                                 <a href="{{ route('admin.users') }}">{{ __('users') }}</a>
                             </li>
                         </ul>
                     </li>

                     <li class="main-menu">
                         <a href="javascript:void(0);" class="menu-toggle">
                             <i class="material-icons">view_list</i>
                             <span>{{ __('master') }}</span>
                         </a>
                         <ul class="ml-menu">
                             <li class="{{ Helper::setActive('admin/subjects') }}">
                                 <a href="{{ route('admin.subject') }}">{{ __('subjects') }}</a>
                             </li>

                             <li class="{{ Helper::setActive('admin/customer-number') }}">
                                 <a href="{{ route('admin.customer_number') }}">{{ __('customer numbers') }}</a>
                             </li>

                             <li class="{{ Helper::setActive('admin/contract-number') }}">
                                 <a href="{{ route('admin.contract_number') }}">{{ __('contract numbers') }}</a>
                             </li>

                             <li class="{{ Helper::setActive('admin/file-number') }}">
                                 <a href="{{ route('admin.file_number') }}">{{ __('file numbers') }}</a>
                             </li>

                             <li class="{{ Helper::setActive('admin/account-number') }}">
                                 <a href="{{ route('admin.account_number') }}">{{ __('account numbers') }}</a>
                             </li>

                             <li class="{{ Helper::setActive('admin/tax-number') }}">
                                 <a href="{{ route('admin.tax_number') }}">{{ __('tax numbers') }}</a>
                             </li>

                             <li class="{{ Helper::setActive('admin/firms') }}">
                                 <a href="{{ route('admin.firms') }}">{{ __('firms') }}</a>
                             </li>
                             <li class="{{ Helper::setActive('admin/parties') }}">
                                 <a href="{{ route('admin.parties') }}">{{ __('parties') }}</a>
                             </li>
                             <li class="{{ Helper::setActive('admin/cases') }}">
                                 <a href="{{ route('admin.cases') }}">{{ __('cases') }}</a>
                             </li>
                         </ul>
                     </li>

                    <li
                        class="{{ Helper::setActive('admin/letters') }}{{ Helper::setActive('admin/letters/search') }} ">
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">book</i>
                            <span>{{ __('letters') }}</span>
                        </a>
                        <ul class="ml-menu">
                            <li class="{{ Helper::setActive('admin/letters') }}">
                                <a href="{{ route('admin.letters') }}">{{ __('letters') }}</a>
                            </li>

                            <li class="{{ Helper::setActive('admin/letters/search') }}">
                                <a href="{{ route('admin.letters.search') }}">{{ __('search') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="{{ Helper::setActive('admin/emails') }}{{ Helper::setActive('admin/emails/compose') }} {{ Helper::setActive('admin/emails/sent') }}">
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">mail</i>
                            <span>{{ __('emails') }}</span>
                        </a>
                        <ul class="ml-menu">
                            <li class="{{ Helper::setActive('admin/emails/compose') }}">
                                <a href="{{ route('admin.emails.compose') }}">{{ __('compose') }}</a>
                            </li>
                            <li class="{{ Helper::setActive('admin/emails/sent') }}">
                                <a href="{{ route('admin.emails.sent') }}">{{ __('sent') }}</a>
                            </li>
                        </ul>
                    </li>

                     <li
                         class="{{ Helper::setActive('admin/documents') }}{{ Helper::setActive('admin/documents/create') }}">
                         <a href="javascript:void(0);" class="menu-toggle">
                             <i class="material-icons">book</i>
                             <span>{{ __('documents') }}</span>
                         </a>
                         <ul class="ml-menu">
                             <li class="{{ Helper::setActive('admin/documents') }}">
                                 <a href="{{ route('admin.documents') }}">{{ __('documents') }}</a>
                             </li>
                             <li class="{{ Helper::setActive('admin/documents/create') }}">
                                 <a href="{{ route('admin.documents.create') }}">{{ __('create') }}</a>
                             </li>
                         </ul>
                     </li>

                     <li
                         class="{{ Helper::setActive('admin/calendar') }}{{ Helper::setActive('admin/calendar/events') }} ">
                         <a href="javascript:void(0);" class="menu-toggle">
                             <i class="material-icons">event_note</i>
                             <span>{{ __('calendar') }}</span>
                         </a>
                         <ul class="ml-menu">
                             <li class="{{ Helper::setActive('admin/calendar') }}">
                                 <a href="{{ route('admin.calendar') }}">{{ __('calendar') }}</a>
                             </li>

                             <li class="{{ Helper::setActive('admin/calendar/events') }}">
                                 <a href="{{ route('admin.calendar.events') }}">{{ __('events') }}</a>
                             </li>
                         </ul>
                     </li>

                     <li class="{{ Helper::setActive('admin/settings') }}">
                         <a href="{{ route('admin.settings') }}" class=" waves-effect waves-block">
                             <i class="material-icons">settings</i>
                             <span>{{ __('settings') }}</span>
                         </a>
                     </li>

                     <li class="{{ Helper::setActive('admin/localization') }}">
                         <a href="{{ route('admin.localization') }}" class=" waves-effect waves-block">
                             <i class="material-icons">language</i>
                             <span>{{ __('localization') }}</span>
                         </a>
                     </li>
                 @endif

                 
                 @if (auth()->user()->role === \App\Models\User::ROLE_AGENT)
                     <li class="{{ Helper::setActive('admin/letters/create') }} ">
                         <a href="javascript:void(0);" class="menu-toggle">
                             <i class="material-icons">book</i>
                             <span>{{ __('letters') }}</span>
                         </a>
                         <ul class="ml-menu">
                             <li class="{{ Helper::setActive('admin/letters/create') }}">
                                 <a href="{{ route('admin.letters.create') }}">{{ __('add_new') }}</a>
                             </li>
                         </ul>
                     </li>
                 @endif
             </ul>
         </div>
         <!-- #Menu -->
         <!-- Footer -->
         <div class="legal">
             <div class="copyright">
                 &copy; {{ date('Y') }} <a href="javascript:void(0);">LMS</a>.
             </div>
             <div class="version">
                 <b>Version: </b> 1.0.0
             </div>
         </div>
         <!-- #Footer -->
     </aside>
     <!-- #END# Left Sidebar -->
     <!-- Right Sidebar -->
     <aside id="rightsidebar" class="right-sidebar">
         <ul class="nav nav-tabs tab-nav-right" role="tablist">
             <li role="presentation" class="active"><a href="#skins" data-toggle="tab">SKINS</a></li>
             <li role="presentation"><a href="#settings" data-toggle="tab">SETTINGS</a></li>
         </ul>
         <div class="tab-content">
             <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
                 <ul class="demo-choose-skin">
                     <li data-theme="red" class="active">
                         <div class="red"></div>
                         <span>Red</span>
                     </li>
                     <li data-theme="pink">
                         <div class="pink"></div>
                         <span>Pink</span>
                     </li>
                     <li data-theme="purple">
                         <div class="purple"></div>
                         <span>Purple</span>
                     </li>
                     <li data-theme="deep-purple">
                         <div class="deep-purple"></div>
                         <span>Deep Purple</span>
                     </li>
                     <li data-theme="indigo">
                         <div class="indigo"></div>
                         <span>Indigo</span>
                     </li>
                     <li data-theme="blue">
                         <div class="blue"></div>
                         <span>Blue</span>
                     </li>
                     <li data-theme="light-blue">
                         <div class="light-blue"></div>
                         <span>Light Blue</span>
                     </li>
                     <li data-theme="cyan">
                         <div class="cyan"></div>
                         <span>Cyan</span>
                     </li>
                     <li data-theme="teal">
                         <div class="teal"></div>
                         <span>Teal</span>
                     </li>
                     <li data-theme="green">
                         <div class="green"></div>
                         <span>Green</span>
                     </li>
                     <li data-theme="light-green">
                         <div class="light-green"></div>
                         <span>Light Green</span>
                     </li>
                     <li data-theme="lime">
                         <div class="lime"></div>
                         <span>Lime</span>
                     </li>
                     <li data-theme="yellow">
                         <div class="yellow"></div>
                         <span>Yellow</span>
                     </li>
                     <li data-theme="amber">
                         <div class="amber"></div>
                         <span>Amber</span>
                     </li>
                     <li data-theme="orange">
                         <div class="orange"></div>
                         <span>Orange</span>
                     </li>
                     <li data-theme="deep-orange">
                         <div class="deep-orange"></div>
                         <span>Deep Orange</span>
                     </li>
                     <li data-theme="brown">
                         <div class="brown"></div>
                         <span>Brown</span>
                     </li>
                     <li data-theme="grey">
                         <div class="grey"></div>
                         <span>Grey</span>
                     </li>
                     <li data-theme="blue-grey">
                         <div class="blue-grey"></div>
                         <span>Blue Grey</span>
                     </li>
                     <li data-theme="black">
                         <div class="black"></div>
                         <span>Black</span>
                     </li>
                 </ul>
             </div>
             <div role="tabpanel" class="tab-pane fade" id="settings">
                 <div class="demo-settings">
                     <p>GENERAL SETTINGS</p>
                     <ul class="setting-list">
                         <li>
                             <span>Report Panel Usage</span>
                             <div class="switch">
                                 <label><input type="checkbox" checked><span class="lever"></span></label>
                             </div>
                         </li>
                         <li>
                             <span>Email Redirect</span>
                             <div class="switch">
                                 <label><input type="checkbox"><span class="lever"></span></label>
                             </div>
                         </li>
                     </ul>
                     <p>SYSTEM SETTINGS</p>
                     <ul class="setting-list">
                         <li>
                             <span>Notifications</span>
                             <div class="switch">
                                 <label><input type="checkbox" checked><span class="lever"></span></label>
                             </div>
                         </li>
                         <li>
                             <span>Auto Updates</span>
                             <div class="switch">
                                 <label><input type="checkbox" checked><span class="lever"></span></label>
                             </div>
                         </li>
                     </ul>
                     <p>ACCOUNT SETTINGS</p>
                     <ul class="setting-list">
                         <li>
                             <span>Offline</span>
                             <div class="switch">
                                 <label><input type="checkbox"><span class="lever"></span></label>
                             </div>
                         </li>
                         <li>
                             <span>Location Permission</span>
                             <div class="switch">
                                 <label><input type="checkbox" checked><span class="lever"></span></label>
                             </div>
                         </li>
                     </ul>
                 </div>
             </div>
         </div>
     </aside>
     <!-- #END# Right Sidebar -->
 </section>
