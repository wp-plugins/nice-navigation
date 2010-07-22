jQuery(function($) {

	var $nice_navigations = $("div.nice_navigation");

	// hide childs on load
	$nice_navigations.find("ul ul").hide();
	
	// open up onstart
	$nice_navigations.find(".current_page_ancestor,.current_page_parent,.current_page_item").find("ul:first").show();
	
	$nice_navigations.find("ul li").live("mousedown", function(e) {
		$target = $(e.target);
		if ($target.is("a")) {
			// click on link, don't do anything
			//console.log("is a");
		} else {
			//console.log("is NOT a");
			$this = $(this);
			if ($this.find("ul").length) {
				$this.find("ul:first").slideToggle("fast", function() {
					if ( $this.find("ul:first").is(":visible") ) {
						//console.log("visible");
						$this.removeClass("nice-navigation-deselected");
						$this.addClass("nice-navigation-selected");
					} else {
						//console.log("not visible");
						$this.removeClass("nice-navigation-selected");
						$this.addClass("nice-navigation-deselected");
					}
				});
				
			}
			return false;
		}

	});
});