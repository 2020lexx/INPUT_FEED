/*
   All emon_widgets code is released under the GNU General Public License v3.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

    Author: Trystan Lea: trystan.lea@googlemail.com
    If you have any questions please get in touch, try the forums here:
    http://openenergymonitor.org/emon/forum
 */

function vis_widgetlist()
{
  var widgets = {
    "realtime":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid"],
      "optionstype":["loggerid"],
      "optionsname":[    "templ_var"],
      "optionshint":[    "templ_var source"],
      "html":""
    },

    "rawdata":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",  
      "options":["loggerid","colour","units","dp","scale","fill"],
      "optionstype":["loggerid","colour_picker","value","value","value","value"],
      "optionsname":[    "templ_var",    "Colour",    "units",    "dp",    "scale",    "Fill"],
      "optionshint":[    "templ_var source",    "Line colour in hex. Blank is use default.",    "units",    "Decimal points",    "Scale by",    "Fill value"],
      
      "html":""
    },
    
    "bargraph":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid","colour","interval","units","dp","scale","delta"],
      "optionstype":["loggerid","colour_picker","value","value","value","value","value"],
      "optionsname":[    "templ_var",    "Colour",    "interval",    "units",    "dp",    "scale",    "delta"],
      "optionshint":[    "templ_var source",    "Line colour in hex. Blank is use default.",    "Interval (seconds)-you can set \"d\" for day, \"m\" for month, or \"y\" for year",    "Units",    "Decimal points",    "Scale by",    "St to \"1\" to show diff between each bar. It displays an ever-increasing Wh templ_var as a daily\/montly\/yeayly kWh templ_var (set interval to \"d\", or \"m\", or \"y\")"],
      "html":""
    },

    "timestoredaily":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid","units"],
      "optionstype":["loggerid","value"],
      "optionsname":[    "templ_var",    "Units"],
      "optionshint":[    "templ_var source",    "Units to show"],
      "html":""
    },

    "zoom":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["power","kwhd","currency","currency_after_val","pricekwh"],
      "optionstype":["loggerid","loggerid","value","value","value"],
      "optionsname":[    "Power",    "kwhd",    "Currency",    "Currency position",    "Kwh price"],
      "optionshint":[    "Power to show",    "kwhd source",    "Currency to show",    "0 = before value, 1 = after value",    "Set kwh price"],
      "html":""
    },

    "simplezoom":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["power","kwhd"],
      "optionstype":["loggerid","loggerid"],
      "optionsname":[    "Power",    "kwhd"],
      "optionshint":[    "Power to show",    "kwhd source"],
      "html":""
    },

    "histgraph":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid"],
      "optionstype":["loggerid"],
      "optionsname":[    "templ_var"],
      "optionshint":[    "templ_var source"],
      "html":""
    },

    "threshold":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid","thresholdA","thresholdB"],
      "optionstype":["loggerid","value","value"],
      "optionsname":[    "templ_var",    "Threshold A",    "Threshold B"],
      "optionshint":[    "templ_var source",    "Threshold A used",    "Threshold B used"],
      "html":""
    },

    "orderthreshold":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid","power","thresholdA","thresholdB"],
      "optionstype":["loggerid","loggerid","value","value"],
      "optionsname":[    "templ_var",    "Power",    "Threshold A",    "Threshold B"],
      "optionshint":[    "templ_var source",    "Power",    "Threshold A used",    "Threshold B used"],
      "html":""
    },

    "orderbars":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid"],
      "optionstype":["loggerid"],
      "optionsname":[    "templ_var"],
      "optionshint":[    "templ_var source"],
      "html":""
    },

    "stacked":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["bottom","top"],
      "optionstype":["loggerid","loggerid"],
      "optionsname":[    "Bottom",    "Top"],
      "optionshint":[    "Bottom templ_var value",    "Top templ_var value"],
      "html":""
    },

    "stackedsolar":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["solar","consumption"],
      "optionstype":["loggerid","loggerid"],
      "optionsname":[    "Solar",    "Consumption"],
      "optionshint":[    "Solar templ_var value",    "Consumption templ_var value"],
      "html":""
    },

    "smoothie":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["loggerid","ufac"],
      "optionstype":["loggerid","value"],
      "optionsname":[    "templ_var",    "Ufac"],
      "optionshint":[    "templ_var source",    "Ufac value"],
      "html":""
    },

    "multigraph":
    {
      "offsetx":0,"offsety":0,"width":400,"height":300,
      "menu":"Visualisations",
      "options":["name_id"],
      "optionstype":["multigraph"],
      "optionsname":[    "name_id"],
      "optionshint":[    "name_id value"],
      "html":""
    }
  }

  // Gets multigraphs from vis_widget.php public multigraphs variable

  return widgets;
}

function vis_init()
{
  vis_draw();
}

function vis_draw()
{
  var vislist = vis_widgetlist();

  var visclasslist = '';
  for (z in vislist) { visclasslist += '.'+z+','; }

  visclasslist = visclasslist.slice(0, -1)

  $(visclasslist).each(function()
  {
    var id = $(this).attr("id");
    var templ_var = $(this).attr("templ_var") || 0;
    var width = $(this).width();
    var height = $(this).height();

    var attrstring = "";
    var target = $(this).get(0);
    var l = target.attributes.length
    for (var i=0; i<l; i++)
    {
      var attr = target.attributes[i].name;
      if (attr!="id" && attr!="class" && attr!="style")
      {
        attrstring += "&"+attr+"="+target.attributes[i].value;
      }
    }

    var apikey_string = "";
    if (apikey) apikey_string = "&apikey="+apikey;
    if (!$(this).html() || reloadiframe==id || apikey){

      console.log("here:"+attrstring);

      $(this).html('<iframe style="width:'+width+'px; height:'+height+'px;" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+path_embed+'?graph='+$(this).attr("class")+attrstring+apikey_string+'"></iframe>');
    }

    var iframe = $(this).children('iframe');
    iframe.width(width);
    iframe.height(height);

  });
reloadiframe = 0;
}

function vis_slowupdate()
{
  // Are these supposed to be empty?
}

function vis_fastupdate()
{

}



