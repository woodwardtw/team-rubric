let identity = document.getElementById('identity')

identity.addEventListener("change", idSet);


function idSet(){
	let user = document.getElementById(identity.value);
	if (document.querySelectorAll(".self")[0]){
		let oldSelf = document.querySelectorAll(".self")[0];
   	 	oldSelf.classList.remove("self");
   	 	let name = oldSelf.innerHTML;
   	 	oldSelf.innerHTML = oldSelf.innerHTML.slice(0,(name.length-7));
	}


	user.innerHTML = user.innerHTML + ' (self)';
	user.classList.add('self');

}
