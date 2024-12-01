window.onload = function () {
    const mainLogoContainer = document.getElementById('mainLogoContainer');
    const loginContainer = document.getElementById('fullHeight');
    loginContainer.style.display = 'none';

    setTimeout(() => {
        mainLogoContainer.style.display = 'none';
        loginContainer.style.display = 'flex';
    }, 4000);
};