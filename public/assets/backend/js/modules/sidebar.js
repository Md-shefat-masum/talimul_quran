(function (document) {
    'use strict';

    var root = document.querySelector('[data-sidebar-root]');

    if (!root) {
        return;
    }

    function directParentItem(item) {
        var parentList = item.parentElement;

        return parentList ? parentList.closest('[data-sidebar-item]') : null;
    }

    function isTopLevel(item) {
        return Number(item.dataset.sidebarLevel || 0) === 0;
    }

    function setOpen(item, open) {
        item.classList.toggle('is-open', open);
        item.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function closeBranch(item) {
        setOpen(item, false);
        item.querySelectorAll('[data-sidebar-item].is-open').forEach(function (child) {
            setOpen(child, false);
        });
    }

    function closeOtherTopLevelItems(activeItem) {
        root.querySelectorAll('[data-sidebar-item][data-sidebar-level="0"].is-open').forEach(function (item) {
            if (item !== activeItem) {
                closeBranch(item);
            }
        });
    }

    function ensureActiveChainOpen() {
        root.querySelectorAll('[data-sidebar-item].is-active').forEach(function (item) {
            var current = item;

            while (current) {
                setOpen(current, true);
                current = directParentItem(current);
            }
        });

        var activeTop = root.querySelector('[data-sidebar-item][data-sidebar-level="0"].is-active');
        if (activeTop) {
            closeOtherTopLevelItems(activeTop);
        }
    }

    function scrollActiveLeafIntoView() {
        var container = root.querySelector('.permission_menues');
        var activeLeaf = root.querySelector('[data-sidebar-item].is-exact-active');

        if (!container || !activeLeaf) {
            return;
        }

        var containerRect = container.getBoundingClientRect();
        var activeRect = activeLeaf.getBoundingClientRect();

        if (activeRect.top >= containerRect.top && activeRect.bottom <= containerRect.bottom) {
            return;
        }

        container.scrollTop += activeRect.top - containerRect.top - 18;
    }

    root.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-sidebar-toggle]');

        if (!toggle || !root.contains(toggle)) {
            return;
        }

        var item = toggle.closest('[data-sidebar-item]');
        if (!item) {
            return;
        }

        var shouldOpen = !item.classList.contains('is-open');

        if (isTopLevel(item) && shouldOpen) {
            closeOtherTopLevelItems(item);
        }

        setOpen(item, shouldOpen);
    });

    document.querySelectorAll('[data-toggle="offcanvas"]').forEach(function (button) {
        button.addEventListener('click', function () {
            root.classList.toggle('active');
        });
    });

    ensureActiveChainOpen();
    window.requestAnimationFrame(scrollActiveLeafIntoView);
})(document);
