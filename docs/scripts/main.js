require.config({
	paths : {
		jquery : 'jquery/jquery-1.7.1.min',
		text : 'text',
		css : 'css',
		'jquery.cookie' : 'jquery/jquery.cookie',
		'jquery-ui.custom' : 'jquery/jquery-ui.custom',
		'jquery.layout' : 'jquery/jquery.layout',
		'jquery.dynatree' : 'jquery/jquery.dynatree'
	},
	shim : {
		'jquery.cookie' : [ 'jquery' ],
		'jquery-ui.custom' : [ 'jquery' ],
		'jquery.layout' : [ 'jquery' ],
		'jquery.dynatree' : [ 'jquery', 'jquery-ui.custom' ]
	}
})

require([ "footnotes", "layout", 'jquery.cookie', 'css!../styles/custom', 'css!../styles/pygments' ],
		function(footnotes, layout) {
			layout.run( 4 );
			//footnotes.run();
});