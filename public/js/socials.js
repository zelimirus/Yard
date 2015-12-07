function initialize() {

	myCenter=new google.maps.LatLng(43.455219, 21.104003);

    var mapOptions = {
	    center:myCenter,
	    zoom:14,
	    streetViewControl: false,
        mapTypeControlOptions: { mapTypeIds: [] }
    };

	map = new google.maps.Map(document.getElementById("googleMap"),mapOptions);

	var marker=new google.maps.Marker({
	  	position:myCenter,
	  	//icon: "/images/sr_rs/1170/marker.png",
	});
	marker.setMap(map);

/*	var myLabelOptions = {
		 content: "Zupska AVLIJA",
		 boxStyle: {
		  textAlign: "center"
		  ,fontSize: "10pt"
		  ,color: "#FF0000"
		  ,width: "100px"
		 }
		,disableAutoPan: true
		,pixelOffset: new google.maps.Size(-25, 0)
		,position: myCenter
		,closeBoxURL: ""
		,isHidden: false
		,pane: "mapPane"
		,enableEventPropagation: true
	};
	var ibLabel = new InfoBox(myLabelOptions);
	ibLabel.open(map);*/
}

google.maps.event.addDomListener(window, 'load', initialize);

window.fbAsyncInit = function() {
    FB.init({
      	appId      : '',
      	xfbml      : true,
      	version    : 'v2.2'
    });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/sr_RS/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));