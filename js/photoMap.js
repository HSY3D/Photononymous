var map;
var usrLat;
var usrLng;
var nearbyPhotos = [];

function get_photos(callback)
{
	var keyReq = new XMLHttpRequest();
	
	keyReq.open("POST", "http://cs.mcgill.ca/~hsyed2/organized/php/getPhotos.php", true);
	
	//Send HTTP request to server
	keyReq.onreadystatechange = function() 
	{
	  if( keyReq.readyState == 4 && keyReq.status == 200)
	  {
	    var res = keyReq.responseText; //Store shared key in var
	    //This will ensure native JSON.parse is used immediately, rather than having jQuery perform sanity checks on the string before passing it to the native parsing function
	    nearbyPhotos = JSON && JSON.parse(res) || $.parseJSON(res);
	    callback(nearbyPhotos);
	  }
	}
	keyReq.send();
}

window.onload = function()
{
	get_photos( function(photoArr)
	{
		nearbyPhotos = photoArr;
		get_loc();	 
	});
};

function show_loc(loc)
{
	var usrLat = loc.coords.latitude;
	var usrLng = loc.coords.longitude;
	
	document.getElementById("lat").value = usrLat;
	document.getElementById("lng").value = usrLng;

	google.maps.event.addDomListener(window, 'load', initialize(usrLat, usrLng, map));
}

function initialize(lat, lng, map)
{
	var mapOpts = 
	{
		center: { lat: lat, lng: lng },
				zoom: 18,
				disableDefaultUI: true,
				zoomControl: true,
				scrollwheel: false,
				zoomControlOptions: {
					style: google.maps.ZoomControlStyle.LARGE
				}
	};

	map = new google.maps.Map(document.getElementById('map-canvas'), mapOpts);

	google.maps.event.addListenerOnce(map, 'tilesloaded', function()
	{
		insert_markers(map);
	});

	google.maps.event.addListener(map, 'bounds_changed', function()
	{
		insert_markers(map);
	});
}

function insert_markers(map)
{
	for( var i = 0; i < nearbyPhotos.length; i++ )
	{
		var photoLat = nearbyPhotos[i].Latitude;
		var photoLng = nearbyPhotos[i].Longitude;
		var latLng = new google.maps.LatLng(photoLat, photoLng);

		if( map.getBounds().contains(latLng) )
		{
			var photo = nearbyPhotos[i].PhotoID;
			//var filePath = "http://cs.mcgill.ca/~hsyed2/organized/images/" + photo + ".jpg";
			var filePath = nearbyPhotos[i].Path;
			var upVotes = nearbyPhotos[i].Upvotes;
			var downVotes = nearbyPhotos[i].Downvotes;
			var voteCount = upVotes - downVotes;
			//var markerTitle = "Upvotes: " + upVotes + "\nDownvotes: " + downVotes;

			//This is pretty ugly, but it works lol...#webdevelopment
			//Will try to find better solution if time permits before demo
			var openDiv = "<div>";
			var voteDiv = "<div style='display: inline; float: right;'><span class='upVote'></span>\
							<h2 id='votes' style='margin-top: 0px; margin-bottom: 0px;'>";
			var endVoteDiv = "</h2><span class='downVote'></span></div>";
			var imgDiv = "<div style='display: inline; margin-right: 5px;'><img src='" + filePath + "' style='width: 95%; vertical-align: top;'></div></div>";
			var voteType = "null";
			var hasVoted = false;
			var voteSent = false;

			var image = 
			{
				url: filePath,
				scaledSize: new google.maps.Size(75, 75),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(0, 0)
			};

			var shape =
			{
				coords: [0, 0, 75, 75],
				type: 'rect'
			};

			var marker = new google.maps.Marker(
			{
				position: latLng,
				map: map,
				icon: image,
				shape: shape,
				//animation: google.maps.Animation.DROP,
				content0: openDiv,
				content1: voteDiv,
				voteCount: voteCount,
				content2: endVoteDiv,
				content3: imgDiv,
				voteType: voteType,
				hasVoted: hasVoted,
				voteSent: voteSent,
				upVotes: upVotes,
				downVotes: downVotes,
				//title: "Upvotes: " + this.upVotes + "\nDownvotes: " + this.downVotes
			});

			var infoWindow = new google.maps.InfoWindow();

			( function(mark, info, pic)
			{
				mark.setTitle("Upvotes: " + mark.upVotes + "\nDownvotes: " + mark.downVotes);

				google.maps.event.addListener(marker, 'click', function()
				{
					map.setCenter(mark.getPosition());
					info.setContent(mark.get('content0') + mark.get('content1') + mark.get('voteCount') + mark.get('content2') + mark.get('content3'));
					info.open(map, mark);
		  		});

		  		google.maps.event.addListener(info, 'domready', function()
		  		{
			  		$('.upvote').click( function()
			  		{
				  		if( mark.hasVoted == false )
			  			{
					  		$(this).toggleClass('on');

					  		mark.upVotes++;
							mark.voteCount++;
							document.getElementById('votes').innerHTML = mark.voteCount;
							mark.setTitle("Upvotes: " + mark.upVotes + "\nDownvotes: " + mark.downVotes);

							mark.voteType = "up";
							mark.content1 = "<div style='float: right;'><span class='upVote on'></span>\
											<h2 id='votes' style='margin-top: 0px; margin-bottom: 0px;'>";

							mark.hasVoted = true;
							console.log("hadVoted2: " + mark.hasVoted);
						}
					});

					$('.downvote').click( function()
					{
						if( mark.hasVoted == false )
						{
							$(this).toggleClass('on');

							mark.downVotes++;
							mark.voteCount--;
						  	document.getElementById('votes').innerHTML = mark.voteCount;
						  	mark.setTitle("Upvotes: " + mark.upVotes + "\nDownvotes: " + mark.downVotes);

							mark.voteType = "down";
							mark.content2 = "</h2><span class='downVote on'></span></div>";

							mark.hasVoted = true;
						}
					});
				});

				google.maps.event.addListener(info, 'closeclick', function()
				{
					if( mark.hasVoted == true && mark.voteSent == false )
					{
						//Construct an HTTP request to get shared key for this client
						var keyReq = new XMLHttpRequest();

						keyReq.open("POST", "http://cs.mcgill.ca/~hsyed2/organized/php/updateVoteCount.php?photo=" + pic + "&voteType=" + mark.voteType, true);

						//Send HTTP request to server with client username and execute script that returns the shared key
						keyReq.onreadystatechange = function()
						{
						  mark.voteSent = true;
						}
						keyReq.send();
					}
				});
			})(marker, infoWindow, photo);
		}
	};
}

function show_error(err)
{
	if( err.code == 1 )
	{
		alert( "Permission Denied" );
	}
	else if( err.code == 2 )
	{
		alert( "Position Unavailable" );
	}
	else if( err.code == 3 )
	{
		alert( "Timeout" );
	}
	else
	{
		alert( "An unknown error occurred." );
	}
}

function get_loc()
{
	if( Modernizr.geolocation )
	{
		navigator.geolocation.getCurrentPosition(show_loc, show_error);
	}
	else
	{
		if( geoPosition.init() )
		{
			geoPosition.getCurrentPosition(show_loc, show_error);
		}
		else
		{
			alert("Sorry. Your browser doesn't support geolocation.");
		}
	}
}
