function getWinSize() {
    if (window.innerWidth) {
        winWidth = window.innerWidth;
    } else if (document.body && document.body.clientWidth) {
        winWidth = document.body.clientWidth;
    }

    if (window.innerHeight) {
        winHeight = window.innerHeight;
    } else if (document.body && document.body.clientHeight) {
        winHeight = document.body.clientHeight;
    }
    // 深入document内部对body进行探测，获取窗口大小
    if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth) {
        winHeight = document.documentElement.clientHeight;
        winWidth = document.documentElement.clientWidth;
    }
    //alert("width： "+winWidth+" height: "+winHeight);
}
function setFontSize() {
    var fontE1 = document.createElement("style");
    fontSize = winWidth / 750 * 40;
    fontE1.innerHTML = "html{font-size:" + fontSize + "px!important;}";
    document.documentElement.firstElementChild.appendChild(fontE1);
};
getWinSize();
setFontSize();