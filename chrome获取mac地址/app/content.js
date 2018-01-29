chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
    console.debug("chrome.runtime.onMessage called");
    if (!!request.text) {
        console.debug("mac address:%s", request.text);
        var macEle = document.getElementById("mac");
        if (!!macEle) {
            macEle.value = request.text
        }
    }
});
console.log(1111);
