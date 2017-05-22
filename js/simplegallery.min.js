/*!
 * jQuery simple gallery Plugin 1.1.0
 *
 * http://fernandomoreiraweb.com/
 *
 * Copyright 2013 Fernando Moreira
 * Released under the MIT license:
 *   http://mit.fernandomoreiraweb.com/
 */
(function(c,b,a,d){c.fn.simplegallery=function(e){var g={galltime:300,gallcontent:".content",gallthumbnail:".thumbnail",gallthumb:".thumb"};
var f=c.extend({},g,e);return this.each(function(){c(f.gallthumb).click(function(){c(f.gallcontent).find("img").stop(true,true).fadeOut(f.galltime).hide();
var h=c(this).find("img").attr("id"),i=h.replace("thumb_","");c(".image_"+i+"").stop(true,true).fadeIn(f.galltime);return false;});});};})(jQuery,window,document);
