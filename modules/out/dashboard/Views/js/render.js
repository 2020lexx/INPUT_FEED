/*
All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org

  render.js goes through all the dashboard html elements that specify the dashboard widgets
  and inserts the dials, visualisations to be displayed inside the element.
  see designer.js for more information on the html element widget box model.

  render.js calls the render scripts of all the widgets which is where all the 
  individual widget render code is located.
*/

// Global page vars definition

// Array that holds ID's of feeds of associative key
var loggerids = [];
// Array for exact values
var assoc = [];
// Array for smooth change values - creation of smooth dial widget
var assoc_curve = [];
var widgetcanvas = {};

var dialrate = 0.02;
var browserVersion = 999;

var Browser =
{
  Version : function()
  {
    var version = 999;
    if (navigator.appVersion.indexOf("MSIE") != -1)
      version = parseFloat(navigator.appVersion.split("MSIE")[1]);
    return version;
  }
}

function show_dashboard()
{
  browserVersion = Browser.Version();
  if (browserVersion < 9) dialrate = 0.2;

  for (z in widget)
  {
    var fname = widget[z]+"_init";
    var fn = window[fname];
    fn();
  } 
   update();
}

// update function
function update()
{
  


  var query = path + "?c_=l&apikey=" + apikey;
  

  $.ajax(
  {
    url : query,
    dataType : 'json',
    success : function(data)
    { 
    
      for (z in data)
      {
         var newstr = data[z]['logger_log_file_id'];
       
       var newstr = data[z]['templ_var'];
        
       
    
        var value = parseFloat(data[z]['value']);
        console.log("newstr:"+newstr+"  value:"+value);

        $("." + newstr).html(value);
        assoc[newstr] = value * 1;
        loggerids[newstr] = data[z]['id'];

        
      }

      for (z in widget)
      {
        var fname = widget[z]+"_slowupdate";
        var fn = window[fname];
        fn();
      }
    }
  });
}

function fast_update()
{
  if (redraw)
  { 
    for (z in widget)
    {
      var fname = widget[z]+"_init";
      var fn = window[fname];
      fn();
    }

  }

  for (z in widget)
  {
    var fname = widget[z]+"_fastupdate";
    var fn = window[fname];
    fn();
  }
    redraw = 0;
}

function curve_value(logger,rate)
{
  var val = 0;
  if (logger) {
    if (!assoc_curve[logger]) assoc_curve[logger] = 0;
    assoc_curve[logger] = assoc_curve[logger] + ((parseFloat(assoc[logger]) - assoc_curve[logger]) * rate);
    val = assoc_curve[logger] * 1;
  }
  return val;
}

function setup_widget_canvas(elementclass)
{
  $('.'+elementclass).each(function(index)
  {
    var widgetId = $(this).attr("id");

    var width = $(this).width();
    var height = $(this).height();
    var canvas = $(this).children('canvas');

    var canvasid = "can-"+widgetId;
    // 1) Create canvas if it does not exist
    if (!canvas[0])
    {
      $(this).html('<canvas id="'+canvasid+'"></canvas>');
    }

    // 2) Resize canvas if it needs resizing
    if (canvas.attr("width") != width) canvas.attr("width", width);
    if (canvas.attr("height") != height) canvas.attr("height", height);

    var canvas = document.getElementById(canvasid);
    if (browserVersion != 999)
    {
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
      if ( typeof G_vmlCanvasManager != "undefined") G_vmlCanvasManager.initElement(canvas);
    }
    // 3) Get and store the canvas context
    widgetcanvas[canvasid] = canvas.getContext("2d");
  });
}
