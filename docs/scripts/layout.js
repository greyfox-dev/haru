define( ['jquery', 'tree', 'jquery.layout'], function( $, tree ) {
	function layoutWest( toclevels ) {
        if ( toclevels ) {
            tree.run( $('#toc'), toclevels );
        };
    };

    function layoutNorth() {
        var northHtml = '<div id="north"></div>';
        $('#header').before( northHtml );        
        $('#header h1').prependTo("#content");
    };
	
	function run( toclevels ) {
    	var myLayout;

        layoutWest( toclevels );
        layoutNorth();

        myLayout = $('body').layout({
            west__paneSelector : "#header",           
            center__paneSelector : "#content",            
        });
        myLayout.sizePane("west", "250");
	};
	
	return {
		run: run
	};
});