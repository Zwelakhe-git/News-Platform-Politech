window.addEventListener('load', ()=>{
    let navigation = performance.getEntriesByType('navigation')[0];
    if(navigation?.type === 'reload'){
        console.log('page reloaded. proceeding...');
    }
});

window.addEventListener('beforeunload', (event) => {
    sessionStorage.setItem('isReload', 'true');
    
    // Для показа диалога подтверждения (работает в современных браузерах)
    event.preventDefault();
    event.returnValue = 'Вы действительно хотите покинуть страницу?';
    return 'Вы действительно хотите покинуть страницу?';
});