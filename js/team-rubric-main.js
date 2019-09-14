if (document.querySelectorAll('.single-team')){

	let identity = document.getElementById('identity');
	let table = document.getElementById('table-holder');
	let gform_scores = document.getElementById('input_1_2');


	identity.addEventListener("change", idSet);


	function idSet(){
		let user = document.getElementById(identity.value);
		let parent = document.getElementById(identity.value).parentNode;
		if (document.querySelectorAll(".self")[0]){
			let oldSelf = document.querySelectorAll(".self")[0];
	   	 	oldSelf.classList.remove("self");
	   	 	let name = oldSelf.innerHTML;
	   	 	oldSelf.innerHTML = oldSelf.innerHTML.slice(0,(name.length-6));//remove self if name changes
		}

		user.innerHTML = user.innerHTML + ' (you)';
		parent.classList.add('self');
		document.getElementById('input_1_1').value = identity.value;//set gravity form student value
	}

	let scores = document.querySelectorAll('#team-rubric-table select')
	console.log(scores)

	scores.forEach((score) => {
	  score.addEventListener('change', () => {
	    gform_scores.value = scoreKeeper(scores) + teamMembers();//write all the scores in
	  });
	});


	function scoreKeeper(scores){
		let allScores = [];
		scores.forEach((score) => {
		  allScores.push(score.value)
		});
		return '['+allScores+']';
	}

	function teamMembers(){
		allMembers = [];
		let members = document.querySelectorAll('tr td:first-child');
		members.forEach((member) => {
		  allMembers.push("'"+member.innerHTML+"'")
		});
		return '|['+allMembers+']';

	}

	//#input_1_1 - student name
	//#input_1_2 - assessment


	jQuery(document).bind('gform_confirmation_loaded', function(event, formId){
	    // code to be trigger when confirmation page is loaded
	    console.log('form ok and submitted')
	   document.getElementById('rubric').classList.add('hidden');
	});
}