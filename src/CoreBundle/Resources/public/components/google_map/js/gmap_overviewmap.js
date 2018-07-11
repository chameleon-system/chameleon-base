/*
 * GoogleMapsV3Overview Plug-in - javascript/jQuery plugin for creating an Overview map in Google Maps V3
 *
 * Extends the current google.maps.Map object
 * Requires jQuery >= 1.2 . See jquery.com for more details
 *
 * Author: David Coen, drcoen [at] gmail dot com
 * Copyright 2010 David Coen (drcoen.com)
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Version: r1
 * Date: 16 November 2010
 *
 * For details visit http://www.drcoen.com/
 *
 * This program isn't guaranteed to always work and is subject to changes in Google Maps.
 * The author takes no responsibility for any outcomes from using this code.
 *
 * Please feel free to update it and send on any improvements.
 *
 */

/*
 * Example - creates a google map object in a div with id "map", 200px * 200px, with a red box (instead of the default blue)
 *
 * var latlng = new google.maps.LatLng(53.276197,-9.0551384);
 * var map_options = {
 *   zoom: 14,
 *   center: latlng,
 *   mapTypeId: google.maps.MapTypeId.ROADMAP
 * }
 * map = new google.maps.Map(document.getElementById("map"), map_options);
 *
 * var overview_options = {
 *   rectangle_color: 'f00',
 *   box_height: 200,
 *   box_width: 200
 * }
 * map.Overview(overview_options);
 *
 */

/*
 * Options
 *
 * @option       zoom_difference
 * @type         int
 * @explanation  Difference between the main map zoom and the overview zoom
 * @default      3
 *
 * @option       border_color
 * @type         string (of valid css color code, without the #)
 * @explanation  Color of border around the overview box
 * @default      979797
 *
 * @option       rectangle
 * @type         bool
 * @explanation  Whether or not you want to display a rectangle in the overview
 *               map that represents the bounds of the main map
 * @default      true
 *
 * @option       rectangle_border_width
 * @type         int
 * @explanation  Width of the rectangle's border
 * @default      2
 *
 * @option       rectangle_color
 * @type         string (of valid css color code, without the #)
 * @explanation  Color of the rectangle
 * @default      '00f' (blue)
 *
 * @option       rectangle_opacity
 * @type         float (between 0.0 and 1.0, closer to one, the more opaque)
 * @explanation  Opacity of the fill of the rectangle
 * @default      0.3
 *
 * @option       up_img
 * @type         string (path to an image file)
 * @explanation  Image to be used for the arrow in the bottom right corner of the
 *               overview, when the overview is invisible. Should be same dimensions
 *               as down_img
 * @default      up.png (available when downloading)
 *
 * @option       down_img
 * @type         string (path to an image file)
 * @explanation  Image to be used for the arrow in the bottom right corner of the
 *               overview, when the overview is visible. Should be same dimensions
 *               as up_img
 * @default      down.png (available when downloading)
 *
 * @option       img_width
 * @type         int
 * @explanation  Width, in pixels, of up_img and down_img
 * @default      15
 *
 * @option       img_height
 * @type         int
 * @explanation  Height, in pixels, of up_img and down_img
 * @default      15
 *
 * @option       box_width
 * @type         int
 * @explanation  Width, in pixels, of your overview map
 * @default      150
 *
 * @option       box_height
 * @type         int
 * @explanation  Height, in pixels, of your overview map
 * @default      150
 *
 *
 */

google.maps.Map.prototype.Overview = function (options)
{
  // default settings
  var s = {
    zoom_difference: 3,
    border_color: '979797',
    background_color: 'e8ecf8',
    rectangle: true,
    rectangle_border_width: 2,
    rectangle_color: '00f',
    rectangle_opacity: 0.3,
    up_img: '/images/up.png',
    down_img: '/images/down.png',
    img_width: 15,
    img_height: 15,
    box_width: 150,
    box_height: 150,
    showinparent_map: true,
    maptype: google.maps.MapTypeId.ROADMAP
  };

  // overwrite defaults with any specified values
  if (options) {
    $.extend(s, options);
  }
  // a clear roadmap
  var sub_map_options = {
    zoom: this.getZoom() - s.zoom_difference,
    center: this.getCenter(),
    mapTypeId: s.maptype,
    mapTypeControl: false,
    navigationControl: false,
    scaleControl: false,
    streetViewControl: false,
    scrollwheel: false,
    disableDoubleClickZoom: true
  };
  var map = this;
  var map_div = map.getDiv();
  var client_width = map_div.clientWidth, client_height = map_div.clientHeight;
  var top_px, left;

  //work out required offsets to get Overview map in bottom right corner
  if ($.browser.msie && $.browser.version <= 8)
  {
    //special IE <= 7
    var bottom_border_width, left_border_width;
    bottom_border_width = parseInt($('#'+map_div.id).css('border-bottom-width'));
    if (isNaN(bottom_border_width))
      bottom_border_width = 0;
    left_border_width = parseInt($('#'+map_div.id).css('border-left-width'));
    if (isNaN(left_border_width))
      left_border_width = 0;
    // top_px = client_height - (s.box_height + 7 + bottom_border_width);
    top_px = client_height - (s.box_height + 1 + bottom_border_width);
    // left = client_width - (s.box_width + 7) + left_border_width; old version
    left = client_width;
  }
  else
  {
    if(s.showinparent_map){
      top_px = client_height - (s.box_height + 9 + parseInt($('#'+map_div.id).css('border-bottom-width')));
      left = client_width - (s.box_width + 9) + parseInt($('#'+map_div.id).css('border-left-width'));
    }else{
      top_px = client_height - (s.box_height + 1 + parseInt($('#'+map_div.id).css('border-bottom-width')));
      left = client_width ;
      $('#'+map_div.id).css({"overflow":"visible"});
    }
  }

  function OverviewMap(map) {
    this.setMap(map);
  }

  // OverviewMap extends Google's OVerlayView
  OverviewMap.prototype = new google.maps.OverlayView();

  // function to put the map in place
  OverviewMap.prototype.draw = function()
  {
    var div = this.div_;
    if (!div)
    {
      //create main outer div and add to map
      var div = document.createElement('DIV');
      if(s.showinparent_map){
        $(div).css({"position": "relative", "border-top": "1px solid #"+s.border_color, "border-left": "1px solid #"+s.border_color, "width": (s.box_width+2)+"px", "height": (s.box_height+2)+"px", "padding": "6px 0 0 6px", "top": top_px+"px", "left": left+"px", "background": "#"+s.background_color});
      }else{
        $(div).css({"position": "relative", "width": (s.box_width+2)+"px", "height": (s.box_height+2)+"px","top": top_px+"px", "left": left+"px"});
      }
      this.div_ = div;
      document.getElementById(map_div.id).appendChild(div);
      //create inner div, the one that will store the actual map
      var sub_div = document.createElement('DIV');
      $(sub_div).css({"width": s.box_width+"px", "height": s.box_height+"px", "border": "1px solid #"+s.border_color});
      $(sub_div).attr('id', 'sub_'+map_div.id);
      div.appendChild(sub_div);
      var sub_map = new google.maps.Map(sub_div, sub_map_options);
      //create the arrow controller
      var arrow_div = document.createElement('DIV');
      $(arrow_div).addClass('down');
      $(arrow_div).css({"padding": "0px", "height": s.img_height+"px", "width": s.img_width+"px", "position": "absolute", "top": (client_height-s.img_height)+"px", "left": (client_width-s.img_width)+"px", "cursor": "pointer"});
      $(arrow_div).html('<img src="'+s.down_img+'" alt="arrow" />');
      // document.getElementById(map_div.id).appendChild(arrow_div);

      //functionality for when you click on the arrow
      $(arrow_div).click(function () {
        // if it's pointing down
        if ($(this).hasClass('down'))
        {
          $(this).children().first().attr('src', 'up.png'); //swap the image
          $(this).removeClass('down'); //change the state
          $(this).addClass('up');
          $(div).hide(); //hide the map
        }
        else //it's pointing up
        {
          $(this).children().first().attr('src', 'down.png'); //swap the image
          $(this).removeClass('up'); //change the state
          $(this).addClass('down');
          $(div).show(); //show the map
        }
      });

      google.maps.event.addListenerOnce(sub_map, 'tilesloaded', function () {
	// draw the rectangle, if we want one
	if (s.rectangle == true)
	{
      if(!s.showinparent_map){
        $('#'+map_div.id).css({"overflow":"visible"});
      }
	  rectangle = new google.maps.Rectangle({
	    bounds: map.getBounds(),
	    fillColor: '#'+s.rectangle_color,
	    fillOpacity: s.rectangle_opacity,
	    strokeColor: '#'+s.rectangle_color,
	    strokeWeight: s.rectangle_border_width,
	    strokeOpacity: 1
	  });
	  rectangle.setMap(sub_map);
	  //whenever the main map changes, change the rectangle to reflect the change
	  google.maps.event.addListener(map, 'bounds_changed', function () {
	    if (map.getZoom() > s.zoom_difference) //if not too far zoomed out
	    {
	      if (!rectangle.getMap())
	      {
		rectangle.setMap(sub_map);
	      }
	      rectangle.setBounds(map.getBounds());
	    }
	    else //don't show rectangle
	    {
	      rectangle.setMap(null);
	    }
	  });
	}
	//sneaky trick to remove the Google logo in the Overview map
	$('#sub_'+map_div.id+' > div > div > a > div > img').remove();
      });

      //if the main map zoom changes, change the overview map
      google.maps.event.addListener(map, 'zoom_changed', function () {
        zoom = this.getZoom();
        if (zoom > s.zoom_difference)
        {
          sub_map.setZoom(this.zoom - s.zoom_difference);
        }
      });
      //if the main map zoom changes, change the overview map
      google.maps.event.addListener(map, 'center_changed', function () {
        sub_map.setCenter(this.getCenter());
      });

      //if the main map zoom changes, change the overview map
      google.maps.event.addListener(map, 'tilesloaded', function () {
        zoom = this.getZoom();
        if (zoom > s.zoom_difference)
        {
          sub_map.setZoom(this.zoom - s.zoom_difference);
        }
      });

      //if the main map drags, change the Overview map
      google.maps.event.addListener(map, 'drag', function () {
	sub_map.setCenter(this.getCenter());
      });

      //if the Overview map drags, change the main map
      google.maps.event.addListener(sub_map, 'drag', function () {
	map.setCenter(this.getCenter());
      });

      //disable double click on the Overview map
      google.maps.event.addListener(sub_map, 'dblclick', function () {
	this.setZoom(map.getZoom() - s.zoom_difference);
	this.setCenter(map.getCenter());
      });
    }
  }

  //create the map
  var sub = new OverviewMap(map);
}

