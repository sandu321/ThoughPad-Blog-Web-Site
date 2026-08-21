//  tab switch controll
/* used for clean dashboard single-page dynamic tab switching architecture */
function switchTab(tabId, menuItem) {
    const contents = document.querySelectorAll('.final-tab-panel');
    contents.forEach(content => content.classList.remove('active-panel'));
    
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => item.classList.remove('active'));
    
    const targetPanel = document.getElementById(tabId);
    if (targetPanel) targetPanel.classList.add('active-panel');
    
    if (menuItem) menuItem.classList.add('active');
    
    if (tabId !== 'profile-tab') {
        const url = new URL(window.location.href);
        url.searchParams.delete('tab');
        window.history.replaceState({}, '', url);
    } else {
        window.location.href = 'dashboard.php?tab=home';
    }
}
