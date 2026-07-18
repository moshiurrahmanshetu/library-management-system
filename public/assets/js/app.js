/**
 * Application JavaScript.
 *
 * Client-side helpers for form validation, SweetAlert feedback,
 * delete confirmation and permission check-all behavior.
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

            Swal.fire({
                title: 'Log out?',
                text: 'Are you sure you want to log out?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
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
    });

    /**
     * Delete confirmation for forms with the .delete-form class.
     */
    const deleteForms = document.querySelectorAll('.delete-form');

    Array.from(deleteForms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    /**
     * Module-level check-all for role permission assignment.
     */
    const checkAllModules = document.querySelectorAll('.check-all-module');

    Array.from(checkAllModules).forEach(function (checkAll) {
        checkAll.addEventListener('change', function () {
            const module = checkAll.getAttribute('data-module');
            const checkboxes = document.querySelectorAll('.permission-' + module);

            Array.from(checkboxes).forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
        });
    });

    /**
     * Show SweetAlert toast for success messages stored in a meta tag.
     */
    const successMessage = document.querySelector('meta[name="success-message"]');

    if (successMessage && typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: successMessage.getAttribute('content'),
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
})();
