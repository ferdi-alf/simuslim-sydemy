<nav class="fixed top-0 z-45 w-full bg-slate-100/50 backdrop-blur-2xl shadow-lg border-b border-gray-200">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">

            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                
                <a href="#" class="flex ms-2 md:me-24">
                    <img src="{{ asset('images/logo.png') }}" class="h-10 me-3"
                        alt="Logo" />
                    {{--
                    <p class="text-indigo-400 md:text-2xl text-lg font-extrabold">
                        Simuslim Sydemy
                    </p>
                    --}}
                </a>

            </div>
            <div class="flex items-center">
                <div class="flex items-center ms-3">
                    <button type="button"
                        class="flex items-center bg-transparent rounded-md p-1 space-x-2 me-2"
                        aria-expanded="false" data-dropdown-toggle="dropdown-user">
                        {{--
                        <img src="{{ asset('images/logo.png') }}" alt="profile"
                            class="h-10 w-10 rounded-full border border-red-500" />
                        --}}
                        <div class="flex flex-col text-left">
                            <span class="font-bold text-sm text-gray-800">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-xs text-gray-500 lowercase">
                                {{ Auth::user()->email }}
                            </span>
                        </div>
                    </button>
                </div>


                            <i class="fa-solid fa-caret-down"></i>
                        </button>
                    </div>
                    <div class="z-50 w-50 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-md shadow-md backdrop-blur-0"
                    id="dropdown-user">
                        {{--
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm  text-gray-900 " role="none">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate " role="none">
                                {{ Auth::user()->email }}

                            </p>
                        </div>
                        --}}
                        <ul class="py-1 bg-slate-100" role="none">
                            <li>
                                <a href="{{ route('dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 "
                                    role="menuitem">Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 "
                                    role="menuitem">Logout</a>
                            </li>
                            {{--
                            <li>
                                <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 "
                                    role="menuitem">Settings</a>
                            </li>
                            
                            --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
