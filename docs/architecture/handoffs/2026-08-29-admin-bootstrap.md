# First Admin Bootstrap

Date: 2026-08-29
Status: IMPLEMENTED

## Why first Google login fails on an empty users table

Admin and Guru Google authentication is intentionally pre-provisioned. The OAuth callback never creates a privileged account. It only binds a verified Google identity to an existing active account whose normalized email and intended role already match.

Therefore `users = 0` must reject the first Google login. This is a security property, not a provider failure.

## Why copying a user row from another database is unreliable

Authentication does not trust only the visible `email` and `role` fields. Relevant identity state includes:

- `email_normalized`
- `google_id`
- `role`
- `disabled_at`
- the selected login portal / intended role
- Google's verified-email claim

A copied row can look correct in a database UI while still failing identity resolution because normalized identity or Google binding state does not match the current environment.

Do not copy privileged user rows between environments as a bootstrap procedure.

## Canonical bootstrap procedure

Provision the first Admin out-of-band through Artisan:

```bash
php artisan auth:bootstrap-admin \
  --name="Official Admin Name" \
  --email="verified-google-account@example.com"
```

The command:

- accepts an official display name and verified Google email;
- normalizes the email through the same account identity rules used by runtime auth;
- creates role `admin` only when no Admin exists yet;
- refuses an email already owned by another account;
- creates no Google binding itself (`google_id` remains `NULL`);
- creates an unusable random local password because Admin authentication is Google-only;
- leaves the account active;
- records `account.admin.bootstrap_created` without storing credentials in audit metadata;
- refuses subsequent bootstrap attempts after any Admin exists.

After bootstrap, open `/login/admin` and authenticate with exactly the Google account matching the provisioned email. On the first successful verified login, the existing Google binding flow stores the Google ID atomically and regenerates the authenticated session.

## Security boundary

This does not restore the removed "first Google user becomes Admin" behavior and does not introduce a `root` role. Roles remain:

- `admin`
- `guru`
- `murid`

The bootstrap command is a one-time operational entry point, not an alternate account-management surface. Normal Admin Account Management continues to create only Guru and Murid accounts.

## Failure behavior

Bootstrap must fail when:

- name is empty or longer than 120 characters;
- email is invalid or longer than 255 characters;
- any Admin already exists;
- the normalized email already belongs to any existing account.

If a database has manual/copied identity rows from earlier experiments, clean or reconcile those rows deliberately before bootstrap instead of weakening OAuth identity checks.

## Proof contract

Automated coverage must prove:

1. first bootstrap creates one normalized active Admin with no Google binding;
2. a second bootstrap is rejected;
3. an existing Guru/Murid/inert email cannot be escalated to Admin;
4. a bootstrapped Admin can bind the matching verified Google identity and reach the Admin dashboard.
