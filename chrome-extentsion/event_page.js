chrome.webRequest.onHeadersReceived.addListener(
    function(details) {
        var sleep=function (milliseconds) {
          var start = new Date().getTime();
          for (var i = 0; i < 1e7; i++) {
            if ((new Date().getTime() - start) > milliseconds){
              break;
            }
          }
        };
        var xmlId=/hdviet\.com\/(.*?)\.xml/.exec(details.url)[1];
        var clientId = Math.random().toString(36).slice(2);
        chrome.tabs.executeScript(null,{code: "sendXmlToServer("+xmlId+",'"+clientId+"')"});
        sleep(3*1000);
        newUrl=REDIRECT_HOST+"/getXml.php?xml_id="+xmlId+"&client_id="+clientId;
        return {
            redirectUrl: newUrl
        };
    }, 
    {
        urls: ["http://movies.hdviet.com/*.xml"]
    }, 
    ["blocking"]
);