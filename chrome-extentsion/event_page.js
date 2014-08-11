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
        var xmlId=/\/([^\/]*?)\.xml/.exec(details.url)[1];
        if(xmlId!="crossdomain"){
            var clientId = Math.random().toString(36).slice(2);
            chrome.tabs.executeScript(null,{code: "sendXmlToServer('"+xmlId+"','"+clientId+"')"});
            sleep(3*1000);
            newUrl=REDIRECT_HOST+"/getXml.php?xml_id="+xmlId+"&client_id="+clientId;
            return {
                redirectUrl: newUrl
            };
        }
        return;
    }, 
    {
        urls: ["*://*/*.xml"]
    }, 
    ["blocking"]
);