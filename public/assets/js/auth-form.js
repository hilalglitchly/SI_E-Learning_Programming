(() => {
    const form = document.querySelector('[data-auth-form]');
    if (!form) return;

    // 1. Accessible show/hide password toggles
    form.querySelectorAll('[data-toggle-password]').forEach((btn) => {
        const input = document.getElementById(btn.getAttribute('aria-controls'));
        if (!input) return;

        btn.addEventListener('click', () => {
            const reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            btn.setAttribute('aria-pressed', reveal ? 'true' : 'false');
            btn.setAttribute('aria-label', reveal ? 'Sembunyikan password' : 'Tampilkan password');

            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = reveal ? 'bx bx-hide' : 'bx bx-show';
            }
        });
    });

    // 2. Live confirm-password matching via native constraint validation
    const confirmInput = form.querySelector('[data-match-password]');
    if (confirmInput) {
        const sourceInput = document.getElementById(confirmInput.dataset.matchPassword);
        const syncMatch = () => {
            if (!sourceInput) return;
            if (confirmInput.value !== '' && confirmInput.value !== sourceInput.value) {
                confirmInput.setCustomValidity('Konfirmasi password tidak cocok dengan password.');
            } else {
                confirmInput.setCustomValidity('');
            }
        };
        if (sourceInput) sourceInput.addEventListener('input', () => {
            syncMatch();
            if (confirmInput.hasAttribute('aria-invalid')) syncAria(confirmInput);
        });
        confirmInput.addEventListener('input', syncMatch);
    }

    // 3. Synchronize ARIA error states with :user-invalid (with fallback for older browsers)
    const supportsUserInvalid = typeof CSS !== 'undefined' && CSS.supports('selector(:user-invalid)');
    const dirtyState = new WeakMap();
    const flagged = new WeakSet();
    const announced = new WeakSet();

    const announcer = document.createElement('div');
    announcer.setAttribute('aria-live', 'polite');
    announcer.className = 'visually-hidden';
    document.body.append(announcer);
    let announceTimer;
    let announcedField;

    const linkError = (el, shown) => {
        const errorId = el.dataset.error;
        if (!errorId) return;
        const ids = (el.getAttribute('aria-describedby') || '').split(/\s+/).filter((id) => id && id !== errorId);
        if (shown) ids.push(errorId);
        if (ids.length) el.setAttribute('aria-describedby', ids.join(' '));
        else el.removeAttribute('aria-describedby');
    };

    const announceError = (el) => {
        const errorEl = document.getElementById(el.dataset.error || '');
        if (!errorEl) return;
        const label = el.labels && el.labels[0] ? `${el.labels[0].textContent.trim()}: ` : '';
        clearTimeout(announceTimer);
        announcer.textContent = '';
        announcedField = el;
        announceTimer = setTimeout(() => {
            announcer.textContent = label + errorEl.textContent.replace(/\s+/g, ' ').trim();
        }, 450);
    };

    function syncAria(el, announce = false) {
        if (!el || !el.matches || !el.matches('input, select, textarea')) return;

        let invalid = false;
        if (supportsUserInvalid) {
            invalid = el.matches(':user-invalid');
        } else {
            const st = dirtyState.get(el);
            if (st && st.hasBlurred && st.hasInteracted) {
                invalid = !el.validity.valid;
                el.classList.toggle('user-invalid-fallback', invalid);
                el.classList.toggle('user-valid-fallback', !invalid);
            }
        }

        if (invalid) {
            if (announce && !announced.has(el)) {
                announceError(el);
                announced.add(el);
            }
            el.setAttribute('aria-invalid', 'true');
            linkError(el, true);
            flagged.add(el);
        } else if (flagged.has(el) || el.getAttribute('aria-invalid') !== 'true') {
            el.setAttribute('aria-invalid', 'false');
            linkError(el, false);
            flagged.delete(el);
            announced.delete(el);
            if (announcedField === el) {
                clearTimeout(announceTimer);
                announcer.textContent = '';
            }
        }
    }

    form.querySelectorAll('input').forEach((el) => {
        if (el.type !== 'hidden') el.setAttribute('aria-invalid', 'false');
    });

    form.addEventListener('input', (e) => {
        if (!supportsUserInvalid) {
            const st = dirtyState.get(e.target) || { hasInteracted: false, hasBlurred: false };
            st.hasInteracted = true;
            dirtyState.set(e.target, st);
        }
        syncAria(e.target);
    });

    form.addEventListener('blur', (e) => {
        if (!supportsUserInvalid) {
            const st = dirtyState.get(e.target) || { hasInteracted: false, hasBlurred: false };
            st.hasBlurred = true;
            dirtyState.set(e.target, st);
        }
        syncAria(e.target, true);
    }, true);

    form.addEventListener('invalid', (e) => {
        if (!supportsUserInvalid) {
            dirtyState.set(e.target, { hasInteracted: true, hasBlurred: true });
        }
        announced.add(e.target);
        syncAria(e.target);
    }, true);

    // 4. Prevent duplicate submissions with visible loading state
    form.addEventListener('submit', (e) => {
        if (confirmInput) {
            const src = document.getElementById(confirmInput.dataset.matchPassword);
            if (src && confirmInput.value !== src.value) {
                confirmInput.setCustomValidity('Konfirmasi password tidak cocok dengan password.');
            }
        }
        if (!form.checkValidity()) {
            return;
        }
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.textContent = submitBtn.dataset.loadingText || 'Memproses...';
        }
    });
})();
