function activateA(cur) {
	var t = cur;
	if (t) {
		t=t.firstChild;
		if (t) {
			var pos = t.className.indexOf('firstlevellink');
			if (pos>=0) {
				t.className += ' hoveratag';
			}
		}
	}
}

function deActivateA(cur) {
	var t = cur;
	if (t) {
		t=t.firstChild;
		if (t) {
			var pos = t.className.indexOf('firstlevellink');
			if (pos>=0) {
				t.className=t.className.replace(" hoveratag", "");
			}
		}
	}
}

// IE only makes :hover work on LI tags
activateMenu = function(nav) {
	/* Get all the list items within the menu */
	var navroot = document.getElementById(nav);
	var lis=navroot.getElementsByTagName("LI");  
	for (i=0; i<lis.length; i++) {
		/* If the LI has another menu level */
		if(lis[i].lastChild.tagName=="UL"){
			/* assign the function to the LI */
			lis[i].onmouseover=function() {		
				/* display the inner menu */
				this.lastChild.style.display="block";
				activateA(this);
			}
			lis[i].onmouseout=function() {                       
				this.lastChild.style.display="none";
				deActivateA(this);
			}
		}
	}
}

window.onload= function(){
    /* pass the function the id of the top level UL */
    /* remove one, when only using one menu */
	activateMenu('yacbddm'); 
}