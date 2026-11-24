// NeoAlert Custom Component
// Designed specifically for Neo Brutalism aesthetic

function NeoToast(message, type = 'success') {
    const safeType = ['success', 'error', 'warning'].includes(type) ? type : 'success';

    // Container for toasts
    let container = document.getElementById('neo-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'neo-toast-container';
        document.body.appendChild(container);
    }

    // Create toast element safely without interpolating user strings into innerHTML
    const toast = document.createElement('div');
    toast.className = `neo-toast neo-toast-${safeType}`;

    const iconWrap = document.createElement('div');
    iconWrap.className = 'neo-toast-icon';
    const iconEl = document.createElement('i');
    if (safeType === 'success') iconEl.className = 'bx bxs-check-circle';
    else if (safeType === 'error') iconEl.className = 'bx bxs-error-circle';
    else iconEl.className = 'bx bxs-error';
    iconWrap.appendChild(iconEl);

    const contentEl = document.createElement('div');
    contentEl.className = 'neo-toast-content';
    contentEl.textContent = message ?? '';

    toast.append(iconWrap, contentEl);

    // Append to container
    container.appendChild(toast);

    // Trigger reflow to play entrance animation
    void toast.offsetWidth;
    toast.classList.add('show');

    // Auto remove after 3.5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        
        // Wait for exit animation to finish before removing from DOM
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 400); // Matches CSS transition duration
    }, 3500);
}

function NeoConfirm(title, text, confirmCallback) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'neo-modal-overlay';
    
    // Create modal box safely using textContent for title/body
    const modal = document.createElement('div');
    modal.className = 'neo-modal-box neo-box';

    const header = document.createElement('div');
    header.className = 'neo-modal-header';
    const icon = document.createElement('i');
    icon.className = 'bx bx-error-circle neo-modal-icon';
    const titleEl = document.createElement('h3');
    titleEl.className = 'neo-modal-title';
    titleEl.textContent = title ?? '';
    header.append(icon, titleEl);

    const body = document.createElement('div');
    body.className = 'neo-modal-body';
    const textEl = document.createElement('p');
    textEl.textContent = text ?? '';
    body.appendChild(textEl);

    const footer = document.createElement('div');
    footer.className = 'neo-modal-footer';
    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'neo-btn neo-btn-cancel';
    cancelBtn.textContent = 'Batal';
    const confirmBtn = document.createElement('button');
    confirmBtn.type = 'button';
    confirmBtn.className = 'neo-btn neo-btn-confirm';
    confirmBtn.textContent = 'Ya';
    footer.append(cancelBtn, confirmBtn);

    modal.append(header, body, footer);
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Trigger entrance animation
    setTimeout(() => {
        overlay.classList.add('show');
        modal.classList.add('show');
    }, 10);
    
    const closeModal = () => {
        overlay.classList.remove('show');
        modal.classList.remove('show');
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 300);
    };
    
    cancelBtn.addEventListener('click', closeModal);
    
    confirmBtn.addEventListener('click', () => {
        closeModal();
        if (typeof confirmCallback === 'function') {
            confirmCallback();
        } else if (typeof confirmCallback === 'string') {
            try {
                const parsed = new URL(confirmCallback, window.location.origin);
                if (parsed.origin === window.location.origin && (parsed.protocol === 'http:' || parsed.protocol === 'https:')) {
                    window.location.href = parsed.href;
                }
            } catch (e) {}
        }
    });
}

function NeoAlert(title, message, type = 'success') {
    const fullText = message ? `${title} — ${message}` : title;
    NeoToast(fullText, type);
}

