{{--**********************************
Sidebar start
***********************************--}}
@persist('sidebar')
<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li class="menu-title">POLYSPHERE TECH</li>

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}" wire:navigate.hover
                    class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                    <div class="menu-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.5 7.49999L10 1.66666L17.5 7.49999V16.6667C17.5 17.1087 17.3244 17.5326 17.0118 17.8452C16.6993 18.1577 16.2754 18.3333 15.8333 18.3333H4.16667C3.72464 18.3333 3.30072 18.1577 2.98816 17.8452C2.67559 17.5326 2.5 17.1087 2.5 16.6667V7.49999Z"
                                stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7.5 18.3333V10H12.5V18.3333" stroke="#888888" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Core HR --}}
                    <li>
                        <a href="{{ route('hr.dashboard') }}" wire:navigate.hover
                           class="{{ request()->routeIs('hr.dashboard') ? 'mm-active' : '' }}">
                            <div class="menu-icon">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.8381 12.7317C16.4566 12.7317 16.9757 13.2422 16.8811 13.853C16.3263 17.4463 13.2502 20.1143 9.54009 20.1143C5.43536 20.1143 2.10834 16.7873 2.10834 12.6835C2.10834 9.30245 4.67693 6.15297 7.56878 5.44087C8.19018 5.28745 8.82702 5.72455 8.82702 6.36429C8.82702 10.6987 8.97272 11.8199 9.79579 12.4297C10.6189 13.0396 11.5867 12.7317 15.8381 12.7317Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M19.8848 9.1223C19.934 6.33756 16.5134 1.84879 12.345 1.92599C12.0208 1.93178 11.7612 2.20195 11.7468 2.5252C11.6416 4.81493 11.7834 7.78204 11.8626 9.12713C11.8867 9.5459 12.2157 9.87493 12.6335 9.89906C14.0162 9.97818 17.0914 10.0862 19.3483 9.74467C19.6552 9.69835 19.88 9.43204 19.8848 9.1223Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="nav-text">Core HR</span>
                        </a>
                    </li>

                    {{-- User Management: Users / Roles / Permissions --}}
                    <li>
                        <a class="has-arrow {{ request()->routeIs('users', 'roles', 'permissions') ? 'mm-active' : '' }}"
                           href="javascript:void(0);"
                           aria-expanded="{{ request()->routeIs('users', 'roles', 'permissions') ? 'true' : 'false' }}">
                            <div class="menu-icon">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.79222 13.9396C12.1738 13.9396 15.0641 14.452 15.0641 16.4989C15.0641 18.5458 12.1931 19.0729 8.79222 19.0729C5.40972 19.0729 2.52039 18.5651 2.52039 16.5172C2.52039 14.4694 5.39047 13.9396 8.79222 13.9396Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.79223 11.0182C6.57206 11.0182 4.77173 9.21874 4.77173 6.99857C4.77173 4.7784 6.57206 2.97898 8.79223 2.97898C11.0115 2.97898 12.8118 4.7784 12.8118 6.99857C12.8201 9.21049 11.0326 11.0099 8.82064 11.0182H8.79223Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M15.1095 9.9748C16.5771 9.76855 17.7073 8.50905 17.7101 6.98464C17.7101 5.48222 16.6147 4.23555 15.1782 3.99997"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M17.0458 13.5045C18.4675 13.7163 19.4603 14.2149 19.4603 15.2416C19.4603 15.9483 18.9928 16.4067 18.2374 16.6936"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="nav-text">User Management</span>
                        </a>
                        <ul aria-expanded="false" class="{{ request()->routeIs('users', 'roles', 'permissions') ? 'mm-show' : '' }}">
                            <li>
                                <a href="{{ route('users') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('users') ? 'mm-active' : '' }}">
                                    Users
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('roles') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('roles') ? 'mm-active' : '' }}">
                                    Roles
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('permissions') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('permissions') ? 'mm-active' : '' }}">
                                    Permissions
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Projects: All Projects / Add Project --}}
                    <li>
                        <a class="has-arrow {{ request()->routeIs('admin.projects.*') ? 'mm-active' : '' }}"
                           href="javascript:void(0);"
                           aria-expanded="{{ request()->routeIs('admin.projects.*') ? 'true' : 'false' }}">
                            <div class="menu-icon">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.75713 9.35157V15.64" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M11.0349 6.34253V15.64" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M15.2428 12.6746V15.64" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.2952 1.83333H6.70474C3.7103 1.83333 1.83331 3.95274 1.83331 6.95306V15.0469C1.83331 18.0473 3.70157 20.1667 6.70474 20.1667H15.2952C18.2984 20.1667 20.1666 18.0473 20.1666 15.0469V6.95306C20.1666 3.95274 18.2984 1.83333 15.2952 1.83333Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="nav-text">Projects</span>
                        </a>
                        <ul aria-expanded="false" class="{{ request()->routeIs('admin.projects.*') ? 'mm-show' : '' }}">
                            <li>
                                <a href="{{ route('admin.projects.index') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('admin.projects.index') ? 'mm-active' : '' }}">
                                    All Projects
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.projects.create') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('admin.projects.create') ? 'mm-active' : '' }}">
                                    Add Project
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Services: All Services / Add Service --}}
                    <li>
                        <a class="has-arrow {{ request()->routeIs('admin.services.*') ? 'mm-active' : '' }}"
                           href="javascript:void(0);"
                           aria-expanded="{{ request()->routeIs('admin.services.*') ? 'true' : 'false' }}">
                            <div class="menu-icon">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M18.634 13.4211C18.634 16.7009 16.7007 18.6342 13.4209 18.6342H6.28738C2.99929 18.6342 1.06238 16.7009 1.06238 13.4211V6.27109C1.06238 2.99584 2.26688 1.06259 5.54763 1.06259H7.38096C8.03913 1.06351 8.65879 1.37242 9.05296 1.89951L9.88988 3.01234C10.2859 3.53851 10.9055 3.84834 11.5637 3.84926H14.1579C17.446 3.84926 18.6596 5.52309 18.6596 8.86984L18.634 13.4211Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.85754 12.2577H13.8646" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="nav-text">Services</span>
                        </a>
                        <ul aria-expanded="false" class="{{ request()->routeIs('admin.services.*') ? 'mm-show' : '' }}">
                            <li>
                                <a href="{{ route('admin.services.index') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('admin.services.index') ? 'mm-active' : '' }}">
                                    All Services
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.services.create') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('admin.services.create') ? 'mm-active' : '' }}">
                                    Add Service
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- CMS: Blog / Categories --}}
                    <li>
                        <a class="has-arrow {{ request()->routeIs('manage.posts', 'create.post', 'edit.post', 'manage.categories', 'create.categories', 'edit.categories') ? 'mm-active' : '' }}"
                           href="javascript:void(0);"
                           aria-expanded="{{ request()->routeIs('manage.posts', 'create.post', 'edit.post', 'manage.categories', 'create.categories', 'edit.categories') ? 'true' : 'false' }}">
                            <div class="menu-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M20.8064 7.62355L20.184 6.54346C19.6574 5.62954 18.4905 5.31426 17.5753 5.83866V5.83866C17.1397 6.09528 16.6198 6.16809 16.1305 6.04103C15.6411 5.91396 15.2224 5.59746 14.9666 5.16131C14.8021 4.88409 14.7137 4.56833 14.7103 4.24598V4.24598C14.7251 3.72916 14.5302 3.22834 14.1698 2.85761C13.8094 2.48688 13.3143 2.2778 12.7973 2.27802H11.5433C11.0367 2.27801 10.5511 2.47985 10.1938 2.83888C9.83644 3.19791 9.63693 3.68453 9.63937 4.19106V4.19106C9.62435 5.23686 8.77224 6.07675 7.72632 6.07664C7.40397 6.07329 7.08821 5.98488 6.81099 5.82035V5.82035C5.89582 5.29595 4.72887 5.61123 4.20229 6.52516L3.5341 7.62355C3.00817 8.53633 3.31916 9.70255 4.22975 10.2322V10.2322C4.82166 10.574 5.18629 11.2055 5.18629 11.889C5.18629 12.5725 4.82166 13.204 4.22975 13.5457V13.5457C3.32031 14.0719 3.00898 15.2353 3.5341 16.1453V16.1453L4.16568 17.2345C4.4124 17.6797 4.82636 18.0082 5.31595 18.1474C5.80554 18.2865 6.3304 18.2248 6.77438 17.976V17.976C7.21084 17.7213 7.73094 17.6515 8.2191 17.7821C8.70725 17.9128 9.12299 18.233 9.37392 18.6716C9.53845 18.9488 9.62686 19.2646 9.63021 19.5869V19.5869C9.63021 20.6435 10.4867 21.5 11.5433 21.5H12.7973C13.8502 21.5 14.7053 20.6491 14.7103 19.5961V19.5961C14.7079 19.088 14.9086 18.6 15.2679 18.2407C15.6272 17.8814 16.1152 17.6806 16.6233 17.6831C16.9449 17.6917 17.2594 17.7797 17.5387 17.9393V17.9393C18.4515 18.4653 19.6177 18.1543 20.1474 17.2437V17.2437L20.8064 16.1453C21.0615 15.7074 21.1315 15.1859 21.001 14.6963C20.8704 14.2067 20.55 13.7893 20.1108 13.5366V13.5366C19.6715 13.2839 19.3511 12.8665 19.2206 12.3769C19.09 11.8872 19.16 11.3658 19.4151 10.9279C19.581 10.6383 19.8211 10.3981 20.1108 10.2322V10.2322C21.0159 9.70283 21.3262 8.54343 20.8064 7.63271V7.63271V7.62355Z"
                                        stroke="#888888" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="12.1747" cy="11.889" r="2.63616" stroke="#888888" stroke-width="1"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="nav-text">CMS</span>
                        </a>
                        <ul aria-expanded="false" class="{{ request()->routeIs('manage.posts', 'create.post', 'edit.post', 'manage.categories', 'create.categories', 'edit.categories') ? 'mm-show' : '' }}">
                            <li>
                                <a href="{{ route('manage.posts') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('manage.posts', 'create.post', 'edit.post') ? 'mm-active' : '' }}">
                                    All Posts
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('create.post') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('create.post') ? 'mm-active' : '' }}">
                                    Add Post
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('manage.categories') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('manage.categories', 'edit.categories') ? 'mm-active' : '' }}">
                                    All Categories
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('create.categories') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('create.categories') ? 'mm-active' : '' }}">
                                    Add Category
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Chat / Messenger --}}
                    <li>
                        <a href="{{ route('messenger') }}" wire:navigate.hover
                           class="{{ request()->routeIs('messenger') ? 'mm-active' : '' }}">
                            <div class="menu-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.11086 10.2878V13.7208" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M8.86244 12.0045H5.35974" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M13.0856 10.3924H12.9875" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M14.748 13.6691H14.6499" stroke="#888888" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M6.39948 0.833328C6.39948 1.5121 6.96092 2.06236 7.65349 2.06236H8.62193C9.69042 2.06617 10.5559 2.9144 10.5608 3.9616V4.5804"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M14.0593 19.1324C11.3045 19.1791 8.60026 19.1771 5.94166 19.1324C2.99069 19.1324 0.833313 17.0275 0.833313 14.1354V9.87325C0.833313 6.98107 2.99069 4.8762 5.94166 4.8762C8.61483 4.83051 11.321 4.83146 14.0593 4.8762C17.0102 4.8762 19.1666 6.98203 19.1666 9.87325V14.1354C19.1666 17.0275 17.0102 19.1324 14.0593 19.1324Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="nav-text">Chat</span>
                        </a>
                    </li>

                    <li class="menu-title">ACCOUNT</li>

                    {{-- Account: Overview / Profile / Security / Activity --}}
                    <li>
                        <a class="has-arrow {{ request()->routeIs('account') ? 'mm-active' : '' }}"
                           href="javascript:void(0);"
                           aria-expanded="{{ request()->routeIs('account') ? 'true' : 'false' }}">
                            <div class="menu-icon">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.986 14.0673C7.4407 14.0673 4.41309 14.6034 4.41309 16.7501C4.41309 18.8969 7.4215 19.4521 10.986 19.4521C14.5313 19.4521 17.5581 18.9152 17.5581 16.7693C17.5581 14.6234 14.5505 14.0673 10.986 14.0673Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.986 11.0054C13.3126 11.0054 15.1983 9.11881 15.1983 6.79223C15.1983 4.46564 13.3126 2.57993 10.986 2.57993C8.65944 2.57993 6.77285 4.46564 6.77285 6.79223C6.76499 9.11096 8.63849 10.9975 10.9563 11.0054H10.986Z"
                                        stroke="#888888" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="nav-text">Account</span>
                        </a>
                        <ul aria-expanded="false" class="{{ request()->routeIs('account') ? 'mm-show' : '' }}">
                            <li>
                                <a href="{{ route('account', 'overview') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('account') && request()->route('tab', 'overview') === 'overview' ? 'mm-active' : '' }}">
                                    Overview
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account', 'profile') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('account') && request()->route('tab') === 'profile' ? 'mm-active' : '' }}">
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account', 'security') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('account') && request()->route('tab') === 'security' ? 'mm-active' : '' }}">
                                    Security
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account', 'activity') }}" wire:navigate.hover
                                   class="{{ request()->routeIs('account') && request()->route('tab') === 'activity' ? 'mm-active' : '' }}">
                                    Activity
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <div class="help-desk">
                    <a href="javascript:void(0)" class="btn btn-primary">Help Desk</a>
                </div>
            </div>
        </div>
        @endpersist
        {{--**********************************
                    Sidebar end
                ***********************************--}}