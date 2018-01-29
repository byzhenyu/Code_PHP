var port = null;
var hostName = "com.lnkj.chrome.safe.echo";
var mac = {};
function connect() {
    port = chrome.runtime.connectNative(hostName);
    port.onMessage.addListener(onNativeMessage);
    port.onDisconnect.addListener(onDisconnected);
}

function onNativeMessage(message) {
    console.debug("nativemessage：%s",JSON.stringify(message));
    chrome.tabs.query({active: true}, function (tabs) {
        console.debug("tabs：%s",JSON.stringify(tabs));
        chrome.tabs.sendMessage(tabs[0].id, message, function (response) {
            console.debug("baground  respone");
        });
    });

}

function onDisconnected() {
    port = null;
}

chrome.runtime.onMessageExternal.addListener(
    function (request, sender, sendResponse) {
        if (request.requestType === "connect") {
            if (port === null) {
                connect();
            }
        }
        else {
            if (port === null) {
                connect();
            }
            else {
                if (!!mac.text) {
                    sendResponse(mac);
                }
            }
        }
    });

