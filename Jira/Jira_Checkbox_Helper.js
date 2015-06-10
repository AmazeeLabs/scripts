// ==UserScript==
// @name Jira Checkbox Helper
// @description Helps selecting all billable checkboxes on the tempo board.
// 
// @include     http*
// @grant       none
// ==/UserScript== 

var iterator = 0;
var length = 0;

var $ = unsafeWindow.jQuery;

function markAll(){

	$('.content-container input[data-key=_billed_]:not(:checked)').slowEach( 400, function(){
		  $("span#all").html(length);
          $(this).click();
          $("span#current").html( parseInt(jQuery("span#current").html()) + 1 );
          
          if($("span#current").html() == length){
          	$("span#done").html("Done!");
          	$("#fancybox-wrap, #fancybox-overlay").delay(400).fadeOut(); 
          }
	});
}

function reset(){
	iterator = 0;
	length = 0;
}


$(document).ready(function() {
	
	if( $('div').hasClass('tempo-table') ){
	  $("th:contains('BILLED')").html('BILLED <input id="marker" type="button" value="Select all"/>');
		$('#marker').fancybox({
				'content'	:	'<div style="width:200px;"><span id="current">0</span> of <span id="all">0</span> Items selected.   <span id="done"></span></div>',		
				'showCloseButton'	: false,
				'hideOnOverlayClick'	: false,
				'onComplete'	: markAll,
				'onClosed'	: reset
			});
	}
	
});


$.fn.slowEach = function( interval, callback ) { 
	var array = this;
	length = array.length;
	if( ! array.length ){
		return; 
	}
	next(); 
	function next() { 

	    if( callback.call( array[iterator], iterator, array[iterator] ) !== false ) {
	        if( ++iterator < array.length ){
	            setTimeout( next, interval ); 

	         }
	    }
	} 
}; 

