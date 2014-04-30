var tooltip = CustomTooltip("posts_tooltip", 240);
var array;
var m = [20, 100, 20, 100],
    w = 1280 - m[1] - m[3],
    h = 800 - m[0] - m[2],
    i = 0,
    root;
	
//var mailTo = ContactPop();

var tree = d3.layout.tree()
    .size([h, w]);

var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });

var vis = d3.select("#myGraph").append("svg:svg")
    .attr("width", w + m[1] + m[3])
    .attr("height", h + m[0] + m[2])
  .append("svg:g")
    .attr("transform", "translate(" + m[3] + "," + m[0] + ")");

d3.json("TEST_Flare.json", function(json) {
  root = json;
  root.x0 = h / 2;
  root.y0 = 0;

  function toggleAll(d) {
    if (d.children) {
      d.children.forEach(toggleAll);
      toggle(d);
    }
  }

  // Initialize the display to show a few nodes.  
  root.children.forEach(toggleAll);
  update(root);
});

function update(source) {
  var duration = d3.event && d3.event.altKey ? 5000 : 500;

  // Compute the new tree layout.
  var nodes = tree.nodes(root).reverse();	  
  
  // Normalize for fixed-depth.
  nodes.forEach(function(d) { d.y = d.depth * 180; });

  // Update the nodes…
  var node = vis.selectAll("g.node")
      .data(nodes, function(d) { return d.id || (d.id = ++i); });

  // Enter any new nodes at the parent's previous position.
  var nodeEnter = node.enter().append("svg:g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
      .on("click", function(d) { toggle(d); update(d); });

  nodeEnter.append("svg:circle")
      .attr("r", 1e-6)
      .style("fill", function(d) { return d._children ? "#04D120" : "#fff"; })
	  .on("mouseover", function(d, i){highlight(d, i, this);})
      .on("mouseout", function(d, i) { downlight( d, i, this );});

	  
  
  nodeEnter.append("svg:text")
      .attr("x", function(d) { return d.children || d._children ? -10 : 10; })
      .attr("dy", ".35em")
      .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
      .text(function(d) { return d.name; })
      .style("fill-opacity", 1e-6);
	  

  // Transition nodes to their new position.
  var nodeUpdate = node.transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

  nodeUpdate.select("circle")
      .attr("r", 32) //4.5
      .style("fill", function(d) { return d._children ? "#04D120" : "#fff"; });

  nodeUpdate.select("text")
      .style("fill-opacity", 1);

  // Transition exiting nodes to the parent's new position.
  var nodeExit = node.exit().transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
      .remove();

  nodeExit.select("circle")
      .attr("r", 1e-6);

  nodeExit.select("text")
      .style("fill-opacity", 1e-6);

  // Update the links…
  var link = vis.selectAll("path.link")
      .data(tree.links(nodes), function(d) { return d.target.id; });

  // Enter any new links at the parent's previous position.
  link.enter().insert("svg:path", "g")
      .attr("class", "link")
      .attr("d", function(d) {
        var o = {x: source.x0, y: source.y0};
        return diagonal({source: o, target: o});
      })
	.transition()
      .duration(duration)
	  .attr("d", diagonal);

  // Transition links to their new position.
  link.transition()
      .duration(duration)
      .attr("d", diagonal);

  // Transition exiting nodes to the parent's new position.
  link.exit().transition()
      .duration(duration)
      .attr("d", function(d) {
        var o = {x: source.x, y: source.y};
        return diagonal({source: o, target: o});
      })
      .remove();

  // Stash the old positions for transition.
  nodes.forEach(function(d) {
    d.x0 = d.x;
    d.y0 = d.y;
  });
  
  
  //FUNCTION TO ADD MOUSEOVER TO ALL APPROPRIATE NODES
  //addMouseOver();
}

// Toggle children.
function toggle(d) {
  if (d.children) {
    d._children = d.children;
    d.children = null;
  } else {
    d.children = d._children;
    d._children = null;
  }
}



//-----------------------------------------------------------------------------------
//method for mousouver
function highlight(data, i, element){

	d3.select( element )
	  .style("stroke-width", "3")
	  .style("stroke", "red");

		var content;
		
		//Console.log(data);
		if(data.depth === 0 || data.depth === 2)
		{
			tooltip.hide();
		}
		
		else if(data.depth === 1)
		{
			content = '<p><b>' + data.name + '</b>  ---  ' + data.description + '</p>';
		}
		
		else if(data.depth === 3)
		{
			resetContent();
			content = '<p><b>'+data.name+'</b><p><span class=\"description\">' + data.description.substring(0, 95) + '</span>'+'...'+' <a href=\"#myModal"\ data-toggle="modal" onclick=\"getDescription(\''+data.description+'\' , \''+data.name+'\');\">Read More</a>';
		}

		else
		{
			content = '<a href=\"#emailForm"\ data-toggle="modal" onclick=\"getEmail(\''+data.email+'\');\">'+data.email+'</a>';
		}


		tooltip.showTooltip(content, d3.event);
}

function getDescription($pd, $title)
{
	var header = document.getElementById('myModalLabel');
	header.innerHTML = header.innerHTML+ $title;
	
	var div = document.getElementById('projContent');
	div.innerHTML = div.innerHTML+'<p>'+ $pd +'</p>';
	
	var pname = document.getElementById('text').value = $title;
}

function getEmail($em)
{
	var email = document.getElementById('rec').value = $em;
}

function resetContent()
{
	var div = document.getElementById('projContent');
	div.innerHTML = "";
	var header = document.getElementById('myModalLabel');
	header.innerHTML="";
}

function downlight( data, i, element ) {
		//tooltip.hideTooltip();
        d3.select(element)
		.style("stroke-width", "1")
		.style("stroke", "black");
}
