<div id="meetup_everywherer_widget">
  <div id="map_canvas" style="color: #000; width: <?php echo $inst['width'];?>px; height: <?php echo $inst['height'];?>px"></div>
</div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  jQuery(document).ready(
    function () {
      //getGeo();
      initialize();
    }
  );
  window.gmap_meetupeverywhere;
  function getGeo () {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          window.currentGeoLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
          initialize();
        },
        function() {
          window.currentGeoLocation = null;
          initialize();
        }
      );
    }
    // Try Google Gears Geolocation
    else if (google.gears) {
      var geo = google.gears.factory.create('beta.geolocation');
      geo.getCurrentPosition(
        function(position) {
          window.currentGeoLocation = new google.maps.LatLng(position.latitude,position.longitude);
          initialize();
        },
        function() {
          window.currentGeoLocation = null;
          initialize();
        }
      );
    }
    // Browser doesn't support Geolocation
    else {
      window.currentGeoLocation = null;
      initialize();
    }
  };

  function initialize() {
    var latlng;
    if (window.currentGeoLocation) {
      latlng = new google.maps.LatLng(window. currentGeoLocation);
    }
    else if (events.length > 0) {
      latlng = new google.maps.LatLng(events[0].lat, events[0].lon);
    }
    else {
      // new york
      latlng = new google.maps.LatLng(40.744309,-73.94186);
    }
    var myOptions = {
      zoom: <?php echo (empty($inst['zoom']) ? '8' : $inst['zoom']); ?>,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    gmap_meetupeverywhere = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    plotEvent();
  };

var events = <?php echo json_encode($events); ?>;

function plotEvent () {
  var muev;
  for (var i = 0, len = events.length; i < len; ++i) {
    muev = events[i];
    muev.marker = new google.maps.Marker(
      {
        position: new google.maps.LatLng(muev.lat, muev.lon),
        map: gmap_meetupeverywhere,
        title: muev.city
      }
    );
    muev.info = new google.maps.InfoWindow();
    muev.info.setContent( genConent(muev) );
    muev.info.setPosition(muev.marker.LatLng);
    google.maps.event.addListener(
      muev.marker,
      'click',
      genFunc(muev)
    );
  }
};

function genConent (muev) {
  var arr = jQuery('<span><a href="' + muev.meetup_url + '">' + muev.city + '</a></span>');
  return arr[0];
};

window.currentOpenWindow = null;

function genFunc (muev) {
  return function (ev) {
    if (window.currentOpenWindow) {
      window.currentOpenWindow.close();
      window.currentOpenWindow = null;
    }
    muev.info.open(gmap_meetupeverywhere, muev.marker);
    window.currentOpenWindow = muev.info;
  };
};

</script>
