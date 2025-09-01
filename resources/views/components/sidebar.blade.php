<div id="logo-sidebar"
    class="md:w-auto w-full transition-transform  bg-slate-100/10 -translate-x-full sm:translate-x-0 fixed top-0 left-0 h-screen  z-30 ">
    <aside class=" w-64 z-40 pt-20 h-screen bg-slate-100/20 backdrop-blur-3xl md:bg-red-100/10  shadow-lg ">
        <div class="h-full px-3 pb-4 overflow-y-auto">

            <ul class="space-y-2 font-medium mt-10">
                <x-fragments.sidebar-item route="dashboard" icon="gauge"
                    colors="indigo">Dashboard</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="user.index" icon="users"
                    colors="indigo">Users</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="masjid.index" icon="mosque"
                    colors="indigo">Masjid</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="ustadz.index" icon="user-plus"
                    colors="indigo">Ustadz</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="kajian.index" icon="calendar-days"
                    colors="indigo">Kajian</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="banner.index" icon="images"
                    colors="indigo">Banner</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="donasi.index" icon="hand-holding-dollar"
                    colors="indigo">Donasi</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="kajian-rekaman.index" icon="circle-play" colors="indigo">Rekaman
                    Kajian</x-fragments.sidebar-item>
                <x-fragments.sidebar-item route="symuslim.index" icon="layer-group"
                    colors="indigo">SyMuslim</x-fragments.sidebar-item>
            </ul>
        </div>
    </aside>
</div>
