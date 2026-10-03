@foreach($accounts as $account)
  <tr>
    <td><strong>{{ $account->name }}</strong><small>{{ $account->email ?? $account->student_id ?? 'Tanpa identitas login' }}</small></td>
    <td>{{ $account->role?->label() ?? 'Inert' }}</td>
    <td>{{ $account->isActive() ? 'Aktif' : 'Nonaktif' }}</td>
    <td>{{ $account->last_login_at?->translatedFormat('d M Y, H:i') ?? 'Belum pernah' }}</td>
    <td><span class="account-progressive-note">Aktifkan JavaScript untuk aksi dinamis.</span></td>
  </tr>
@endforeach
