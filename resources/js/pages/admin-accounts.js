import {
    accountUrl,
    sendAccountMutation,
    setFormBusy,
    showFormErrors,
} from './admin-account-api';

const root = document.querySelector('[data-account-manager]');

if (root) {
    const rows = root.querySelector('[data-account-rows]');
    const empty = root.querySelector('[data-account-empty]');
    const panel = root.querySelector('[data-account-panel]');
    const filters = root.querySelector('[data-account-filters]');
    const notice = root.querySelector('[data-account-notice]');
    const sync = root.querySelector('[data-account-sync]');
    const createDialog = root.querySelector('[data-create-dialog]');
    const editDialog = root.querySelector('[data-edit-dialog]');
    const resetDialog = root.querySelector('[data-reset-dialog]');
    const createForm = root.querySelector('[data-create-form]');
    const editForm = root.querySelector('[data-edit-form]');
    const resetForm = root.querySelector('[data-reset-form]');
    let etag = null;
    let debounceId = 0;

    const openDialog = (dialog) => dialog.showModal();
    const closeDialog = (dialog) => {
        dialog.close();
        dialog.querySelector('form')?.reset();
        dialog.querySelector('[data-form-errors]')?.replaceChildren();
    };
    const cell = (value, className = '') => {
        const item = document.createElement('td');
        item.className = className;
        item.textContent = value;
        return item;
    };
    const actionButton = (label, className, handler) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = className;
        button.textContent = label;
        button.addEventListener('click', handler);
        return button;
    };

    function identityCell(account) {
        const item = document.createElement('td');
        const name = document.createElement('strong');
        const identity = document.createElement('small');
        name.textContent = account.name;
        identity.textContent = account.email || account.student_id || 'Tanpa identitas login';
        item.append(name, identity);
        return item;
    }

    function openEdit(account) {
        editForm.action = accountUrl(root.dataset.updateUrl, account.id);
        editForm.elements.name.value = account.name;
        editForm.elements.email.value = account.email || '';
        editForm.elements.student_id.value = account.student_id || '';
        editDialog.querySelector('[data-edit-title]').textContent = `Edit ${account.name}`;
        editDialog.querySelector('[data-edit-email]').hidden = !['admin', 'guru'].includes(account.role);
        editDialog.querySelector('[data-edit-student]').hidden = account.role !== 'murid';
        openDialog(editDialog);
    }

    function openReset(account) {
        resetForm.action = accountUrl(root.dataset.resetUrl, account.id);
        resetDialog.querySelector('[data-reset-title]').textContent = `Reset Password ${account.name}`;
        openDialog(resetDialog);
    }

    async function changeStatus(account) {
        const data = new FormData();
        data.append('_method', 'PATCH');
        data.append('active', account.active ? '0' : '1');
        try {
            const payload = await sendAccountMutation(accountUrl(root.dataset.statusUrl, account.id), data);
            notice.textContent = payload.message;
            etag = null;
            await loadAccounts();
        } catch (error) {
            notice.textContent = error.message;
        }
    }

    function renderAccounts(accounts) {
        rows.replaceChildren();
        accounts.forEach((account) => {
            const row = document.createElement('tr');
            const actions = document.createElement('td');
            actions.className = 'account-actions';
            actions.append(actionButton('Edit', 'admin-small-action admin-small-action--ghost', () => openEdit(account)));
            actions.append(actionButton(account.active ? 'Nonaktifkan' : 'Aktifkan', 'admin-small-action', () => changeStatus(account)));
            if (account.role === 'murid') {
                actions.append(actionButton('Reset Password', 'admin-small-action admin-small-action--ghost', () => openReset(account)));
            }
            row.append(
                identityCell(account),
                cell(account.role_label),
                cell(account.status_label, account.active ? 'is-active' : 'is-inactive'),
                cell(account.last_login_label),
                actions,
            );
            rows.append(row);
        });
        empty.hidden = accounts.length > 0;
    }

    async function loadAccounts(showLoading = false) {
        if (showLoading) panel.setAttribute('aria-busy', 'true');
        const url = new URL(root.dataset.listUrl, window.location.origin);
        new FormData(filters).forEach((value, key) => {
            if (value) url.searchParams.set(key, value);
        });
        const headers = { Accept: 'application/json' };
        if (etag) headers['If-None-Match'] = etag;

        try {
            const response = await fetch(url, { headers });
            if (response.status !== 304) {
                if (!response.ok) throw new Error('Daftar akun gagal diperbarui.');
                etag = response.headers.get('ETag');
                renderAccounts((await response.json()).data);
            }
            sync.textContent = `Sinkron ${new Date().toLocaleTimeString('id-ID')}`;
        } catch (error) {
            notice.textContent = error.message;
        } finally {
            panel.setAttribute('aria-busy', 'false');
        }
    }

    async function submitForm(form, dialog) {
        const data = new FormData(form);
        setFormBusy(form, true);
        form.querySelector('[data-form-errors]').replaceChildren();
        try {
            const payload = await sendAccountMutation(form.action, data);
            notice.textContent = payload.message;
            closeDialog(dialog);
            etag = null;
            await loadAccounts();
        } catch (error) {
            showFormErrors(form, error.payload || { message: error.message });
        } finally {
            setFormBusy(form, false);
        }
    }

    root.querySelector('[data-open-create]').addEventListener('click', () => openDialog(createDialog));
    root.querySelectorAll('[data-close-dialog]').forEach((button) => button.addEventListener('click', () => closeDialog(button.closest('dialog'))));
    [createForm, editForm, resetForm].forEach((form) => form.addEventListener('submit', (event) => {
        event.preventDefault();
        submitForm(form, form.closest('dialog'));
    }));
    root.querySelector('[data-create-role]').addEventListener('change', (event) => {
        const student = event.target.value === 'murid';
        root.querySelector('[data-teacher-field]').hidden = student;
        root.querySelectorAll('[data-student-field]').forEach((field) => { field.hidden = !student; });
        createForm.elements.email.required = !student;
        ['student_id', 'password', 'password_confirmation'].forEach((name) => { createForm.elements[name].required = student; });
    });
    filters.addEventListener('input', () => {
        window.clearTimeout(debounceId);
        debounceId = window.setTimeout(() => { etag = null; loadAccounts(true); }, 300);
    });
    document.addEventListener('visibilitychange', () => { if (!document.hidden) loadAccounts(); });
    window.setInterval(() => { if (!document.hidden) loadAccounts(); }, 30000);
    loadAccounts();
}
