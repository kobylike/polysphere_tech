/**
 * Because the sidebar is wrapped in @persist('sidebar'), Blade only
 * evaluates request()->routeIs(...) on the very first hard page load.
 * On every subsequent wire:navigate click the persisted DOM node is
 * reused as-is, so the mm-active / mm-show classes go stale.
 *
 * This keeps them in sync with the current URL without touching
 * metismenu's own click handlers or Bootstrap's dropdown JS —
 * it only ever adds/removes classes metismenu already understands.
 */
function syncSidebarActiveState() {
    const menu = document.getElementById('menu');
    if (!menu) return;

    // Reset state
    menu.querySelectorAll('a').forEach((a) => a.classList.remove('mm-active'));
    menu.querySelectorAll('ul[aria-expanded]').forEach((ul) => {
        ul.classList.remove('mm-show');
        ul.setAttribute('aria-expanded', 'false');
    });

    const current = window.location.pathname + window.location.search;
    const currentPathOnly = window.location.pathname;

    // Prefer an exact match (path + query, for tabbed routes like /account/security)
    let link = [...menu.querySelectorAll('a[href]')].find(
        (a) => a.getAttribute('href') === current
    );

    // Fall back to path-only match
    if (!link) {
        link = [...menu.querySelectorAll('a[href]')].find((a) => {
            try {
                const url = new URL(a.href, window.location.origin);
                return url.pathname === currentPathOnly;
            } catch {
                return false;
            }
        });
    }

    if (!link) return;

    link.classList.add('mm-active');

    // Walk up and open every ancestor submenu + mark its toggle active
    let parentUl = link.closest('ul[aria-expanded]');
    while (parentUl && parentUl.id !== 'menu') {
        parentUl.classList.add('mm-show');
        parentUl.setAttribute('aria-expanded', 'true');

        const parentLi = parentUl.closest('li');
        const toggle = parentLi?.querySelector(':scope > a.has-arrow');
        if (toggle) {
            toggle.classList.add('mm-active');
            toggle.setAttribute('aria-expanded', 'true');
        }

        parentUl = parentLi?.parentElement?.closest('ul[aria-expanded]');
    }
}

document.addEventListener('DOMContentLoaded', syncSidebarActiveState);
document.addEventListener('livewire:navigated', syncSidebarActiveState);

export default syncSidebarActiveState;