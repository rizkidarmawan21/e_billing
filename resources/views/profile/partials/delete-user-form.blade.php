<section>
    <div class="mb-4">
        <h5 class="fw-bold mb-1 text-danger">
            Hapus Akun
        </h5>

        <p class="text-muted mb-0">
            Setelah akun dihapus, seluruh data akun tidak dapat dikembalikan.
        </p>
    </div>

    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        {{-- PASSWORD --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">
                Password
            </label>

            <input
                id="password"
                name="password"
                type="password"
                class="form-control"
                placeholder="Masukkan password untuk konfirmasi"
            >

            @if ($errors->userDeletion->get('password'))
                <div class="text-danger small mt-1">
                    {{ $errors->userDeletion->first('password') }}
                </div>
            @endif
        </div>

        <button type="submit"
                class="btn btn-danger px-4"
                onclick="return confirm('Yakin ingin menghapus akun administrator ini?')">
            Hapus Akun
        </button>
    </form>
</section>