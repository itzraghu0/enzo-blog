(function ($) {
    'use strict';

    function storageGet(key) {
        try {
            return localStorage.getItem(key);
        } catch (error) {
            return null;
        }
    }

    function storageSet(key, value) {
        try {
            localStorage.setItem(key, value);
        } catch (error) {
            // Ignore browser storage restrictions.
        }
    }

    function setTheme(theme) {
        $('html').toggleClass('dark', theme === 'dark');
        storageSet('theme', theme);
    }

    if (storageGet('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    }

    if (!$) {
        document.addEventListener('DOMContentLoaded', function () {
            window.toggleTheme = function () {
                var nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
                document.documentElement.classList.toggle('dark', nextTheme === 'dark');
                storageSet('theme', nextTheme);
            };
        });

        return;
    }

    $(function () {
        var $window = $(window);
        var $document = $(document);
        var $progress = $('#reading-progress');

        function updateProgress() {
            if (!$progress.length) {
                return;
            }

            var scrollTop = $window.scrollTop();
            var scrollHeight = $(document).height() - $window.height();
            var width = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;

            $progress.css('width', width + '%');
        }

        function closeDropdowns() {
            $('[data-site-dropdown].is-open').each(function () {
                $(this)
                    .removeClass('is-open')
                    .find('[data-site-dropdown-toggle]')
                    .attr('aria-expanded', 'false');
            });
        }

        window.toggleTheme = function () {
            setTheme($('html').hasClass('dark') ? 'light' : 'dark');
        };

        $window.on('scroll', updateProgress);
        updateProgress();

        $document.on('click', '[data-site-dropdown-toggle]', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $toggle = $(this);
            var $dropdown = $toggle.closest('[data-site-dropdown]');
            var isOpen = $dropdown.hasClass('is-open');

            closeDropdowns();

            $dropdown.toggleClass('is-open', !isOpen);
            $toggle.attr('aria-expanded', String(!isOpen));
        });

        $document.on('click', '[data-theme-option]', function () {
            var $button = $(this);

            setTheme($button.data('theme-option') || 'light');
            $button.closest('[data-site-dropdown]').removeClass('is-open');
        });

        $document.on('click', '[data-submit-form]', function () {
            var formId = $(this).data('submit-form');
            var form = formId ? document.getElementById(formId) : null;

            if (form) {
                form.submit();
            }
        });

        $document.on('click', function () {
            closeDropdowns();
        });
    });
})(window.jQuery);
