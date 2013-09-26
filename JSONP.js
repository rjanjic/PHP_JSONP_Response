/******************************************************************
* Projectname:   JS JSONP 
* Version:       1.0
* Author:        Radovan Janjic <rade@it-radionica.com>
* Last modified: 21 07 2013
* Copyright (C): 2013 IT-radionica.com, All Rights Reserved
*
* GNU General Public License (Version 2, June 1991)
*
* This program is free software; you can redistribute
* it and/or modify it under the terms of the GNU
* General Public License as published by the Free
* Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will
* be useful, but WITHOUT ANY WARRANTY; without even the
* implied warranty of MERCHANTABILITY or FITNESS FOR A
* PARTICULAR PURPOSE. See the GNU General Public License
* for more details.
* 
* Description:
* 
* JSONP or "JSON with padding" is a communication technique 
* used in JavaScript programs which run in Web browsers. 
* It provides a method to request data from a server in a different domain, 
* something prohibited by typical web browsers because of the same origin policy.
* Read more on: 
* http://en.wikipedia.org/wiki/JSONP
*
* Example:
* 
* // Make object instance 
* var e = new JSONP(
*   'http://localhost/example.php', 
*	function (bool) {
* 	if (bool) {
* 		alert('true');
* 	} else {
* 		alert('false');
* 	}
* });
* 
* // Call metod request
* e.request({param1: 1, param2: 'asdf', param3: {pp: 1, pp:2, pp: {ppp:1, ppp:2}}, param4: [1, 2, 3, 4]});
* 
******************************************************************/

JSONP = function(url, callback, callbackParam) {
	this.url = 'http://www.it-radionica.com/JSONP/captcha.php';
	this.callback = 'callbackFunctionName';
	this.callbackParam = 'callback';
	
	// ID
	this.jsID = 'JSONP_' + (1000000 + Math.floor((Math.random() * 1000000) + 1));
	
	// URL
	this.url =  url ? url : this.url;
	
	// Param
	this.callbackParam = callbackParam ? callbackParam : this.callbackParam;
	
	// Callback function
	if (typeof callback == 'function') {
		eval('window.FUNC_' + this.jsID + ' = ' + callback.toString());
		this.callback = 'FUNC_' + this.jsID;
	} else {
		this.callback = callback ? callback : this.callback;
	}
	
	// Remove the previous request 
	this.clear = function() {
		var JS = document.getElementById(this.jsID);
		if (JS) {
			JS.parentNode.removeChild(JS);
		}
	}
	
	// New JSONP request
	this.request = function(params, callback) {
		this.clear();
		if (typeof callback == 'function') {
			eval('window.FUNC_' + this.jsID + ' = ' + callback.toString());
			callback = 'FUNC_' + this.jsID;
		}
		
		var URL = this.url + '?' + this.callbackParam + '=' + (callback ? callback : this.callback) + '&' + this.serialize(params);
		var JS = document.createElement('script');
		JS.setAttribute('type', 'text/javascript');
		JS.setAttribute('src', URL);
		JS.setAttribute('id', this.jsID);
		if (typeof JS != 'undefined') {
			document.getElementsByTagName('head')[0].appendChild(JS);
		}
	}
	
	// Serialize request
	this.serialize = function(obj, prefix) {
		var s = [];
		for (var p in obj) {
			var k = prefix ? prefix + '[' + p + ']' : p, v = obj[p];
			s.push(typeof v == 'object' ? this.serialize(v, k) : encodeURIComponent(k) + '=' + encodeURIComponent(v));
		}
		return s.join('&');
	}
}