<dialog class="account-dialog" data-create-dialog>
  <form method="POST" action="{{ route('admin.accounts.store') }}" data-create-form>
    @csrf
    <header><div><span>Akun baru</span><h2>Tambah Guru atau Murid</h2></div><button type="button" data-close-dialog aria-label="Tutup">×</button></header>
    <label>Role<select name="role" data-create-role required><option value="guru">Guru</option><option value="murid">Murid</option></select></label>
    <label>Nama resmi<input name="name" required maxlength="120" autocomplete="off"></label>
    <label data-teacher-field>Email Google<input name="email" type="email" maxlength="255" autocomplete="off" required></label>
    <label data-student-field hidden>ID siswa<input name="student_id" maxlength="32" pattern="[A-Za-z0-9]{1,32}" autocomplete="off"></label>
    <label data-student-field hidden>Password awal<input name="password" type="password" maxlength="72" autocomplete="new-password"></label>
    <label data-student-field hidden>Konfirmasi password awal<input name="password_confirmation" type="password" maxlength="72" autocomplete="new-password"></label>
    <div class="account-form-errors" data-form-errors role="alert"></div>
    <footer><button type="button" class="admin-small-action admin-small-action--ghost" data-close-dialog>Batal</button><button type="submit" class="admin-primary-action">Simpan Akun</button></footer>
  </form>
</dialog>
