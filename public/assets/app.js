(() => {
    'use strict';
    const root = document.documentElement;
    const labels = document.body.dataset;
    root.classList.remove('no-js');
    const connection = document.getElementById('connection-status');
    const saveStatus = document.querySelector('[data-save-status]');
    const originalSaveStatus = saveStatus?.textContent ?? '';
    let dirty = false;
    let submitting = false;

    const updateConnection = () => {
        if (connection) connection.hidden = navigator.onLine;
        if (!navigator.onLine && saveStatus) {
            saveStatus.textContent = dirty ? (labels.uiOffline || 'Offline · changes are not saved') : (labels.uiOfflineSaved || 'Offline · previous saved version retained');
            saveStatus.dataset.state = 'dirty';
            saveStatus.dataset.offline = 'true';
        } else if (navigator.onLine && saveStatus?.dataset.offline === 'true') {
            delete saveStatus.dataset.offline;
            saveStatus.textContent = dirty ? (labels.uiUnsaved || 'Changes not saved') : originalSaveStatus;
            if (dirty) saveStatus.dataset.state = 'dirty';
            else delete saveStatus.dataset.state;
        }
    };
    updateConnection();
    window.addEventListener('online', updateConnection);
    window.addEventListener('offline', updateConnection);

    const markDirty = () => {
        if (dirty) return;
        dirty = true;
        if (saveStatus) {
            saveStatus.textContent = labels.uiUnsaved || 'Changes not saved';
            saveStatus.dataset.state = 'dirty';
        }
    };
    const answerForm = document.querySelector('[data-answer-form]');
    answerForm?.addEventListener('input', markDirty);
    answerForm?.addEventListener('change', markDirty);
    window.addEventListener('beforeunload', (event) => {
        if (!dirty || submitting) return;
        event.preventDefault();
        event.returnValue = '';
    });

    document.querySelectorAll('[data-choice-group]').forEach((group) => {
        const inputs = [...group.querySelectorAll('input[type="checkbox"],input[type="radio"]')];
        const sync = () => inputs.forEach((input) => input.closest('.choice')?.classList.toggle('is-selected', input.checked));
        sync();
        group.addEventListener('change', (event) => {
            const selected = event.target;
            if (selected instanceof HTMLInputElement && selected.checked && group.dataset.choiceGroup === 'multiple') {
                if (selected.hasAttribute('data-exclusive')) inputs.forEach((input) => { if (input !== selected) input.checked = false; });
                else inputs.filter((input) => input.hasAttribute('data-exclusive')).forEach((input) => { input.checked = false; });
            }
            sync();
        });
    });

    document.querySelectorAll('form[data-submitting]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.inFlight === 'true') {
                event.preventDefault();
                return;
            }
            if (!navigator.onLine) {
                event.preventDefault();
                updateConnection();
                if (connection) { connection.hidden = false; connection.setAttribute('tabindex', '-1'); connection.focus(); }
                return;
            }
            submitting = true;
            form.dataset.inFlight = 'true';
            form.classList.add('is-submitting');
            form.setAttribute('aria-busy', 'true');
            const trigger = event.submitter;
            if (trigger instanceof HTMLButtonElement) {
                trigger.dataset.originalLabel = trigger.textContent;
                if (trigger.dataset.loadingText) trigger.textContent = trigger.dataset.loadingText;
                trigger.setAttribute('aria-disabled', 'true');
            }
            if (form.hasAttribute('data-answer-form') && saveStatus) {
                saveStatus.textContent = labels.uiSaving || 'Saving…';
                saveStatus.dataset.state = 'pending';
            }
            // Controls remain enabled so the browser includes the selected submitter's name/value.
            // The server's revision checks and idempotency key remain authoritative.
        });
    });

    window.addEventListener('pageshow', (event) => {
        const wasSubmitting = submitting;
        submitting = false;
        document.querySelectorAll('form[data-submitting]').forEach((form) => {
            delete form.dataset.inFlight;
            form.classList.remove('is-submitting');
            form.removeAttribute('aria-busy');
            form.querySelectorAll('button[data-original-label]').forEach((button) => {
                button.textContent = button.dataset.originalLabel;
                delete button.dataset.originalLabel;
                button.removeAttribute('aria-disabled');
            });
        });
        if (event.persisted && wasSubmitting && saveStatus) {
            dirty = true;
            saveStatus.textContent = labels.uiReviewSaved || 'Review the saved version before making another change';
            saveStatus.dataset.state = 'dirty';
        } else if (!dirty && saveStatus) {
            saveStatus.textContent = originalSaveStatus;
            delete saveStatus.dataset.state;
        }
        updateConnection();
    });

    const errors = document.querySelector('[data-focus-errors]');
    if (errors instanceof HTMLElement) errors.focus();
    else {
        const heading = document.querySelector('[data-question-heading]');
        if (heading instanceof HTMLElement) heading.focus({ preventScroll: true });
    }

    document.querySelectorAll('[data-copy-target]').forEach((button) => {
        button.addEventListener('click', async () => {
            const target = document.getElementById(button.dataset.copyTarget);
            const status = document.querySelector('[data-copy-status]');
            if (!target) return;
            try {
                await navigator.clipboard.writeText(target.textContent.trim());
                if (status) status.textContent = labels.uiCopySuccess || 'Recovery code copied. Keep it private.';
            } catch {
                if (status) status.textContent = labels.uiCopyFailed || 'Copy is unavailable. Select the displayed recovery code and copy it manually.';
            }
        });
    });
    document.querySelectorAll('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));

    if ('IntersectionObserver' in window) {
        const links = [...document.querySelectorAll('.report-nav a[href^="#"]')];
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                links.forEach((link) => {
                    const current = link.getAttribute('href') === '#' + entry.target.id;
                    link.classList.toggle('is-current', current);
                    if (current) link.setAttribute('aria-current', 'location');
                    else link.removeAttribute('aria-current');
                });
            });
        }, { rootMargin: '-10% 0px -65% 0px' });
        document.querySelectorAll('.report-section[id]').forEach((section) => observer.observe(section));
    }
})();
