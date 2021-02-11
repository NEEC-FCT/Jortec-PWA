importScripts('https://storage.googleapis.com/workbox-cdn/releases/5.1.2/workbox-sw.js');

//Workbox Config
workbox.setConfig({
  debug: false //set to true if you want to see SW in action.
});

//Caching Everything Inside the Folder of our Item
workbox.routing.registerRoute(
  new RegExp('.*'),
  new workbox.strategies.NetworkOnly()
);

workbox.routing.setCatchHandler(({ url, event, params }) => {
  console.log("Failed: ", url, event, params)
  const strategy = new workbox.strategies.NetworkFirst({ networkTimeoutSeconds: 10 });
  return strategy.handle({
    request: new Request(url),
  });
});


//console.log('Sticky Service Worker Running');

//Learn more about Service Workers and Configurations
//https://developers.google.com/web/tools/workbox/