chrome.webRequest.onHeadersReceived.addListener(
    function(details) {
        newUrl=details.url.replace("movies.hdviet.com/","127.0.0.1/hdvietinfo.php?encode=true&xmlcode=");
        return {
            redirectUrl: newUrl
        };
    }, 
    {
        urls: ["http://movies.hdviet.com/*.xml"]
    }, 
    ["blocking"]
);