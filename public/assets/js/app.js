/**
 * Application JavaScript.
 *
 * Small client-side helpers for form validation feedback and UI behavior.
 */

(function () {
    'use strict';

    /**
     * Bootstrap form validation enhancement.
     */
    const forms = document.querySelectorAll('form');

    Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });

    /**
     * Confirm logout actions (if any link-based logout is used).
     */
    const logoutLinks = document.querySelectorAll('[data-logout]');

    Array.from(logoutLinks).forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            if (confirm('Are you sure you want to log out?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = link.getAttribute('href');

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = 'csrf_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
})();
