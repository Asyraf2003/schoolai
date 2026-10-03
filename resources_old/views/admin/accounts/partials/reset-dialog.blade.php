<dialog class="account-dialog" data-reset-dialog>
  <form method="POST" data-reset-form>
    @csrf
    @method('PUT')
    <header><div><span>Keamanan murid</span><h2 data-reset-title>Reset Password</h2></div><button type="button" data-close-dialog aria-label="Tutup">×</button></header>
    <p>Password lama langsung tidak berlaku dan seluruh session lama akan dibatalkan.</p>
    <label>Password baru<input name="password" type="password" maxlength="72" autocomplete="new-password" required></label>
    <label>Konfirmasi password baru<input name="password_confirmation" type="password" maxlength="72" autocomplete="new-password" required></label>
    <div class="account-form-errors" data-form-errors role="alert"></div>
    <footer><button type="button" class="admin-small-action admin-small-action--ghost" data-close-dialog>Batal</button><button type="submit" class="admin-primary-action">Reset Password</button></footer>
  </form>
</dialog>
