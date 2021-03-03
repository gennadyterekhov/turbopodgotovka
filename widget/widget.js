(function () {
    var script = document.createElement('script');
    script.onload = function () {
        console.log("gigantSearchNearestShops ready");
    };
    
    // dev
    // script.src = 'https://f0ac5544866a.ngrok.io/widget/script.js';
    
    // test
    // script.src = 'https://test.dev.introvert.bz/gigant/search_nearest_shops/widget/script.js';
    
    // prod
    script.src = 'https://dev.introvert.bz/gigant/search_nearest_shops/widget/script.js';
    document.getElementsByTagName('head')[0].appendChild(script);
})();