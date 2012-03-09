function initTemplate(){
    setTimeout('resizeContent()', 0);
    togglePanel();
}
function togglePanel(){
    var pnlRight = document.getElementById('pnlRight');
    var showpanel = document.getElementById('showpanel');
    if(pnlRight.style.display=="none"){
        pnlRight.style.display="block";
        showpanel.innerHTML = "Ukryj panel";
    }else{
        pnlRight.style.display="none";
        showpanel.innerHTML = "Pokaż panel";
    }
}
function resizeContent() { 
    var container = document.getElementById('container');
    var pnlCenter = document.getElementById('pnlCenter');
    var pnlLeft = document.getElementById('pnlLeft');
    var pnlRight = document.getElementById('pnlRight');
    var pnlMenu = document.getElementById('sidebar_menu');
    var footer = document.getElementById('footer');
    var appinfo = document.getElementById('app_info');
    var showpanel = document.getElementById('showpanel');
		
    // This may need to be done differently on IE than FF, but you get the idea. 
    var h = window.innerHeight;
    var w = window.innerWidth; 
		
    container.style.width = w - 20 + 'px';
    container.style.height = h + 'px';
    footer.style.width = container.clientWidth + 'px';
    pnlLeft.style.height = container.clientHeight - footer.clientHeight + 'px';
    pnlMenu.style.height = container.clientHeight -
    appinfo.clientHeight -
    footer.clientHeight - 10 + 'px';
    pnlCenter.style.height = container.clientHeight - footer.clientHeight + 'px';
    pnlCenter.style.width = container.clientWidth - pnlLeft.clientWidth - 10 + 'px';
    pnlRight.style.top = showpanel.clientHeight + 'px';
}