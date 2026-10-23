<aside class="admin-sidebar" aria-label="Navigasi pengelolaan">
    <div class="admin-brand">
        <span class="admin-brand-mark" aria-hidden="true">M</span>
        <div class="admin-brand-copy">
            <strong>Al Mustaqbal</strong>
            <span>Administrasi sekolah</span>
        </div>
    </div>
    <div class="admin-sidebar-menu">
        <p class="admin-nav-caption">MENU UTAMA</p>
        <nav class="admin-nav" aria-label="Menu admin">
            <x-admin.nav-item route="admin.accounts.index" icon="users" :active="request()->routeIs('admin.accounts.*', 'admin.dashboard')">Akun</x-admin.nav-item>
            <x-admin.nav-item route="admin.galeri" icon="images" :active="request()->routeIs('admin.galeri*')">Galeri</x-admin.nav-item>
            <x-admin.nav-item route="admin.artikel" icon="article" :active="request()->routeIs('admin.artikel*')">Artikel</x-admin.nav-item>
            <x-admin.nav-item route="admin.ppdb" icon="link" :active="request()->routeIs('admin.ppdb*')">Link PPDB</x-admin.nav-item>
        </nav>
    </div>
    <div class="admin-sidebar-footer">
        <a class="admin-site-link" href="{{ route('home') }}">
            <x-admin.icon name="external"/><span>Lihat website</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-admin.button type="submit" variant="quiet" class="admin-logout">
                <x-admin.icon name="logout"/>Keluar
            </x-admin.button>
        </form>
    </div>
</aside>
