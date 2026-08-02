<dialog class="account-dialog" data-edit-dialog>
  <form method="POST" data-edit-form>
    @csrf
    @method('PUT')
    <header><div><span>Perbarui data</span><h2 data-edit-title>Edit Akun</h2></div><button type="button" data-close-dialog aria-label="Tutup">×</button></header>
    <label>Nama resmi<input name="name" required maxlength="120" autocomplete="off"></label>
    <label data-edit-email>Email Google<input name="email" type="email" maxlength="255" autocomplete="off"></label>
    <label data-edit-student>ID siswa<input name="student_id" maxlength="32" pattern="[A-Za-z0-9]{1,32}" autocomplete="off"></label>
    <div class="account-form-errors" data-form-errors role="alert"></div>
    <footer><button type="button" class="admin-small-action admin-small-action--ghost" data-close-dialog>Batal</button><button type="submit" class="admin-primary-action">Simpan Perubahan</button></footer>
  </form>
</dialog>
