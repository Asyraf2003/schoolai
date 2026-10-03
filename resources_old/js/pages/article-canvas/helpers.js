export function resizeTextarea(field) {
    field.style.height = 'auto';
    field.style.height = `${field.scrollHeight}px`;
}

export function closestBlock(node, editor) {
    const element = node instanceof Element ? node : node?.parentElement;
    const block = element?.closest('p,h2,h3,blockquote,li,pre,figure,div.article-embed,div.article-video');
    return block && editor?.contains(block) ? block : (element === editor ? editor : null);
}

export function placeCaret(element, atEnd = false) {
    const range = document.createRange();
    range.selectNodeContents(element);
    range.collapse(!atEnd);
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
    element.closest('[contenteditable="true"]')?.focus();
}

export function isCaretAtEnd(element) {
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0 || !selection.isCollapsed) return false;
    const range = selection.getRangeAt(0).cloneRange();
    const tail = document.createRange();
    tail.selectNodeContents(element);
    tail.setStart(range.endContainer, range.endOffset);
    return tail.toString() === '';
}

export function toDateTimeLocal(date) {
    const pad = (value) => String(value).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

export function isSafeHttpUrl(value) {
    try {
        const url = new URL(value);
        return ['http:', 'https:'].includes(url.protocol);
    } catch {
        return false;
    }
}

export async function jsonRequest(url, method, payload, csrf) {
    const response = await fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
    });
    const data = await readJsonResponse(response);
    if (!response.ok) throw new Error(errorMessage(data));
    return data;
}

export async function readJsonResponse(response) {
    const raw = await response.text();

    if (raw.trim() === '') {
        return {};
    }

    try {
        return JSON.parse(raw);
    } catch {
        const contentType = response.headers.get('content-type') || '';
        const looksLikeHtml = contentType.includes('text/html') || /^\s*<!doctype\s+html|^\s*<html/i.test(raw);

        if (!looksLikeHtml) {
            throw new Error(`Respons server tidak valid (HTTP ${response.status}).`);
        }

        if (response.status === 419) {
            throw new Error('Sesi admin atau token keamanan sudah kedaluwarsa. Muat ulang halaman, lalu coba publish lagi.');
        }

        if (response.status === 401 || response.status === 403 || (response.redirected && response.url.includes('/login'))) {
            throw new Error('Sesi login admin sudah berakhir. Login ulang, lalu buka kembali canvas artikel.');
        }

        if (response.status === 404) {
            throw new Error('Endpoint canvas tidak ditemukan. Bersihkan cache route Laravel lalu muat ulang halaman.');
        }

        if (response.status >= 500) {
            throw new Error(`Server gagal memproses artikel (HTTP ${response.status}). Periksa storage/logs/laravel.log untuk penyebabnya.`);
        }

        throw new Error(`Server mengembalikan halaman HTML, bukan data JSON (HTTP ${response.status}). Muat ulang canvas dan coba lagi.`);
    }
}

export function errorMessage(payload) {
    const errors = payload?.errors ? Object.values(payload.errors).flat() : [];
    return errors[0] || payload?.message || 'Terjadi kesalahan. Silakan coba lagi.';
}
