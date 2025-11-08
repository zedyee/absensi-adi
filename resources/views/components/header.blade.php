<!-- header -->
<nav class="navbar navbar-expand-lg navbar-light bg-dark shadow-sm fixed-top w-100">
    <div class="container-fluid">
        <img class="ms-2 me-2" src="public/img/logo-ad.png" alt="Logo"
            style="width:40px; height:40px; object-fit:contain;">
        <a class="navbar-brand fw-bold me-auto text-secondary" href="#">Akar Daya</a>

        {{-- logout button --}}
        <div class="ms-auto">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- beri padding-top agar konten tidak tertutup navbar -->
<div class="pt-5">
    <!-- tabel kamu di sini -->
</div>
<!-- header end -->
